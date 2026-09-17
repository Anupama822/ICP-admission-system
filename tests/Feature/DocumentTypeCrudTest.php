<?php

namespace Tests\Feature;

use App\Models\DocumentType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DocumentTypeCrudTest extends TestCase
{
    use RefreshDatabase;

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
    public function index_lists_document_types(): void
    {
        DocumentType::create(['name' => 'NEB']);

        $this->actingAs($this->admin)
            ->get(route('admin.document-types.index'))
            ->assertOk()
            ->assertSee('NEB')
            ->assertSee('add-document-type-form', false);
    }

    #[Test]
    public function admin_can_create_a_document_type(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.document-types.store'), ['name' => 'A-Level'])
            ->assertRedirect(route('admin.document-types.index'));

        $this->assertDatabaseHas('document_types', ['name' => 'A-Level']);
    }

    #[Test]
    public function document_type_names_must_be_unique(): void
    {
        DocumentType::create(['name' => 'NEB']);

        $this->actingAs($this->admin)
            ->post(route('admin.document-types.store'), ['name' => 'NEB'])
            ->assertSessionHasErrors('name');
    }

    #[Test]
    public function admin_can_add_a_document_type_inline_via_ajax(): void
    {
        $response = $this->actingAs($this->admin)
            ->postJson(route('admin.document-types.store'), ['name' => 'A-Level'])
            ->assertOk();

        $this->assertSame('A-Level', $response->json('data.name'));
        $this->assertStringContainsString('A-Level', $response->json('data.row'));
        $this->assertDatabaseHas('document_types', ['name' => 'A-Level']);
    }

    #[Test]
    public function admin_can_rename_a_document_type_inline(): void
    {
        $documentType = DocumentType::create(['name' => 'NEB']);

        $this->actingAs($this->admin)
            ->patchJson(route('admin.document-types.inline-update', $documentType), ['value' => 'NEB (10+2)'])
            ->assertOk()
            ->assertJsonPath('data.value', 'NEB (10+2)');

        $this->assertSame('NEB (10+2)', $documentType->fresh()->name);
    }

    #[Test]
    public function admin_can_set_the_format_hint_inline(): void
    {
        $documentType = DocumentType::create(['name' => 'NEB']);

        $this->actingAs($this->admin)
            ->patchJson(route('admin.document-types.inline-update', $documentType), [
                'field' => 'format_hint',
                'value' => 'NEB +2 (CGPA-X.XX Year 12 Eng-Y)',
            ])
            ->assertOk()
            ->assertJsonPath('data.value', 'NEB +2 (CGPA-X.XX Year 12 Eng-Y)');

        $this->assertSame('NEB +2 (CGPA-X.XX Year 12 Eng-Y)', $documentType->fresh()->format_hint);
    }

    #[Test]
    public function inline_rename_rejects_a_duplicate_name(): void
    {
        DocumentType::create(['name' => 'NEB']);
        $documentType = DocumentType::create(['name' => 'SEE']);

        $this->actingAs($this->admin)
            ->patchJson(route('admin.document-types.inline-update', $documentType), ['value' => 'NEB'])
            ->assertStatus(422);

        $this->assertSame('SEE', $documentType->fresh()->name);
    }

    #[Test]
    public function admin_can_delete_an_unused_document_type(): void
    {
        $documentType = DocumentType::create(['name' => 'NEB']);

        $this->actingAs($this->admin)
            ->delete(route('admin.document-types.destroy', $documentType))
            ->assertRedirect(route('admin.document-types.index'));

        $this->assertDatabaseMissing('document_types', ['id' => $documentType->id]);
    }

    #[Test]
    public function staff_cannot_reach_any_document_type_route(): void
    {
        $staff = User::create([
            'name' => 'Staff',
            'email' => 'staff@example.test',
            'password' => Hash::make('password'),
            'role' => 'staff',
            'status' => 'active',
            'position' => 'Admissions Officer',
        ]);
        $documentType = DocumentType::create(['name' => 'NEB']);

        // EnsureRole bounces a non-admin back to their own dashboard.
        $this->actingAs($staff)->get(route('admin.document-types.index'))->assertRedirect(route('staff.dashboard'));
        $this->actingAs($staff)->post(route('admin.document-types.store'), [])->assertRedirect(route('staff.dashboard'));
        $this->actingAs($staff)->patch(route('admin.document-types.inline-update', $documentType), [])->assertRedirect(route('staff.dashboard'));
        $this->actingAs($staff)->delete(route('admin.document-types.destroy', $documentType))->assertRedirect(route('staff.dashboard'));
    }
}
