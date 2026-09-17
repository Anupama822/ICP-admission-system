<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DocumentTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        // On update the route model is bound as {documentType}; ignore it
        // so the record does not clash with its own name.
        $ignoreId = $this->route('documentType')?->getKey();

        return [
            'name' => ['required', 'string', 'max:100', Rule::unique('document_types', 'name')->ignore($ignoreId)],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'document type',
        ];
    }
}
