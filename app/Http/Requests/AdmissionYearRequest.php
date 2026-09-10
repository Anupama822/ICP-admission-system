<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdmissionYearRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        // On update the route model is bound as {admissionYear}; ignore it so the
        // record does not clash with its own title.
        $ignoreId = $this->route('admissionYear')?->getKey();

        return [
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('admission_years', 'title')->ignore($ignoreId),
            ],
            'year' => ['required', 'digits:4', 'integer', 'between:2000,2100'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'admission year title',
            'year' => 'starting year',
            'is_active' => 'active status',
        ];
    }
}
