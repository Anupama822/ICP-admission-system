<?php

namespace App\Http\Controllers;

use App\DataTables\StudentDataTable;
use App\Http\Requests\StudentRequest;
use App\Models\AdmissionYear;
use App\Models\Course;
use App\Models\Student;
use App\Models\StudentDocument;
use Dompdf\Dompdf;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StudentController extends BaseController
{
    public function __construct()
    {
        $this->title = 'Student';
        $this->subTitle = 'Student Enrollment';
        $this->resources = 'admin.students.';
        $this->icon = 'heroicon-o-academic-cap';
        $this->route = 'admin.students.';
        $this->description = 'Enroll and manage students against an admission year (intake) and course.';
        parent::__construct();
    }

    public function index(StudentDataTable $dataTable)
    {
        return $dataTable->render($this->indexResource(), $this->crudInfo() + [
            'add_button_name' => 'Add Student',
        ]);
    }

    public function create()
    {
        return view($this->createResource(), $this->crudInfo() + $this->formOptions() + ['wide' => true]);
    }

    public function store(StudentRequest $request)
    {
        $admissionYear = AdmissionYear::findOrFail($request->validated('admission_year_id'));

        $attributes = $request->safe()->except(['photo', 'signature', 'qualifications', 'documents']);

        $student = $this->createWithUniqueAdmissionId($attributes, $admissionYear);

        $this->applyPhoto($student, $request->file('photo'));
        $this->applySignature($student, $request->input('signature'));
        $student->save();

        $this->syncQualifications($student, $request->input('qualifications', []), $request->file('qualifications', []));
        $this->appendDocuments($student, $request->input('documents', []), $request->file('documents', []));

        return $this->gotoCrudIndex()
            ->with('status', "Student {$student->fullName()} enrolled successfully. Admission ID: {$student->admission_id}.");
    }

    public function show(Student $student)
    {
        $student->load(['course', 'intake', 'qualifications', 'documents']);

        return view($this->showResource(), $this->crudInfo() + [
            'item' => $student,
            'hideEdit' => ! request()->user()->can('students.edit'),
        ]);
    }

    public function edit(Student $student)
    {
        $student->load(['qualifications', 'documents']);

        return view($this->editResource(), $this->crudInfo() + ['item' => $student] + $this->formOptions() + ['wide' => true]);
    }

    public function update(StudentRequest $request, Student $student)
    {
        $student->update($request->safe()->except(['photo', 'signature', 'qualifications', 'documents']));

        $this->applyPhoto($student, $request->file('photo'));
        if ($request->filled('signature')) {
            $this->applySignature($student, $request->input('signature'));
        }
        $student->save();

        $this->syncQualifications($student, $request->input('qualifications', []), $request->file('qualifications', []));
        $this->appendDocuments($student, $request->input('documents', []), $request->file('documents', []));

        return $this->gotoCrudIndex()
            ->with('status', "Student {$student->fullName()} updated successfully.");
    }

    public function destroy(Request $request, Student $student)
    {
        $name = $student->fullName();

        foreach ([$student->photo_path, $student->signature_path] as $path) {
            if ($path) {
                Storage::disk('public')->delete($path);
            }
        }
        foreach ($student->qualifications as $qualification) {
            if ($qualification->document_path) {
                Storage::disk('public')->delete($qualification->document_path);
            }
        }
        foreach ($student->documents as $document) {
            Storage::disk('public')->delete($document->file_path);
        }

        $student->delete();

        return $request->expectsJson()
            ? $this->returnSuccess(['message' => "Student {$name} deleted."])
            : $this->gotoCrudIndex()->with('status', "Student {$name} deleted.");
    }

    /**
     * Remove a single previously-uploaded document without touching the
     * rest of the enrollment record.
     */
    public function destroyDocument(Request $request, Student $student, StudentDocument $document)
    {
        abort_if($document->student_id !== $student->getKey(), 404);

        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        return $request->expectsJson()
            ? $this->returnSuccess(['message' => 'Document deleted.'])
            : redirect()->back()->with('status', 'Document deleted.');
    }

    public function exportCsv()
    {
        $headers = [
            'Full Name', 'LMU', 'Student ID', 'Level', 'Group', 'Biometric',
            'Date of Birth', 'Gender', 'Email Address', 'Contact',
            "Father's Name", "Father's Contact", "Mother's Name", "Mother's Contact",
            'Local Guardian', 'Local Guardian Contact', 'Course', 'Intake Year',
        ];

        return response()->streamDownload(function () use ($headers) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers);

            Student::query()->with(['course', 'intake'])->orderBy('admission_id')
                ->chunk(200, function ($students) use ($handle) {
                    foreach ($students as $student) {
                        fputcsv($handle, [
                            $student->fullName(),
                            $student->university_registration_no,
                            $student->admission_id,
                            $student->level,
                            $student->group,
                            $student->biometric_id ?: $student->admission_id,
                            optional($student->dob_ad)->format('Y-m-d'),
                            ucfirst((string) $student->gender),
                            $student->email_1,
                            $student->mobile,
                            $student->father_full_name,
                            $student->father_mobile,
                            $student->mother_full_name,
                            $student->mother_mobile,
                            $student->guardian_full_name,
                            $student->guardian_contact,
                            $student->course?->title,
                            $student->intake?->year,
                        ]);
                    }
                });

            fclose($handle);
        }, 'students_'.date('Y-m-d_His').'.csv', ['Content-Type' => 'text/csv']);
    }

    public function exportPdf(Student $student)
    {
        $student->load(['course', 'intake', 'qualifications']);

        $html = view('admin.students.exports.student-pdf', ['student' => $student])->render();

        $dompdf = new Dompdf(['isRemoteEnabled' => false]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('a4');
        $dompdf->render();

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$student->admission_id.'.pdf"',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function formOptions(): array
    {
        return [
            'admissionYears' => AdmissionYear::orderByDesc('year')->get(),
            'courses' => Course::orderBy('title')->get(),
        ];
    }

    /**
     * Reserve an admission ID + group inside a transaction and create the
     * student, retrying if a concurrent enrollment claimed the same number
     * first (the unique index on admission_id is the real safety net; the
     * locked read in Student::nextEnrollment() only prevents collisions
     * when a row for that year already exists to lock).
     */
    private function createWithUniqueAdmissionId(array $attributes, AdmissionYear $admissionYear, int $attempts = 3): Student
    {
        for ($i = 1; $i <= $attempts; $i++) {
            try {
                return DB::transaction(function () use ($attributes, $admissionYear) {
                    $enrollment = Student::nextEnrollment($admissionYear->year);

                    return Student::create($attributes + [
                        'admission_id' => $enrollment['admission_id'],
                        'group' => $enrollment['group'],
                    ]);
                });
            } catch (QueryException $e) {
                if ($i === $attempts || ! $this->isUniqueConstraintViolation($e)) {
                    throw $e;
                }
            }
        }

        throw new \RuntimeException('Could not reserve a unique admission ID.');
    }

    private function isUniqueConstraintViolation(QueryException $e): bool
    {
        $message = strtolower($e->getMessage());

        return ($e->errorInfo[1] ?? null) == 1062
            || str_contains($message, 'unique constraint')
            || str_contains($message, 'duplicate');
    }

    private function applyPhoto(Student $student, ?UploadedFile $photo): void
    {
        if (! $photo) {
            return;
        }

        if ($student->photo_path) {
            Storage::disk('public')->delete($student->photo_path);
        }

        $filename = Str::of($student->admission_id.' '.$student->fullName())
            ->replaceMatches('/[^A-Za-z0-9 _-]/', '')
            ->trim()
            ->append('.jpg');

        $student->photo_path = $photo->storeAs('students/photos', (string) $filename, 'public');
    }

    private function applySignature(Student $student, ?string $dataUrl): void
    {
        if (! $dataUrl || ! str_contains($dataUrl, ',')) {
            return;
        }

        if ($student->signature_path) {
            Storage::disk('public')->delete($student->signature_path);
        }

        [, $encoded] = explode(',', $dataUrl, 2);
        $binary = base64_decode($encoded);

        $path = 'students/signatures/'.Str::uuid().'.png';
        Storage::disk('public')->put($path, $binary);

        $student->signature_path = $path;
    }

    /**
     * Replace-all: qualifications have no identity worth preserving beyond
     * their content, so the submitted set becomes the new complete set.
     * Existing attachments are kept via a hidden `existing_document_path`
     * field unless a fresh file was uploaded for that row.
     */
    private function syncQualifications(Student $student, array $rows, array $files): void
    {
        $rows = array_values($rows);

        // Store (or carry forward) each row's document exactly once before
        // touching any existing records.
        $documentPaths = [];
        foreach ($rows as $index => $row) {
            if (isset($files[$index]['document']) && $files[$index]['document'] instanceof UploadedFile) {
                $documentPaths[$index] = $files[$index]['document']->store('students/qualifications', 'public');
            } else {
                $documentPaths[$index] = $row['existing_document_path'] ?? null;
            }
        }

        $keepPaths = array_filter($documentPaths);

        foreach ($student->qualifications as $existing) {
            if ($existing->document_path && ! in_array($existing->document_path, $keepPaths, true)) {
                Storage::disk('public')->delete($existing->document_path);
            }
        }

        $student->qualifications()->delete();

        foreach ($rows as $index => $row) {
            $student->qualifications()->create([
                'document_type' => $row['document_type'],
                'awarded_year' => $row['awarded_year'] ?? null,
                'subject' => $row['subject'],
                'institute_name' => $row['institute_name'],
                'score' => $row['score'] ?? null,
                'document_path' => $documentPaths[$index],
                'sort_order' => $index,
            ]);
        }
    }

    /**
     * Documents are pure attachments: new ones are appended, existing ones
     * are only ever removed individually via destroyDocument().
     */
    private function appendDocuments(Student $student, array $rows, array $files): void
    {
        foreach (array_values($rows) as $index => $row) {
            if (! isset($files[$index]['file']) || ! $files[$index]['file'] instanceof UploadedFile) {
                continue;
            }

            $file = $files[$index]['file'];

            $student->documents()->create([
                'title' => $row['title'],
                'file_path' => $file->store('students/documents', 'public'),
                'original_filename' => $file->getClientOriginalName(),
            ]);
        }
    }
}
