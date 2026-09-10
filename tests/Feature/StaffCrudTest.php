<?php

namespace Tests\Feature;

use App\Models\AdmissionYear;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StaffCrudTest extends TestCase
{
    use RefreshDatabase;

    /** Headers jQuery DataTables sends with its ajax draw requests. */
    private const AJAX = ['X-Requested-With' => 'XMLHttpRequest'];

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

        AdmissionYear::factory()->active()->create();
    }

    #[Test]
    public function index_renders_the_datatable_shell(): void
    {
        User::factory()->create(['name' => 'Aarav Shrestha']);

        $this->actingAs($this->admin)
            ->get(route('admin.staff.index'))
            ->assertOk()
            ->assertSee('staff-table')
            ->assertSee('Add Staff');
    }

    #[Test]
    public function datatable_ajax_matches_on_name_and_email(): void
    {
        User::factory()->create(['name' => 'Aarav Shrestha', 'email' => 'aarav@example.test']);
        User::factory()->create(['name' => 'Suman Gurung', 'email' => 'suman@example.test']);

        $response = $this->actingAs($this->admin)->getJson(route('admin.staff.index', [
            'draw' => 1,
            'start' => 0,
            'length' => 10,
            'search' => ['value' => 'suman@example.test', 'regex' => 'false'],
            'columns' => $this->datatableColumns(),
        ]), self::AJAX);

        $response->assertOk()
            ->assertJsonPath('recordsTotal', 2)
            ->assertJsonPath('recordsFiltered', 1);
    }

    #[Test]
    public function datatable_search_understands_the_status_column(): void
    {
        User::factory()->inactive()->create(['name' => 'Suman Gurung']);
        User::factory()->create(['name' => 'Aarav Shrestha']);

        $this->actingAs($this->admin)
            ->getJson(route('admin.staff.index', [
                'draw' => 1,
                'start' => 0,
                'length' => 10,
                'search' => ['value' => 'inactive', 'regex' => 'false'],
                'columns' => $this->datatableColumns(),
            ]), self::AJAX)
            ->assertOk()
            ->assertJsonPath('recordsFiltered', 1);
    }

    #[Test]
    public function create_edit_and_show_pages_render(): void
    {
        $staff = User::factory()->create(['name' => 'Aarav Shrestha']);

        $this->actingAs($this->admin)->get(route('admin.staff.create'))
            ->assertOk()->assertSee('Create Staff');

        $this->actingAs($this->admin)->get(route('admin.staff.edit', $staff))
            ->assertOk()->assertSee('Edit Staff')->assertSee('Aarav Shrestha');

        $this->actingAs($this->admin)->get(route('admin.staff.show', $staff))
            ->assertOk()->assertSee('Aarav Shrestha')->assertSee('Permissions');
    }

    #[Test]
    public function create_form_pre_ticks_the_configured_default_permissions(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.staff.create'))
            ->assertOk()
            ->assertViewHas('checkedPermissions', PermissionSeeder::defaultSlugs());
    }

    #[Test]
    public function admin_can_create_a_staff_member_and_grant_the_chosen_permissions(): void
    {
        $chosen = ['students.view', 'reports.generate'];

        $this->actingAs($this->admin)
            ->post(route('admin.staff.store'), [
                'name' => 'Maya Gurung',
                'email' => 'maya@example.test',
                'position' => 'Admissions Counselor',
                'status' => 'active',
                'password' => 'password123',
                'permissions' => $chosen,
            ])
            ->assertRedirect(route('admin.staff.index'));

        $staff = User::where('email', 'maya@example.test')->firstOrFail();

        $this->assertSame('staff', $staff->role);
        $this->assertTrue(Hash::check('password123', $staff->password));
        $this->assertEqualsCanonicalizing($chosen, $staff->getPermissionNames()->all());
    }

    #[Test]
    public function omitting_permissions_on_create_grants_none(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.staff.store'), [
                'name' => 'Maya Gurung',
                'email' => 'maya@example.test',
                'position' => 'Admissions Counselor',
                'status' => 'active',
                'password' => 'password123',
            ])
            ->assertRedirect(route('admin.staff.index'));

        $staff = User::where('email', 'maya@example.test')->firstOrFail();

        $this->assertCount(0, $staff->getPermissionNames());
    }

    #[Test]
    public function store_rejects_an_unknown_permission_slug(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.staff.store'), [
                'name' => 'Maya Gurung',
                'email' => 'maya@example.test',
                'position' => 'Admissions Counselor',
                'status' => 'active',
                'password' => 'password123',
                'permissions' => ['not-a-real-permission'],
            ])
            ->assertSessionHasErrors('permissions.0');

        $this->assertDatabaseMissing('users', ['email' => 'maya@example.test']);
    }

    #[Test]
    public function staff_creation_requires_an_active_admission_year(): void
    {
        AdmissionYear::query()->update(['is_active' => false]);

        $this->actingAs($this->admin)
            ->post(route('admin.staff.store'), [
                'name' => 'Maya Gurung',
                'email' => 'maya@example.test',
                'position' => 'Admissions Counselor',
                'status' => 'active',
                'password' => 'password123',
            ])
            ->assertSessionHasErrors('error');

        $this->assertDatabaseMissing('users', ['email' => 'maya@example.test']);
    }

    #[Test]
    public function admin_can_update_a_staff_member(): void
    {
        $staff = User::factory()->create(['position' => 'Junior Officer']);

        $this->actingAs($this->admin)
            ->put(route('admin.staff.update', $staff), [
                'name' => $staff->name,
                'email' => $staff->email,
                'position' => 'Senior Officer',
                'status' => 'active',
            ])
            ->assertRedirect(route('admin.staff.index'));

        $this->assertSame('Senior Officer', $staff->fresh()->position);
    }

    #[Test]
    public function toggle_status_flips_between_active_and_inactive(): void
    {
        $staff = User::factory()->create();

        $this->actingAs($this->admin)
            ->postJson(route('admin.staff.toggle-status', $staff))
            ->assertOk()
            ->assertJsonPath('data.status', 'inactive');

        $this->assertSame('inactive', $staff->fresh()->status);
    }

    #[Test]
    public function admin_accounts_are_not_reachable_through_the_staff_endpoints(): void
    {
        $this->actingAs($this->admin)
            ->deleteJson(route('admin.staff.destroy', $this->admin))
            ->assertStatus(404);
    }

    #[Test]
    public function admin_can_delete_a_staff_member(): void
    {
        $staff = User::factory()->create();

        $this->actingAs($this->admin)
            ->deleteJson(route('admin.staff.destroy', $staff))
            ->assertOk();

        $this->assertDatabaseMissing('users', ['id' => $staff->id]);
    }

    #[Test]
    public function admin_can_revoke_and_regrant_individual_permissions(): void
    {
        $staff = User::factory()->create();
        $staff->syncPermissions(PermissionSeeder::allSlugs());

        $remaining = PermissionSeeder::allSlugs();
        $revoked = array_pop($remaining);

        $this->actingAs($this->admin)
            ->putJson(route('admin.staff.permissions.update', $staff), ['permissions' => $remaining])
            ->assertOk();

        $this->assertFalse($staff->fresh()->hasPermissionTo($revoked));
        $this->assertTrue($staff->fresh()->hasPermissionTo($remaining[0]));
    }

    #[Test]
    public function updating_permissions_rejects_an_unknown_slug(): void
    {
        $staff = User::factory()->create();

        $this->actingAs($this->admin)
            ->putJson(route('admin.staff.permissions.update', $staff), ['permissions' => ['not-a-real-permission']])
            ->assertStatus(422);
    }

    #[Test]
    public function staff_cannot_reach_the_staff_management_routes(): void
    {
        $staff = $this->staff();
        $other = User::factory()->create();

        // EnsureRole bounces a non-admin back to their own dashboard.
        $this->actingAs($staff)->get(route('admin.staff.index'))->assertRedirect(route('staff.dashboard'));
        $this->actingAs($staff)->get(route('admin.staff.create'))->assertRedirect(route('staff.dashboard'));
        $this->actingAs($staff)->post(route('admin.staff.store'), [])->assertRedirect(route('staff.dashboard'));
        $this->actingAs($staff)->put(route('admin.staff.update', $other), [])->assertRedirect(route('staff.dashboard'));
    }

    private function staff(): User
    {
        return User::create([
            'name' => 'Staff',
            'email' => 'staff@example.test',
            'password' => Hash::make('password'),
            'role' => 'staff',
            'status' => 'active',
            'position' => 'Admissions Officer',
        ]);
    }

    /**
     * The column definitions DataTables sends with every request.
     *
     * Mirrors what yajra's minifiedAjax() leaves in place: an identical `name`
     * is dropped, and `searchable` / `orderable` only survive when false.
     *
     * @return array<int, array<string, mixed>>
     */
    private function datatableColumns(): array
    {
        $columns = ['DT_RowIndex', 'name', 'position', 'status', 'permissions', 'created_at', 'action'];
        $computed = ['DT_RowIndex', 'permissions', 'action'];

        return collect($columns)->map(function (string $name) use ($computed) {
            $column = ['data' => $name];

            if (in_array($name, $computed, true)) {
                $column['searchable'] = 'false';
                $column['orderable'] = 'false';
            }

            return $column;
        })->all();
    }
}
