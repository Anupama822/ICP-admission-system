<?php

namespace Tests\Feature;

use App\Models\AdmissionYear;
use App\Models\Course;
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

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->seed(PermissionSeeder::class);

        $this->admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
            'position' => 'Administrator',
        ]);

        $this->admissionYear = AdmissionYear::factory()->create(['year' => '2026']);
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
            'highest_qualification' => 'NEB',
            'awarding_body' => 'Sagarmatha Secondary School',
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
            'qualifications' => [
                ['document_type' => 'Academic', 'awarded_year' => '2021', 'subject' => 'SEE', 'institute_name' => 'Mount Annapurna School', 'score' => 'GPA-3.80'],
                ['document_type' => 'Academic', 'awarded_year' => '2023', 'subject' => 'Management', 'institute_name' => 'Sagarmatha Secondary School', 'score' => 'CGPA-3.19'],
            ],
            'documents' => [
                ['title' => 'Citizenship', 'file' => UploadedFile::fake()->create('citizenship.pdf', 100, 'application/pdf')],
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
        $this->assertCount(1, $student->documents);
        Storage::disk('public')->assertExists($student->documents->first()->file_path);
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
    public function admin_can_update_a_student_and_replace_qualifications(): void
    {
        $student = Student::factory()->create(['admission_year_id' => $this->admissionYear->id, 'course_id' => $this->course->id]);
        $student->qualifications()->create(['document_type' => 'Academic', 'subject' => 'Old Subject', 'institute_name' => 'Old Institute', 'sort_order' => 0]);

        $payload = $this->payload([
            'email_1' => $student->email_1,
            'citizenship_number' => $student->citizenship_number,
            'last_name' => 'Updated',
            'signature' => '',
            'qualifications' => [
                ['document_type' => 'Academic', 'subject' => 'New Subject', 'institute_name' => 'New Institute', 'awarded_year' => '2024'],
            ],
        ]);

        $this->actingAs($this->admin)
            ->put(route('admin.students.update', $student), $payload)
            ->assertRedirect(route('admin.students.index'));

        $student->refresh();
        $this->assertSame('Updated', $student->last_name);
        $this->assertCount(1, $student->qualifications);
        $this->assertSame('New Subject', $student->qualifications->first()->subject);
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
