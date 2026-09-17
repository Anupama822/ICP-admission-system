<?php

namespace Tests\Feature;

use App\Models\Institute;
use App\Models\Student;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class InstituteCrudTest extends TestCase
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
    public function index_lists_institutes(): void
    {
        Institute::create(['name' => 'Informatics College Pokhara']);

        $this->actingAs($this->admin)
            ->get(route('admin.institutes.index'))
            ->assertOk()
            ->assertSee('Informatics College Pokhara');
    }

    #[Test]
    public function admin_can_add_an_institute_inline_via_ajax(): void
    {
        $response = $this->actingAs($this->admin)
            ->postJson(route('admin.institutes.store'), ['name' => 'Prativa Secondary School'])
            ->assertOk();

        $this->assertSame('Prativa Secondary School', $response->json('data.name'));
        $this->assertDatabaseHas('institutes', ['name' => 'Prativa Secondary School']);
    }

    #[Test]
    public function staff_permitted_to_create_students_can_add_an_institute_from_the_enrollment_form(): void
    {
        $staff = $this->staff(['students.create']);

        $this->actingAs($staff)
            ->postJson(route('admin.institutes.store'), ['name' => 'Mount Annapurna School'])
            ->assertOk();

        $this->assertDatabaseHas('institutes', ['name' => 'Mount Annapurna School']);
    }

    #[Test]
    public function staff_without_student_permissions_cannot_add_an_institute(): void
    {
        $staff = $this->staff();

        $this->actingAs($staff)
            ->postJson(route('admin.institutes.store'), ['name' => 'Mount Annapurna School'])
            ->assertForbidden();
    }

    #[Test]
    public function institute_names_must_be_unique(): void
    {
        Institute::create(['name' => 'Prativa Secondary School']);

        $this->actingAs($this->admin)
            ->post(route('admin.institutes.store'), ['name' => 'Prativa Secondary School'])
            ->assertSessionHasErrors('name');
    }

    #[Test]
    public function admin_can_rename_an_institute_inline(): void
    {
        $institute = Institute::create(['name' => 'Prativa Secondary School']);

        $this->actingAs($this->admin)
            ->patchJson(route('admin.institutes.inline-update', $institute), ['value' => 'Prativa Secondary School, Pokhara'])
            ->assertOk()
            ->assertJsonPath('data.value', 'Prativa Secondary School, Pokhara');

        $this->assertSame('Prativa Secondary School, Pokhara', $institute->fresh()->name);
    }

    #[Test]
    public function admin_can_delete_an_unused_institute(): void
    {
        $institute = Institute::create(['name' => 'Prativa Secondary School']);

        $this->actingAs($this->admin)
            ->delete(route('admin.institutes.destroy', $institute))
            ->assertRedirect(route('admin.institutes.index'));

        $this->assertDatabaseMissing('institutes', ['id' => $institute->id]);
    }

    #[Test]
    public function admin_cannot_delete_an_institute_used_by_a_qualification(): void
    {
        $institute = Institute::create(['name' => 'Prativa Secondary School']);
        Student::factory()->create()->qualifications()->create([
            'document_type' => 'Academic', 'institute_name' => 'Prativa Secondary School', 'is_highest' => true, 'sort_order' => 0,
        ]);

        $this->actingAs($this->admin)
            ->deleteJson(route('admin.institutes.destroy', $institute))
            ->assertStatus(422);

        $this->assertDatabaseHas('institutes', ['id' => $institute->id]);
    }

    #[Test]
    public function staff_cannot_reach_the_institute_management_page(): void
    {
        $staff = $this->staff();
        $institute = Institute::create(['name' => 'Prativa Secondary School']);

        // EnsureRole bounces a non-admin back to their own dashboard.
        $this->actingAs($staff)->get(route('admin.institutes.index'))->assertRedirect(route('staff.dashboard'));
        $this->actingAs($staff)->patch(route('admin.institutes.inline-update', $institute), [])->assertRedirect(route('staff.dashboard'));
        $this->actingAs($staff)->delete(route('admin.institutes.destroy', $institute))->assertRedirect(route('staff.dashboard'));
    }
}
