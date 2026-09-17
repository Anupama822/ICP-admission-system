<?php

namespace App\Http\Controllers;

use App\DataTables\StudentDataTable;
use App\Http\Requests\StudentRequest;
use App\Models\AdmissionYear;
use App\Models\Course;
use App\Models\DocumentType;
use App\Models\Faculty;
use App\Models\Institute;
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

    public function edit(Student $student)
    {
        $student->load(['intake', 'qualifications.documents', 'documents']);

        return view($this->editResource(), $this->crudInfo() + ['item' => $student] + $this->formOptions($student) + ['wide' => true]);
    }

    public function store(StudentRequest $request)
    {
        $admissionYear = AdmissionYear::active()->firstOrFail();

        $attributes = $request->safe()->except(['photo', 'signature', 'qualifications', 'documents']);
        $attributes['admission_year_id'] = $admissionYear->id;
        // Semester is not chosen on the form; it always matches the intake
        // (Spring/Autumn) of the admission year the student enrolled under.
        $attributes['semester'] = $admissionYear->intake;
        $attributes['declared_date'] = now();
        // Not collected on the form; the certificate is issued in the
        // student's full legal name.
        $attributes['certificate_name'] = $this->fullNameFromAttributes($attributes);

        $student = $this->createWithUniqueAdmissionId($attributes, $admissionYear);

        $this->applyPhoto($student, $request->file('photo'));
        $this->applySignature($student, $request->input('signature'));
        $student->save();

        [$qualificationRows, $qualificationFiles] = $this->mergeQualificationRows($request);
        $this->syncQualifications($student, $qualificationRows, $qualificationFiles);
        $this->appendDocuments($student, $request->input('documents', []), $request->file('documents', []));

        return $this->gotoCrudIndex()
            ->with('status', "Student {$student->fullName()} enrolled successfully. Admission ID: {$student->admission_id}.");
    }

    public function show(Student $student)
    {
        $student->load(['course', 'intake', 'qualifications.documents', 'documents']);

        return view($this->showResource(), $this->crudInfo() + [
            'item' => $student,
            'hideEdit' => ! request()->user()->can('students.edit'),
        ]);
    }

    public function update(StudentRequest $request, Student $student)
    {
        // Admission year, semester and declared date are fixed at enrollment
        // time and are not editable afterwards.
        $attributes = $request->safe()->except(['photo', 'signature', 'qualifications', 'documents']);
        // Not collected on the form; the certificate is issued in the
        // student's full legal name.
        $attributes['certificate_name'] = $this->fullNameFromAttributes($attributes);

        $student->update($attributes);

        $this->applyPhoto($student, $request->file('photo'));
        if ($request->filled('signature')) {
            $this->applySignature($student, $request->input('signature'));
        }
        $student->save();

        [$qualificationRows, $qualificationFiles] = $this->mergeQualificationRows($request);
        $this->syncQualifications($student, $qualificationRows, $qualificationFiles);
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
            foreach ($qualification->documents as $document) {
                Storage::disk('public')->delete($document->file_path);
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
        $student->load(['course', 'intake', 'qualifications.documents', 'documents']);

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
     * Dropdown data plus the values the Course & Intake step should show
     * pre-selected: the active admission year (or the student's own, when
     * editing), the course/level carried over from a failed submission or
     * the record on file, and the fixed list of entry types.
     *
     * @return array<string, mixed>
     */
    private function formOptions(?Student $student = null): array
    {
        $admissionYears = AdmissionYear::orderByDesc('year')->get();
        $courses = Course::orderBy('title')->get();

        $activeAdmissionYear = $admissionYears->firstWhere('is_active', true);
        $selectedAdmissionYearId = $student?->admission_year_id ?? $activeAdmissionYear?->id;
        $selectedAdmissionYear = $student?->intake ?? $admissionYears->firstWhere('id', $selectedAdmissionYearId);

        $selectedCourseId = old('course_id', $student?->course_id);
        $selectedCourse = $courses->firstWhere('id', $selectedCourseId);

        return [
            'admissionYears' => $admissionYears,
            'courses' => $courses,
            'selectedAdmissionYearId' => $selectedAdmissionYearId,
            'selectedAdmissionYear' => $selectedAdmissionYear,
            'selectedCourseId' => $selectedCourseId,
            'selectedCourse' => $selectedCourse,
            'selectedLevel' => old('level', $student?->level),
            'entryTypeOptions' => Student::ENTRY_TYPES,
            'documentTypes' => DocumentType::orderBy('name')->get(),
            'documentTypeFormatHints' => DocumentType::orderBy('name')->pluck('format_hint', 'name'),
            'faculties' => Faculty::orderBy('name')->get(),
            'institutes' => Institute::orderBy('name')->get(),
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

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function fullNameFromAttributes(array $attributes): string
    {
        return trim(collect([
            $attributes['first_name'] ?? null,
            $attributes['middle_name'] ?? null,
            $attributes['last_name'] ?? null,
        ])->filter()->implode(' '));
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
     * The Academic step submits three kinds of rows that all live in the
     * same `qualifications` table: the mandatory "highest qualification"
     * (flagged `is_highest`), the optional "Qualification Description"
     * entries, and the optional "Academic Qualifications" records (flagged
     * `is_record`, with Document Type forced to "Academic" regardless of
     * what was submitted). Combine them into one ordered set before syncing.
     *
     * @return array{0: array<int, array<string, mixed>>, 1: array<int, array<string, mixed>>}
     */
    private function mergeQualificationRows(StudentRequest $request): array
    {
        $highestRow = $request->input('highest_qualification', []);
        $highestRow['is_highest'] = true;
        $highestFiles = $request->file('highest_qualification', []);

        $recordRows = array_map(function (array $row) {
            $row['document_type'] = 'Academic';
            $row['is_record'] = true;

            return $row;
        }, $request->input('academic_records', []));
        $recordFiles = $request->file('academic_records', []);

        $rows = array_merge([$highestRow], $request->input('qualifications', []), $recordRows);
        $files = array_merge([$highestFiles], $request->file('qualifications', []), $recordFiles);

        return [$rows, $files];
    }

    /**
     * Replace-all: qualifications have no identity worth preserving beyond
     * their content, so the submitted set becomes the new complete set.
     * Each row's previously-uploaded documents are kept via hidden
     * `existing_documents[]` fields (dropped client-side if the user removes
     * one) and newly-uploaded images are appended to them.
     */
    private function syncQualifications(Student $student, array $rows, array $files): void
    {
        $rows = array_values($rows);

        $student->loadMissing('qualifications.documents');

        // original_filename isn't resubmitted for kept documents, so look it
        // up from what's already on file before anything is deleted.
        $originalFilenames = $student->qualifications
            ->flatMap(fn ($qualification) => $qualification->documents)
            ->pluck('original_filename', 'file_path');

        // Build each row's full document set (kept + newly uploaded) exactly
        // once before touching any existing records.
        $documents = [];
        foreach ($rows as $index => $row) {
            $kept = array_values(array_filter((array) ($row['existing_documents'] ?? [])));

            $uploaded = [];
            foreach ($files[$index]['documents'] ?? [] as $file) {
                if ($file instanceof UploadedFile) {
                    $path = $file->store('students/qualifications', 'public');
                    $uploaded[$path] = $file->getClientOriginalName();
                }
            }

            $documents[$index] = collect($kept)
                ->mapWithKeys(fn (string $path) => [$path => $originalFilenames->get($path, basename($path))])
                ->merge($uploaded);
        }

        $keepPaths = collect($documents)->flatMap(fn ($paths) => $paths->keys())->all();

        foreach ($student->qualifications as $existing) {
            foreach ($existing->documents as $document) {
                if (! in_array($document->file_path, $keepPaths, true)) {
                    Storage::disk('public')->delete($document->file_path);
                }
            }
        }

        $student->qualifications()->delete();

        foreach ($rows as $index => $row) {
            $qualification = $student->qualifications()->create([
                'document_type' => $row['document_type'],
                'awarded_year' => $row['awarded_year'] ?? null,
                'faculty' => $row['faculty'] ?? null,
                'institute_name' => $row['institute_name'] ?? null,
                'score' => $row['score'] ?? null,
                'score_type' => $row['score_type'] ?? null,
                'qualification_description' => $row['qualification_description'] ?? null,
                'is_highest' => $row['is_highest'] ?? false,
                'is_record' => $row['is_record'] ?? false,
                'sort_order' => $index,
            ]);

            foreach ($documents[$index] as $path => $originalFilename) {
                $qualification->documents()->create([
                    'file_path' => $path,
                    'original_filename' => $originalFilename,
                ]);
            }
        }
    }

    /**
     * Documents are pure attachments: new ones are appended, existing ones
     * are only ever removed individually via destroyDocument().
     */
    private function appendDocuments(Student $student, array $rows, array $files): void
    {
        foreach (array_values($rows) as $index => $row) {
            foreach ($files[$index]['files'] ?? [] as $file) {
                if (! $file instanceof UploadedFile) {
                    continue;
                }

                $student->documents()->create([
                    'title' => $row['title'],
                    'file_path' => $file->store('students/documents', 'public'),
                    'original_filename' => $file->getClientOriginalName(),
                ]);
            }
        }
    }
}
