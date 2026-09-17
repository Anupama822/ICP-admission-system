<?php

namespace Tests\Feature;

use App\Models\AdmissionYear;
use App\Models\Course;
use App\Models\DocumentType;
use App\Models\Faculty;
use App\Models\Institute;
use App\Models\Student;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StudentCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private AdmissionYear $admissionYear;

    private Course $course;

    private DocumentType $documentType;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->seed(PermissionSeeder::class);

        $this->documentType = DocumentType::create(['name' => 'Academic']);
        Institute::create(['name' => 'Example School']);
        Institute::create(['name' => 'Sagarmatha Secondary School']);

        $this->admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
            'position' => 'Administrator',
        ]);

        $this->admissionYear = AdmissionYear::factory()->active()->create(['year' => '2026']);
        $this->course = Course::factory()->create();
    }

    private function staff(array $permissions = []): User
    {
        static $count = 0;
        $count++;

        $staff = User::create([
            'name' => 'Staff '.$count,
            'email' => "staff{$count}@example.test",
            'password' => Hash::make('password'),
            'role' => 'staff',
            'status' => 'active',
            'position' => 'Admissions Officer',
        ]);

        if ($permissions) {
            $staff->givePermissionTo($permissions);
        }

        return $staff;
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        return array_merge([
            'admission_year_id' => $this->admissionYear->id,
            'course_id' => $this->course->id,
            'highest_qualification' => [
                'document_type' => 'Academic',
                'faculty' => 'Science',
                'institute_name' => 'Example School',
            ],
            'first_name' => 'Krish',
            'middle_name' => '',
            'last_name' => 'Shai',
            'certificate_name' => 'Krish Shai',
            'gender' => 'male',
            'dob_bs' => '2062-03-05',
            'dob_ad' => '2005-06-19',
            'citizenship_number' => '46-01-78-00694',
            'declared_date' => '2025-01-02',
            'level' => '04',
            'entry_type' => 'Standard',
            'semester' => 'Spring',
            'permanent_address' => 'Pokhara-2, Kaski',
            'corresponding_address' => 'Pokhara-2, Kaski',
            'mobile' => '9814109890',
            'email_1' => 'krish@example.test',
            'father_full_name' => 'Anil Shai',
            'father_mobile' => '9801132234',
            'mother_full_name' => 'Asha Shai',
            'mother_mobile' => '9846166557',
            'has_disorder' => '0',
            'is_drug_abuser' => '0',
            'has_criminal_record' => '0',
            'has_communicable_disease' => '0',
            'is_minor_requiring_consent' => '0',
            'signature' => 'data:image/png;base64,'.base64_encode('fake-signature-bytes'),
        ], $overrides);
    }

    #[Test]
    public function index_renders_the_datatable_shell(): void
    {
        $this->actingAs($this->staff(['students.view']))
            ->get(route('admin.students.index'))
            ->assertOk()
            ->assertSee('students-table')
            ->assertSee('Add Student');
    }

    #[Test]
    public function create_edit_and_show_pages_render(): void
    {
        $student = Student::factory()->create(['admission_year_id' => $this->admissionYear->id, 'course_id' => $this->course->id]);
        $admin = $this->admin;

        $this->actingAs($admin)->get(route('admin.students.create'))
            ->assertOk()->assertSee('Create Student');

        $this->actingAs($admin)->get(route('admin.students.edit', $student))
            ->assertOk()->assertSee('Edit Student');

        $this->actingAs($admin)->get(route('admin.students.show', $student))
            ->assertOk()->assertSee($student->admission_id);
    }

    #[Test]
    public function admin_can_enroll_a_student_with_qualifications_and_documents(): void
    {
        $payload = $this->payload([
            'highest_qualification' => [
                'document_type' => 'Academic', 'awarded_year' => '2023', 'faculty' => 'Management', 'institute_name' => 'Sagarmatha Secondary School',
                'score' => '3.705', 'score_type' => 'CGPA', 'qualification_description' => 'NEB +2 (CGPA-3.705 Year 12 Eng-A)',
            ],
            'qualifications' => [
                [
                    'document_type' => 'Academic', 'awarded_year' => '2021', 'faculty' => 'SEE', 'institute_name' => 'Mount Annapurna School', 'score' => '3.80', 'score_type' => 'GPA',
                    'documents' => [
                        UploadedFile::fake()->image('see-marksheet.jpg'),
                        UploadedFile::fake()->image('see-certificate.jpg'),
                    ],
                ],
            ],
            'documents' => [
                ['title' => 'Citizenship', 'files' => [
                    UploadedFile::fake()->create('citizenship-front.pdf', 100, 'application/pdf'),
                    UploadedFile::fake()->create('citizenship-back.pdf', 100, 'application/pdf'),
                ]],
            ],
        ]);

        $this->actingAs($this->admin)
            ->post(route('admin.students.store'), $payload)
            ->assertRedirect(route('admin.students.index'));

        $student = Student::where('email_1', 'krish@example.test')->firstOrFail();

        $this->assertMatchesRegularExpression('/^2026\d{4}$/', $student->admission_id);
        $this->assertSame('C1', $student->group);
        $this->assertNotNull($student->signature_path);
        Storage::disk('public')->assertExists($student->signature_path);
        $this->assertCount(2, $student->qualifications);

        $highest = $student->qualifications->firstWhere('is_highest', true);
        $this->assertNotNull($highest);
        $this->assertSame('Management', $highest->faculty);
        $this->assertSame('NEB +2 (CGPA-3.705 Year 12 Eng-A)', $highest->qualification_description);

        $additional = $student->qualifications->firstWhere('is_highest', false);
        $this->assertSame('SEE', $additional->faculty);
        $this->assertSame('GPA', $additional->score_type);
        $this->assertCount(2, $additional->documents);
        Storage::disk('public')->assertExists($additional->documents->first()->file_path);

        $this->assertCount(2, $student->documents);
        Storage::disk('public')->assertExists($student->documents->first()->file_path);
    }

    #[Test]
    public function admin_can_add_multiple_qualification_descriptions_each_with_its_own_board(): void
    {
        $seeBoard = DocumentType::create(['name' => 'SEE']);

        $payload = $this->payload([
            'qualifications' => [
                [
                    'document_type' => $seeBoard->name,
                    'qualification_description' => 'SEE (Agg GPA-3.35 / English A, Maths B)',
                    'documents' => [UploadedFile::fake()->image('see.jpg')],
                ],
                [
                    'document_type' => $this->documentType->name,
                    'qualification_description' => 'NEB +2 (CGPA-3.70 Year 12 Eng-A)',
                ],
            ],
        ]);

        $this->actingAs($this->admin)
            ->post(route('admin.students.store'), $payload)
            ->assertRedirect(route('admin.students.index'));

        $student = Student::where('email_1', 'krish@example.test')->firstOrFail();
        $descriptions = $student->qualifications->where('is_highest', false);

        $this->assertCount(2, $descriptions);
        $see = $descriptions->firstWhere('document_type', 'SEE');
        $this->assertSame('SEE (Agg GPA-3.35 / English A, Maths B)', $see->qualification_description);
        $this->assertNull($see->faculty);
        $this->assertNull($see->institute_name);
        $this->assertCount(1, $see->documents);
    }

    #[Test]
    public function qualification_description_requires_an_educational_board(): void
    {
        $payload = $this->payload([
            'qualifications' => [
                ['qualification_description' => 'Missing a board'],
            ],
        ]);

        $this->actingAs($this->admin)
            ->post(route('admin.students.store'), $payload)
            ->assertSessionHasErrors('qualifications.0.document_type');
    }

    #[Test]
    public function admin_can_add_academic_qualification_records_with_document_type_forced_to_academic(): void
    {
        $faculty = Faculty::create(['name' => 'Management']);
        $institute = Institute::create(['name' => 'Prativa Secondary School, Pokhara-03, Nadipur, Nepal']);

        $payload = $this->payload([
            'academic_records' => [
                [
                    'document_type' => 'Whatever the client sends is ignored',
                    'awarded_year' => '2024',
                    'faculty' => $faculty->name,
                    'institute_name' => $institute->name,
                    'score' => 'CGPA-3.705',
                ],
            ],
        ]);

        $this->actingAs($this->admin)
            ->post(route('admin.students.store'), $payload)
            ->assertRedirect(route('admin.students.index'));

        $student = Student::where('email_1', 'krish@example.test')->firstOrFail();
        $record = $student->qualifications->firstWhere('is_record', true);

        $this->assertNotNull($record);
        $this->assertSame('Academic', $record->document_type);
        $this->assertSame('Management', $record->faculty);
        $this->assertSame('Prativa Secondary School, Pokhara-03, Nadipur, Nepal', $record->institute_name);
        $this->assertSame('CGPA-3.705', $record->score);
        $this->assertSame(2024, $record->awarded_year);
    }

    #[Test]
    public function academic_qualification_record_requires_a_registered_faculty(): void
    {
        $payload = $this->payload([
            'academic_records' => [
                ['faculty' => 'Not A Real Faculty', 'institute_name' => 'Some School'],
            ],
        ]);

        $this->actingAs($this->admin)
            ->post(route('admin.students.store'), $payload)
            ->assertSessionHasErrors('academic_records.0.faculty');
    }

    #[Test]
    public function two_enrollments_in_the_same_intake_year_get_sequential_ids(): void
    {
        $this->actingAs($this->admin)->post(route('admin.students.store'), $this->payload());
        $this->actingAs($this->admin)->post(route('admin.students.store'), $this->payload([
            'email_1' => 'second@example.test',
            'citizenship_number' => '46-01-78-11111',
        ]));

        $students = Student::orderBy('admission_id')->get();

        $this->assertCount(2, $students);
        $this->assertSame('20260001', $students[0]->admission_id);
        $this->assertSame('20260002', $students[1]->admission_id);
    }

    #[Test]
    public function the_thirty_first_enrollment_in_a_year_lands_in_the_next_group(): void
    {
        $enrollment = Student::nextEnrollment('2026');
        $this->assertSame(1, $enrollment['sequence']);
        $this->assertSame('C1', $enrollment['group']);

        Student::factory()->create(['admission_id' => '20260030']);
        $next = Student::nextEnrollment('2026');
        $this->assertSame(31, $next['sequence']);
        $this->assertSame('20260031', $next['admission_id']);
        $this->assertSame('C2', $next['group']);
    }

    #[Test]
    public function highest_qualification_only_needs_an_educational_board_and_awarding_body(): void
    {
        $payload = $this->payload([
            'highest_qualification' => [
                'document_type' => 'Academic',
                'institute_name' => 'Sagarmatha Secondary School',
            ],
        ]);

        $this->actingAs($this->admin)
            ->post(route('admin.students.store'), $payload)
            ->assertRedirect(route('admin.students.index'));

        $student = Student::where('email_1', 'krish@example.test')->firstOrFail();
        $highest = $student->qualifications->firstWhere('is_highest', true);

        $this->assertNotNull($highest);
        $this->assertSame('Sagarmatha Secondary School', $highest->institute_name);
        $this->assertNull($highest->faculty);
    }

    #[Test]
    public function admission_id_is_unique_at_the_database_level(): void
    {
        Student::factory()->create(['admission_id' => '20260001']);

        $this->expectException(\Illuminate\Database\QueryException::class);
        Student::factory()->create(['admission_id' => '20260001']);
    }

    #[Test]
    public function requires_at_least_one_of_citizenship_or_passport_number(): void
    {
        $payload = $this->payload(['citizenship_number' => null]);

        $this->actingAs($this->admin)
            ->post(route('admin.students.store'), $payload)
            ->assertSessionHasErrors('citizenship_number');
    }

    #[Test]
    public function highest_qualification_and_documents_survive_a_validation_error_redisplay(): void
    {
        $payload = $this->payload([
            'citizenship_number' => null,
            'passport_number' => null,
            'highest_qualification' => [
                'document_type' => 'Academic',
                'institute_name' => 'Sagarmatha Secondary School',
            ],
            'documents' => [
                ['title' => 'My Custom Document'],
            ],
        ]);

        $this->actingAs($this->admin)
            ->from(route('admin.students.create'))
            ->post(route('admin.students.store'), $payload)
            ->assertSessionHasErrors('citizenship_number')
            ->assertRedirect(route('admin.students.create'));

        $this->actingAs($this->admin)
            ->get(route('admin.students.create'))
            ->assertOk()
            ->assertSee('Sagarmatha Secondary School')
            ->assertSee('My Custom Document');
    }

    #[Test]
    public function admin_can_update_a_student_and_replace_qualifications(): void
    {
        $student = Student::factory()->create(['admission_year_id' => $this->admissionYear->id, 'course_id' => $this->course->id]);
        $student->qualifications()->create(['document_type' => 'Academic', 'faculty' => 'Old Faculty', 'institute_name' => 'Old Institute', 'sort_order' => 0]);

        $payload = $this->payload([
            'email_1' => $student->email_1,
            'citizenship_number' => $student->citizenship_number,
            'last_name' => 'Updated',
            'signature' => '',
            'qualifications' => [
                ['document_type' => 'Academic', 'faculty' => 'New Faculty', 'institute_name' => 'New Institute', 'awarded_year' => '2024'],
            ],
        ]);

        $this->actingAs($this->admin)
            ->put(route('admin.students.update', $student), $payload)
            ->assertRedirect(route('admin.students.index'));

        $student->refresh();
        $this->assertSame('Updated', $student->last_name);
        $this->assertCount(2, $student->qualifications);
        $this->assertSame('New Faculty', $student->qualifications->firstWhere('is_highest', false)->faculty);
        $this->assertSame('Science', $student->qualifications->firstWhere('is_highest', true)->faculty);
    }

    #[Test]
    public function admin_can_delete_a_student(): void
    {
        $student = Student::factory()->create(['admission_year_id' => $this->admissionYear->id, 'course_id' => $this->course->id]);

        $this->actingAs($this->admin)
            ->deleteJson(route('admin.students.destroy', $student))
            ->assertOk();

        $this->assertDatabaseMissing('students', ['id' => $student->id]);
    }

    #[Test]
    public function staff_without_any_permission_are_blocked_from_every_route(): void
    {
        $staff = $this->staff();
        $student = Student::factory()->create(['admission_year_id' => $this->admissionYear->id, 'course_id' => $this->course->id]);

        $this->actingAs($staff)->get(route('admin.students.index'))->assertForbidden();
        $this->actingAs($staff)->get(route('admin.students.create'))->assertForbidden();
        $this->actingAs($staff)->post(route('admin.students.store'), [])->assertForbidden();
        $this->actingAs($staff)->get(route('admin.students.show', $student))->assertForbidden();
        $this->actingAs($staff)->get(route('admin.students.edit', $student))->assertForbidden();
        $this->actingAs($staff)->put(route('admin.students.update', $student), [])->assertForbidden();
        $this->actingAs($staff)->delete(route('admin.students.destroy', $student))->assertForbidden();
        $this->actingAs($staff)->get(route('admin.students.export-pdf', $student))->assertForbidden();
        $this->actingAs($staff)->get(route('admin.students.export-csv'))->assertForbidden();
    }

    #[Test]
    public function staff_with_view_only_permission_can_list_and_view_but_nothing_else(): void
    {
        $staff = $this->staff(['students.view']);
        $student = Student::factory()->create(['admission_year_id' => $this->admissionYear->id, 'course_id' => $this->course->id]);

        $this->actingAs($staff)->get(route('admin.students.index'))->assertOk();
        $this->actingAs($staff)->get(route('admin.students.show', $student))->assertOk();
        $this->actingAs($staff)->get(route('admin.students.create'))->assertForbidden();
        $this->actingAs($staff)->delete(route('admin.students.destroy', $student))->assertForbidden();
    }

    #[Test]
    public function admin_always_passes_regardless_of_granted_permissions(): void
    {
        $student = Student::factory()->create(['admission_year_id' => $this->admissionYear->id, 'course_id' => $this->course->id]);

        $this->actingAs($this->admin)->get(route('admin.students.index'))->assertOk();
        $this->actingAs($this->admin)->get(route('admin.students.export-csv'))->assertOk();
        $this->actingAs($this->admin)->get(route('admin.students.export-pdf', $student))->assertOk();
    }

    #[Test]
    public function export_csv_returns_exactly_the_required_columns_in_order(): void
    {
        Student::factory()->create([
            'admission_year_id' => $this->admissionYear->id,
            'course_id' => $this->course->id,
            'admission_id' => '20260001',
            'group' => 'C1',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.students.export-csv'));

        $response->assertOk();
        $content = $response->streamedContent();
        $firstLine = strtok($content, "\n");
        $headers = str_getcsv(trim($firstLine));

        $this->assertSame([
            'Full Name', 'LMU', 'Student ID', 'Level', 'Group', 'Biometric',
            'Date of Birth', 'Gender', 'Email Address', 'Contact',
            "Father's Name", "Father's Contact", "Mother's Name", "Mother's Contact",
            'Local Guardian', 'Local Guardian Contact', 'Course', 'Intake Year',
        ], $headers);
    }

    #[Test]
    public function export_pdf_streams_a_pdf_document(): void
    {
        $student = Student::factory()->create([
            'admission_year_id' => $this->admissionYear->id,
            'course_id' => $this->course->id,
            'admission_id' => '20260005',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.students.export-pdf', $student));

        $response->assertOk();
        $this->assertStringContainsString('pdf', (string) $response->headers->get('content-type'));
    }
}
