<?php

namespace Tests\Feature;

use App\Models\AdmissionYear;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdmissionYearCrudTest extends TestCase
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
        AdmissionYear::factory()->active()->create(['title' => '2026/27']);

        $this->actingAs($this->admin)
            ->get(route('admission-year.index'))
            ->assertOk()
            ->assertSee('admission-years-table')
            ->assertSee('Add Admission Year');
    }

    #[Test]
    public function datatable_ajax_returns_searched_sorted_and_paginated_rows(): void
    {
        AdmissionYear::factory()->create(['title' => '2024/25', 'year' => '2024']);
        AdmissionYear::factory()->create(['title' => '2025/26', 'year' => '2025']);
        AdmissionYear::factory()->create(['title' => '2026/27', 'year' => '2026']);

        $response = $this->actingAs($this->admin)->getJson(route('admission-year.index', [
            'draw' => 1,
            'start' => 0,
            'length' => 10,
            'search' => ['value' => '2025', 'regex' => 'false'],
            'order' => [['column' => 2, 'dir' => 'asc']],
            'columns' => $this->datatableColumns(),
        ]), self::AJAX);

        $response->assertOk()
            ->assertJsonPath('recordsTotal', 3)
            ->assertJsonPath('recordsFiltered', 1);

        $this->assertStringContainsString('2025/26', $response->json('data.0.title'));
    }

    #[Test]
    public function datatable_search_understands_the_status_column(): void
    {
        AdmissionYear::factory()->active()->create(['title' => '2026/27']);
        AdmissionYear::factory()->create(['title' => '2025/26']);

        $this->actingAs($this->admin)
            ->getJson(route('admission-year.index', [
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
    public function csv_export_streams_every_matching_row(): void
    {
        AdmissionYear::factory()->create(['title' => '2026/27', 'year' => '2026']);

        $response = $this->actingAs($this->admin)->get(route('admission-year.index', [
            'action' => 'csv',
            'columns' => $this->datatableColumns(),
        ]));

        $response->assertOk();
        $this->assertStringContainsString('.csv', (string) $response->headers->get('content-disposition'));
        $this->assertStringContainsString('admission-years_', (string) $response->headers->get('content-disposition'));
        $content = $response->streamedContent();

        $this->assertStringContainsString('2026/27', $content);
        // The status badge and the inline-edit markup must not leak into the file.
        $this->assertStringContainsString('Inactive', $content);
        $this->assertStringNotContainsString('<span', $content);
        $this->assertStringNotContainsString('Actions', $content);
    }

    #[Test]
    public function pdf_export_returns_a_pdf_document(): void
    {
        AdmissionYear::factory()->create(['title' => '2026/27', 'year' => '2026']);

        $response = $this->actingAs($this->admin)->get(route('admission-year.index', [
            'action' => 'pdf',
            'columns' => $this->datatableColumns(),
        ]));

        $response->assertOk();
        $this->assertStringContainsString('pdf', (string) $response->headers->get('content-type'));
    }

    #[Test]
    public function print_preview_uses_the_branded_template(): void
    {
        AdmissionYear::factory()->create(['title' => '2026/27', 'year' => '2026']);

        $this->actingAs($this->admin)
            ->get(route('admission-year.index', [
                'action' => 'print',
                'columns' => $this->datatableColumns(),
            ]))
            ->assertOk()
            ->assertSee('2026/27')
            ->assertDontSee('Actions');
    }

    #[Test]
    public function create_edit_and_show_pages_render(): void
    {
        $year = AdmissionYear::factory()->create(['title' => '2026/27', 'year' => '2026']);

        $this->actingAs($this->admin)->get(route('admission-year.create'))
            ->assertOk()->assertSee('Create Admission Year');

        $this->actingAs($this->admin)->get(route('admission-year.edit', $year))
            ->assertOk()->assertSee('Edit Admission Year')->assertSee('2026/27');

        $this->actingAs($this->admin)->get(route('admission-year.show', $year))
            ->assertOk()->assertSee('2026/27')->assertSee('Inactive');
    }

    #[Test]
    public function admin_can_create_an_admission_year_and_activate_it(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admission-year.store'), [
                'title' => '2026/27',
                'year' => '2026',
                'is_active' => 1,
            ])
            ->assertRedirect(route('admission-year.index'));

        $this->assertDatabaseHas('admission_years', ['title' => '2026/27', 'is_active' => true]);
    }

    #[Test]
    public function activating_a_year_deactivates_the_previous_one(): void
    {
        $old = AdmissionYear::factory()->active()->create(['title' => '2025/26']);
        $new = AdmissionYear::factory()->create(['title' => '2026/27']);

        $this->actingAs($this->admin)
            ->post(route('admission-year.activate', $new))
            ->assertRedirect();

        $this->assertTrue($new->fresh()->is_active);
        $this->assertFalse($old->fresh()->is_active);
    }

    #[Test]
    public function admin_can_update_an_admission_year(): void
    {
        $year = AdmissionYear::factory()->create(['title' => '2025/26', 'year' => '2025']);

        $this->actingAs($this->admin)
            ->put(route('admission-year.update', $year), ['title' => '2026/27', 'year' => '2026'])
            ->assertRedirect(route('admission-year.index'));

        $this->assertDatabaseHas('admission_years', ['id' => $year->id, 'title' => '2026/27', 'year' => '2026']);
    }

    #[Test]
    public function inline_update_saves_a_single_field(): void
    {
        $year = AdmissionYear::factory()->create(['title' => '2025/26', 'year' => '2025']);

        $this->actingAs($this->admin)
            ->patchJson(route('admission-year.inline-update', $year), ['field' => 'year', 'value' => '2027'])
            ->assertOk()
            ->assertJsonPath('status', true)
            ->assertJsonPath('data.value', '2027');

        $this->assertSame('2027', $year->fresh()->year);
    }

    #[Test]
    public function inline_update_rejects_an_unknown_field(): void
    {
        $year = AdmissionYear::factory()->create();

        $this->actingAs($this->admin)
            ->patchJson(route('admission-year.inline-update', $year), ['field' => 'is_active', 'value' => '1'])
            ->assertStatus(422);
    }

    #[Test]
    public function inline_update_rejects_a_duplicate_title(): void
    {
        AdmissionYear::factory()->create(['title' => '2026/27']);
        $year = AdmissionYear::factory()->create(['title' => '2025/26']);

        $this->actingAs($this->admin)
            ->patchJson(route('admission-year.inline-update', $year), ['field' => 'title', 'value' => '2026/27'])
            ->assertStatus(422);

        $this->assertSame('2025/26', $year->fresh()->title);
    }

    #[Test]
    public function the_active_admission_year_cannot_be_deleted(): void
    {
        $year = AdmissionYear::factory()->active()->create();

        $this->actingAs($this->admin)
            ->deleteJson(route('admission-year.destroy', $year))
            ->assertStatus(422);

        $this->assertDatabaseHas('admission_years', ['id' => $year->id]);
    }

    #[Test]
    public function an_inactive_admission_year_can_be_deleted(): void
    {
        $year = AdmissionYear::factory()->create();

        $this->actingAs($this->admin)
            ->deleteJson(route('admission-year.destroy', $year))
            ->assertOk();

        $this->assertDatabaseMissing('admission_years', ['id' => $year->id]);
    }

    #[Test]
    public function staff_get_a_read_only_table(): void
    {
        AdmissionYear::factory()->create(['title' => '2026/27']);

        // Note: only one request per test may hit the table — yajra binds
        // 'datatables.request' as a container singleton, so a second request in
        // the same process would replay the first one's input.
        $this->actingAs($this->staff())
            ->getJson(route('admission-year.index', [
                'draw' => 1,
                'start' => 0,
                'length' => 10,
                'columns' => $this->datatableColumns(),
            ]), self::AJAX)
            ->assertOk()
            ->assertJsonPath('recordsTotal', 1)
            ->assertDontSee('icp-inline-edit')
            ->assertDontSee('js-delete');
    }

    #[Test]
    public function staff_cannot_reach_the_write_endpoints(): void
    {
        $staff = $this->staff();
        $year = AdmissionYear::factory()->create();

        // EnsureRole bounces a non-admin back to their own dashboard.
        $this->actingAs($staff)->get(route('admission-year.create'))->assertRedirect(route('staff.dashboard'));
        $this->actingAs($staff)->put(route('admission-year.update', $year), [])->assertRedirect(route('staff.dashboard'));
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
        $columns = ['DT_RowIndex', 'title', 'year', 'is_active', 'created_at', 'action'];
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
