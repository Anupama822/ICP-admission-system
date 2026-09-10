<?php

namespace App\Http\Requests;

use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StaffRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        // On update the route model is bound as {staff}; ignore it so the
        // record does not clash with its own email.
        $ignoreId = $this->route('staff')?->getKey();

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($ignoreId)],
            'position' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'password' => [$this->isMethod('post') ? 'required' : 'sometimes', 'string', 'min:6'],
            'permissions' => ['sometimes', 'array'],
            'permissions.*' => [Rule::in(PermissionSeeder::allSlugs())],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'full name',
            'email' => 'email address',
            'position' => 'position',
            'status' => 'status',
            'password' => 'password',
        ];
    }
}
