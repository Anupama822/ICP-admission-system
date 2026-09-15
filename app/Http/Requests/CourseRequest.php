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
            'display_title' => ['required', 'string', 'max:255'],
            'levels' => ['required', 'string', 'max:255'],
            'credits' => ['nullable', 'numeric', 'min:0', 'max:20'],
            'description' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'course title',
            'display_title' => 'display title',
            'levels' => 'levels',
            'credits' => 'credits',
            'description' => 'description',
        ];
    }

    /**
     * The submitted comma-separated levels ("04, 05, 06"), normalized into a
     * clean array (trimmed, no blanks, no duplicates).
     *
     * @return array<int, string>
     */
    public function levelsArray(): array
    {
        return collect(explode(',', (string) $this->input('levels')))
            ->map(fn (string $level) => trim($level))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
