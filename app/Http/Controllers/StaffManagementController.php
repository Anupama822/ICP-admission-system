<?php

namespace App\Http\Controllers;

use App\DataTables\StaffDataTable;
use App\Http\Requests\StaffRequest;
use App\Models\AdmissionYear;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class StaffManagementController extends BaseController
{
    public function __construct()
    {
        $this->title = 'Staff';
        $this->subTitle = 'Staff Management';
        $this->resources = 'admin.staff.';
        $this->icon = 'heroicon-o-users';
        $this->route = 'admin.staff.';
        $this->description = 'Create and manage staff accounts and the permissions granted to them.';
        parent::__construct();
    }

    /**
     * Index backed by a server side DataTable (search, sort, paginate, export).
     */
    public function index(StaffDataTable $dataTable)
    {
        return $dataTable->render($this->indexResource(), $this->crudInfo() + [
            'add_button_name' => 'Add Staff',
        ]);
    }

    public function create()
    {
        return view($this->createResource(), $this->crudInfo() + [
            'permissionCatalog' => PermissionSeeder::catalog(),
            'checkedPermissions' => old('permissions', PermissionSeeder::defaultSlugs()),
        ]);
    }

    public function store(StaffRequest $request)
    {
        if (! AdmissionYear::where('is_active', true)->exists()) {
            return back()->withErrors(['error' => 'Set an active academic year before creating new records.']);
        }

        $validated = $request->safe()->only(['name', 'email', 'position', 'status', 'password']);
        $validated['role'] = 'staff';
        $validated['password'] = Hash::make($validated['password']);
        $validated['image_url'] = 'https://ui-avatars.com/api/?name='.urlencode($validated['name']).'&background=0284c7&color=fff';

        $staff = User::create($validated);
        $staff->syncPermissions($request->validated('permissions', []));

        return $this->gotoCrudIndex()
            ->with('status', "Staff member {$staff->name} added successfully.");
    }

    public function show(User $staff)
    {
        $this->ensureIsStaff($staff);

        return view($this->showResource(), $this->crudInfo() + [
            'item' => $staff,
            'permissionCatalog' => PermissionSeeder::catalog(),
            'grantedPermissions' => $staff->getPermissionNames()->all(),
        ]);
    }

    public function edit(User $staff)
    {
        $this->ensureIsStaff($staff);

        return view($this->editResource(), $this->crudInfo() + ['item' => $staff]);
    }

    public function update(StaffRequest $request, User $staff)
    {
        $this->ensureIsStaff($staff);

        $staff->update($request->safe()->only(['name', 'email', 'position', 'status']));

        return $this->gotoCrudIndex()
            ->with('status', "Staff member {$staff->name} updated successfully.");
    }

    /**
     * Toggle active/inactive status of a staff member.
     */
    public function toggleStatus(Request $request, User $staff)
    {
        $this->ensureIsStaff($staff);

        $staff->status = $staff->status === 'active' ? 'inactive' : 'active';
        $staff->save();

        $message = "Staff member {$staff->name} status changed to ".ucfirst($staff->status).'.';

        return $request->expectsJson()
            ? $this->returnSuccess(['message' => $message, 'status' => $staff->status])
            : redirect()->back()->with('status', $message);
    }

    public function destroy(Request $request, User $staff)
    {
        // Admins can only ever reach this route themselves, and ensureIsStaff
        // already rejects any target that isn't a plain staff account, so
        // self-deletion can't happen here.
        $this->ensureIsStaff($staff);

        $name = $staff->name;
        $staff->delete();

        return $request->expectsJson()
            ? $this->returnSuccess(['message' => "Staff member {$name} deleted."])
            : $this->gotoCrudIndex()->with('status', "Staff member {$name} deleted.");
    }

    /**
     * Sync the permissions granted to a staff member from the checkboxes on
     * their profile page.
     */
    public function updatePermissions(Request $request, User $staff): JsonResponse
    {
        $this->ensureIsStaff($staff);

        $validated = $request->validate([
            'permissions' => ['sometimes', 'array'],
            'permissions.*' => [Rule::in(PermissionSeeder::allSlugs())],
        ]);

        $staff->syncPermissions($validated['permissions'] ?? []);

        return $this->returnSuccess([
            'permissions' => $staff->getPermissionNames(),
            'message' => 'Permissions updated.',
        ]);
    }

    /**
     * Guard every action against being pointed at a non-staff (admin) user.
     */
    private function ensureIsStaff(User $staff): void
    {
        abort_if($staff->role !== 'staff', 404);
    }
}
