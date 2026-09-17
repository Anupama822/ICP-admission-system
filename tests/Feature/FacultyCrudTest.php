<?php

namespace Tests\Feature;

use App\Models\Faculty;
use App\Models\Student;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FacultyCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionSeeder::class);

        $this->admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
            'position' => 'Administrator',
        ]);
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

    #[Test]
    public function index_lists_faculties(): void
    {
        Faculty::create(['name' => 'Management']);

        $this->actingAs($this->admin)
            ->get(route('admin.faculties.index'))
            ->assertOk()
            ->assertSee('Management');
    }

    #[Test]
    public function admin_can_add_a_faculty_inline_via_ajax(): void
    {
        $response = $this->actingAs($this->admin)
            ->postJson(route('admin.faculties.store'), ['name' => 'Science'])
            ->assertOk();

        $this->assertSame('Science', $response->json('data.name'));
        $this->assertDatabaseHas('faculties', ['name' => 'Science']);
    }

    #[Test]
    public function staff_permitted_to_create_students_can_add_a_faculty_from_the_enrollment_form(): void
    {
        $staff = $this->staff(['students.create']);

        $this->actingAs($staff)
            ->postJson(route('admin.faculties.store'), ['name' => 'Humanities'])
            ->assertOk();

        $this->assertDatabaseHas('faculties', ['name' => 'Humanities']);
    }

    #[Test]
    public function staff_without_student_permissions_cannot_add_a_faculty(): void
    {
        $staff = $this->staff();

        $this->actingAs($staff)
            ->postJson(route('admin.faculties.store'), ['name' => 'Humanities'])
            ->assertForbidden();
    }

    #[Test]
    public function faculty_names_must_be_unique(): void
    {
        Faculty::create(['name' => 'Science']);

        $this->actingAs($this->admin)
            ->post(route('admin.faculties.store'), ['name' => 'Science'])
            ->assertSessionHasErrors('name');
    }

    #[Test]
    public function admin_can_rename_a_faculty_inline(): void
    {
        $faculty = Faculty::create(['name' => 'Science']);

        $this->actingAs($this->admin)
            ->patchJson(route('admin.faculties.inline-update', $faculty), ['value' => 'Natural Science'])
            ->assertOk()
            ->assertJsonPath('data.value', 'Natural Science');

        $this->assertSame('Natural Science', $faculty->fresh()->name);
    }

    #[Test]
    public function admin_can_delete_an_unused_faculty(): void
    {
        $faculty = Faculty::create(['name' => 'Science']);

        $this->actingAs($this->admin)
            ->delete(route('admin.faculties.destroy', $faculty))
            ->assertRedirect(route('admin.faculties.index'));

        $this->assertDatabaseMissing('faculties', ['id' => $faculty->id]);
    }

    #[Test]
    public function admin_cannot_delete_a_faculty_used_by_a_qualification(): void
    {
        $faculty = Faculty::create(['name' => 'Science']);
        Student::factory()->create()->qualifications()->create(['document_type' => 'Academic', 'faculty' => 'Science', 'is_record' => true, 'sort_order' => 0]);

        $this->actingAs($this->admin)
            ->deleteJson(route('admin.faculties.destroy', $faculty))
            ->assertStatus(422);

        $this->assertDatabaseHas('faculties', ['id' => $faculty->id]);
    }

    #[Test]
    public function staff_cannot_reach_the_faculty_management_page(): void
    {
        $staff = $this->staff();
        $faculty = Faculty::create(['name' => 'Science']);

        // EnsureRole bounces a non-admin back to their own dashboard.
        $this->actingAs($staff)->get(route('admin.faculties.index'))->assertRedirect(route('staff.dashboard'));
        $this->actingAs($staff)->patch(route('admin.faculties.inline-update', $faculty), [])->assertRedirect(route('staff.dashboard'));
        $this->actingAs($staff)->delete(route('admin.faculties.destroy', $faculty))->assertRedirect(route('staff.dashboard'));
    }
}
