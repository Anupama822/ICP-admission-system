<?php

namespace App\Http\Controllers;

use App\Http\Requests\DocumentTypeRequest;
use App\Models\DocumentType;
use App\Models\StudentQualification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DocumentTypeController extends BaseController
{
    public function __construct()
    {
        $this->title = 'Educational Board';
        $this->subTitle = 'Educational Board Management';
        $this->resources = 'admin.document-types.';
        $this->icon = 'heroicon-o-document-text';
        $this->route = 'admin.document-types.';
        $this->description = 'Manage the educational boards offered on the student enrollment form (NEB, SEE, A-Level, ...).';
        parent::__construct();
    }

    /**
     * The only page for this resource: everything (add / rename / delete)
     * happens inline from here.
     */
    public function index()
    {
        return view($this->indexResource(), $this->crudInfo() + [
            'hideCreate' => true,
            'items' => DocumentType::orderBy('name')->get(),
        ]);
    }

    public function store(DocumentTypeRequest $request)
    {
        $documentType = DocumentType::create($request->validated());

        if ($request->expectsJson()) {
            return $this->returnSuccess([
                'id' => $documentType->getKey(),
                'name' => $documentType->name,
                'row' => view('admin.document-types.partials.row', ['item' => $documentType])->render(),
                'message' => "Educational board {$documentType->name} added.",
            ]);
        }

        return $this->gotoCrudIndex()
            ->with('status', "Educational board {$documentType->name} created successfully.");
    }

    /**
     * Fields that may be changed straight from the index table.
     */
    private const INLINE_EDITABLE = ['name', 'format_hint'];

    /**
     * Save a single field edited straight from the index table.
     */
    public function inlineUpdate(Request $request, DocumentType $documentType): JsonResponse
    {
        $field = (string) $request->input('field', 'name');

        if (! in_array($field, self::INLINE_EDITABLE, true)) {
            return $this->sendError('This field cannot be edited inline.', [], 422);
        }

        $rules = [
            'name' => ['required', 'string', 'max:100', Rule::unique('document_types', 'name')->ignore($documentType->getKey())],
            'format_hint' => ['nullable', 'string', 'max:2000'],
        ];

        // An empty string means "clear it" for the optional hint.
        $value = $request->input('value');
        if ($value === '' && $field !== 'name') {
            $value = null;
        }

        $validator = Validator::make(
            [$field => $value],
            [$field => $rules[$field]],
            [],
            ['name' => 'educational board', 'format_hint' => 'format example']
        );

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first($field), $validator->errors()->toArray(), 422);
        }

        $documentType->update([$field => $validator->validated()[$field]]);

        return $this->returnSuccess([
            'field' => $field,
            'value' => (string) $documentType->{$field},
            'message' => 'Saved.',
        ]);
    }

    public function destroy(Request $request, DocumentType $documentType)
    {
        if (StudentQualification::where('document_type', $documentType->name)->exists()) {
            $message = 'This educational board is used by existing qualifications and cannot be deleted.';

            return $request->expectsJson()
                ? $this->sendError($message, [], 422)
                : redirect()->back()->withErrors($message);
        }

        $name = $documentType->name;
        $documentType->delete();

        return $request->expectsJson()
            ? $this->returnSuccess(['message' => "Educational board {$name} deleted."])
            : $this->gotoCrudIndex()->with('status', "Educational board {$name} deleted.");
    }
}
