<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CourseCrudTest extends TestCase
{
    use RefreshDatabase;

    /** Headers jQuery DataTables sends with its ajax draw requests. */
    private const AJAX = ['X-Requested-With' => 'XMLHttpRequest'];

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
            'position' => 'Administrator',
        ]);
    }

    #[Test]
    public function index_renders_the_datatable_shell(): void
    {
        Course::factory()->create(['title' => 'Introduction to Programming']);

        $this->actingAs($this->admin)
            ->get(route('admin.courses.index'))
            ->assertOk()
            ->assertSee('courses-table')
            ->assertSee('Add Course');
    }

    #[Test]
    public function datatable_ajax_returns_searched_sorted_and_paginated_rows(): void
    {
        Course::factory()->create(['title' => 'Algorithms']);
        Course::factory()->create(['title' => 'Databases']);
        Course::factory()->create(['title' => 'Networking']);

        $response = $this->actingAs($this->admin)->getJson(route('admin.courses.index', [
            'draw' => 1,
            'start' => 0,
            'length' => 10,
            'search' => ['value' => 'Data', 'regex' => 'false'],
            'columns' => $this->datatableColumns(),
        ]), self::AJAX);

        $response->assertOk()
            ->assertJsonPath('recordsTotal', 3)
            ->assertJsonPath('recordsFiltered', 1);

        $this->assertStringContainsString('Databases', $response->json('data.0.title'));
    }

    #[Test]
    public function create_edit_and_show_pages_render(): void
    {
        $course = Course::factory()->create(['title' => 'Algorithms', 'credits' => 3]);

        $this->actingAs($this->admin)->get(route('admin.courses.create'))
            ->assertOk()->assertSee('Create Course');

        $this->actingAs($this->admin)->get(route('admin.courses.edit', $course))
            ->assertOk()->assertSee('Edit Course')->assertSee('Algorithms');

        $this->actingAs($this->admin)->get(route('admin.courses.show', $course))
            ->assertOk()->assertSee('Algorithms');
    }

    #[Test]
    public function admin_can_create_a_course_with_only_a_title(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.courses.store'), [
                'title' => 'Introduction to Programming',
            ])
            ->assertRedirect(route('admin.courses.index'));

        $this->assertDatabaseHas('courses', [
            'title' => 'Introduction to Programming',
            'credits' => null,
            'description' => null,
        ]);
    }

    #[Test]
    public function admin_can_create_a_course_with_credits_and_a_description(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.courses.store'), [
                'title' => 'Data Structures',
                'credits' => 3.5,
                'description' => 'Covers linked lists, trees, and graphs.',
            ])
            ->assertRedirect(route('admin.courses.index'));

        $course = Course::where('title', 'Data Structures')->firstOrFail();

        $this->assertSame('3.5', (string) $course->credits);
        $this->assertSame('Covers linked lists, trees, and graphs.', $course->description);
    }

    #[Test]
    public function course_titles_must_be_unique(): void
    {
        Course::factory()->create(['title' => 'Algorithms']);

        $this->actingAs($this->admin)
            ->post(route('admin.courses.store'), ['title' => 'Algorithms'])
            ->assertSessionHasErrors('title');
    }

    #[Test]
    public function admin_can_update_a_course(): void
    {
        $course = Course::factory()->create(['title' => 'Algorithms', 'credits' => 3]);

        $this->actingAs($this->admin)
            ->put(route('admin.courses.update', $course), [
                'title' => 'Advanced Algorithms',
                'credits' => 4,
            ])
            ->assertRedirect(route('admin.courses.index'));

        $course->refresh();
        $this->assertSame('Advanced Algorithms', $course->title);
        $this->assertSame('4.0', (string) $course->credits);
    }

    #[Test]
    public function inline_update_saves_a_single_field(): void
    {
        $course = Course::factory()->create(['title' => 'Algorithms', 'credits' => 3]);

        $this->actingAs($this->admin)
            ->patchJson(route('admin.courses.inline-update', $course), ['field' => 'title', 'value' => 'Advanced Algorithms'])
            ->assertOk()
            ->assertJsonPath('status', true)
            ->assertJsonPath('data.value', 'Advanced Algorithms');

        $this->assertSame('Advanced Algorithms', $course->fresh()->title);
    }

    #[Test]
    public function inline_update_can_change_credits(): void
    {
        $course = Course::factory()->create(['credits' => 3]);

        $this->actingAs($this->admin)
            ->patchJson(route('admin.courses.inline-update', $course), ['field' => 'credits', 'value' => '4.5'])
            ->assertOk()
            ->assertJsonPath('data.value', '4.5');

        $this->assertSame('4.5', (string) $course->fresh()->credits);
    }

    #[Test]
    public function inline_update_can_clear_an_optional_field(): void
    {
        $course = Course::factory()->create(['credits' => 3, 'description' => 'Something']);

        $this->actingAs($this->admin)
            ->patchJson(route('admin.courses.inline-update', $course), ['field' => 'credits', 'value' => ''])
            ->assertOk()
            ->assertJsonPath('data.value', '');

        $this->assertNull($course->fresh()->credits);
    }

    #[Test]
    public function inline_update_rejects_an_unknown_field(): void
    {
        $course = Course::factory()->create();

        $this->actingAs($this->admin)
            ->patchJson(route('admin.courses.inline-update', $course), ['field' => 'id', 'value' => 'x'])
            ->assertStatus(422);
    }

    #[Test]
    public function inline_update_rejects_a_duplicate_title(): void
    {
        Course::factory()->create(['title' => 'Algorithms']);
        $course = Course::factory()->create(['title' => 'Databases']);

        $this->actingAs($this->admin)
            ->patchJson(route('admin.courses.inline-update', $course), ['field' => 'title', 'value' => 'Algorithms'])
            ->assertStatus(422);

        $this->assertSame('Databases', $course->fresh()->title);
    }

    #[Test]
    public function admin_can_delete_a_course(): void
    {
        $course = Course::factory()->create();

        $this->actingAs($this->admin)
            ->deleteJson(route('admin.courses.destroy', $course))
            ->assertOk();

        $this->assertDatabaseMissing('courses', ['id' => $course->id]);
    }

    #[Test]
    public function staff_cannot_reach_any_course_management_route(): void
    {
        $staff = $this->staff();
        $course = Course::factory()->create();

        // EnsureRole bounces a non-admin back to their own dashboard.
        $this->actingAs($staff)->get(route('admin.courses.index'))->assertRedirect(route('staff.dashboard'));
        $this->actingAs($staff)->get(route('admin.courses.create'))->assertRedirect(route('staff.dashboard'));
        $this->actingAs($staff)->post(route('admin.courses.store'), [])->assertRedirect(route('staff.dashboard'));
        $this->actingAs($staff)->get(route('admin.courses.show', $course))->assertRedirect(route('staff.dashboard'));
        $this->actingAs($staff)->get(route('admin.courses.edit', $course))->assertRedirect(route('staff.dashboard'));
        $this->actingAs($staff)->put(route('admin.courses.update', $course), [])->assertRedirect(route('staff.dashboard'));
        $this->actingAs($staff)->patch(route('admin.courses.inline-update', $course), [])->assertRedirect(route('staff.dashboard'));
        $this->actingAs($staff)->delete(route('admin.courses.destroy', $course))->assertRedirect(route('staff.dashboard'));
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
        $columns = ['DT_RowIndex', 'title', 'credits', 'description', 'created_at', 'action'];
        $computed = ['DT_RowIndex', 'action'];

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
