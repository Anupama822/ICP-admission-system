<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        // On update the route model is bound as {course}; ignore it so the
        // record does not clash with its own title.
        $ignoreId = $this->route('course')?->getKey();

        return [
            'title' => ['required', 'string', 'max:255', Rule::unique('courses', 'title')->ignore($ignoreId)],
            'credits' => ['nullable', 'numeric', 'min:0', 'max:20'],
            'description' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'course title',
            'credits' => 'credits',
            'description' => 'description',
        ];
    }
}
