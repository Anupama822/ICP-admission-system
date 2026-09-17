<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FacultyRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        // Faculty management (rename/delete) is admin-only, but adding a new
        // faculty inline from the student enrollment form should also work
        // for any staff member permitted to create/edit students.
        return $user && ($user->isAdmin() || $user->can('students.create') || $user->can('students.edit'));
    }

    public function rules(): array
    {
        // On update the route model is bound as {faculty}; ignore it so the
        // record does not clash with its own name.
        $ignoreId = $this->route('faculty')?->getKey();

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('faculties', 'name')->ignore($ignoreId)],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'faculty',
        ];
    }
}
