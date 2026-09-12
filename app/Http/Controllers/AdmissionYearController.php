<?php

namespace App\Http\Controllers;

use App\DataTables\AdmissionYearDataTable;
use App\Http\Requests\AdmissionYearRequest;
use App\Models\AdmissionYear;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AdmissionYearController extends BaseController
{
    /**
     * Fields that may be changed straight from the index table.
     */
    private const INLINE_EDITABLE = ['title', 'year'];

    public function __construct()
    {
        $this->title = 'Admission Year';
        $this->subTitle = 'Admission Year';
        $this->resources = 'admin.admissionYears.';
        $this->icon = 'heroicon-o-calendar-days';
        $this->route = 'admission-year.';
        $this->description = 'Create the Admission Year that will be active for the system.';
        parent::__construct();
        // $this->generateAllMiddlewareByPermission();
    }

    /**
     * Index backed by a server side DataTable (search, sort, paginate, export).
     */
    public function index(AdmissionYearDataTable $dataTable)
    {
        return $dataTable->render($this->indexResource(), $this->crudInfo() + [
            'add_button_name' => 'Add Admission Year',
        ]);
    }

    public function create()
    {
        return view($this->createResource(), $this->crudInfo() + $this->yearFormOptions() + ['wide' => true]);
    }

    public function store(AdmissionYearRequest $request)
    {
        $admissionYear = AdmissionYear::create($request->safe()->only(['title', 'year', 'intake']) + ['is_active' => false]);

        if ($request->boolean('is_active')) {
            $admissionYear->activate();
        }

        return $this->gotoCrudIndex()
            ->with('status', "Admission year {$admissionYear->title} created successfully.");
    }

    public function show(AdmissionYear $admissionYear)
    {
        return view($this->showResource(), $this->crudInfo() + ['item' => $admissionYear]);
    }

    public function edit(AdmissionYear $admissionYear)
    {
        return view($this->editResource(), $this->crudInfo() + $this->yearFormOptions() + ['item' => $admissionYear, 'wide' => true]);
    }

    public function update(AdmissionYearRequest $request, AdmissionYear $admissionYear)
    {
        $admissionYear->update($request->safe()->only(['title', 'year', 'intake']));

        if ($request->boolean('is_active')) {
            $admissionYear->activate();
        }

        return $this->gotoCrudIndex()
            ->with('status', "Admission year {$admissionYear->title} updated successfully.");
    }

    /**
     * Save a single cell edited straight from the index table.
     */
    public function inlineUpdate(Request $request, AdmissionYear $admissionYear): JsonResponse
    {
        $field = (string) $request->input('field');

        if (! in_array($field, self::INLINE_EDITABLE, true)) {
            return $this->sendError('This field cannot be edited inline.', [], 422);
        }

        $rules = [
            'title' => ['required', 'string', 'max:255', Rule::unique('admission_years', 'title')->ignore($admissionYear->getKey())],
            'year' => ['required', 'digits:4', 'integer', 'between:2000,2100'],
        ];

        $validator = Validator::make(
            [$field => $request->input('value')],
            [$field => $rules[$field]],
            [],
            ['title' => 'admission year title', 'year' => 'starting year']
        );

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first($field), $validator->errors()->toArray(), 422);
        }

        $admissionYear->update([$field => $validator->validated()[$field]]);

        return $this->returnSuccess([
            'field' => $field,
            'value' => $admissionYear->{$field},
            'message' => 'Saved.',
        ]);
    }

    public function destroy(Request $request, AdmissionYear $admissionYear)
    {
        if ($admissionYear->is_active) {
            $message = 'The active admission year cannot be deleted. Activate another year first.';

            return $request->expectsJson()
                ? $this->sendError($message, [], 422)
                : redirect()->back()->withErrors($message);
        }

        if ($admissionYear->students()->exists()) {
            $message = 'This admission year has enrolled students and cannot be deleted.';

            return $request->expectsJson()
                ? $this->sendError($message, [], 422)
                : redirect()->back()->withErrors($message);
        }

        $title = $admissionYear->title;
        $admissionYear->delete();

        return $request->expectsJson()
            ? $this->returnSuccess(['message' => "Admission year {$title} deleted."])
            : $this->gotoCrudIndex()->with('status', "Admission year {$title} deleted.");
    }

    /**
     * Show the admission year setup form.
     */
    public function showAdmissionYearSetup()
    {
        return view($this->resources.'setup', $this->crudInfo() + $this->yearFormOptions());
    }

    /**
     * Store the first / active admission year from the setup screen.
     */
    public function storeAdmissionYear(AdmissionYearRequest $request)
    {
        $admissionYear = AdmissionYear::create($request->safe()->only(['title', 'year', 'intake']) + ['is_active' => false]);
        $admissionYear->activate();

        return redirect()->route('admin.dashboard')->with('status', 'Admission year set up successfully.');
    }

    /**
     * Activate an existing admission year and deactivate all others.
     */
    public function activateAdmissionYear(Request $request, AdmissionYear $admissionYear)
    {
        $admissionYear->activate();

        $message = "Admission year {$admissionYear->title} is now active.";

        return $request->expectsJson()
            ? $this->returnSuccess(['message' => $message])
            : redirect()->back()->with('status', $message);
    }

    /**
     * Options shared by the create/edit/setup forms: the picklist of
     * intakes and a sensible range of selectable starting years.
     *
     * @return array<string, mixed>
     */
    private function yearFormOptions(): array
    {
        $currentYear = (int) date('Y');

        return [
            'intakes' => AdmissionYear::INTAKES,
            'years' => range($currentYear - 5, $currentYear + 2),
        ];
    }
}
