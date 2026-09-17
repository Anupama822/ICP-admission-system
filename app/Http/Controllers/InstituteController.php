<?php

namespace App\Http\Controllers;

use App\Http\Requests\InstituteRequest;
use App\Models\Institute;
use App\Models\StudentQualification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class InstituteController extends BaseController
{
    public function __construct()
    {
        $this->title = 'Institute';
        $this->subTitle = 'Institute Management';
        $this->resources = 'admin.institutes.';
        $this->icon = 'heroicon-o-building-library';
        $this->route = 'admin.institutes.';
        $this->description = 'Manage the institutes/awarding bodies shared by the Highest Education and Academic Qualifications fields on the student enrollment form.';
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
            'items' => Institute::orderBy('name')->get(),
        ]);
    }

    public function store(InstituteRequest $request)
    {
        $institute = Institute::create($request->validated());

        if ($request->expectsJson()) {
            return $this->returnSuccess([
                'id' => $institute->getKey(),
                'name' => $institute->name,
                'row' => view('admin.institutes.partials.row', ['item' => $institute])->render(),
                'message' => "Institute {$institute->name} added.",
            ]);
        }

        return $this->gotoCrudIndex()
            ->with('status', "Institute {$institute->name} created successfully.");
    }

    /**
     * Save the name edited straight from the index table.
     */
    public function inlineUpdate(Request $request, Institute $institute): JsonResponse
    {
        $validator = Validator::make(
            ['name' => $request->input('value')],
            ['name' => ['required', 'string', 'max:255', Rule::unique('institutes', 'name')->ignore($institute->getKey())]],
            [],
            ['name' => 'institute']
        );

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first('name'), $validator->errors()->toArray(), 422);
        }

        $institute->update(['name' => $validator->validated()['name']]);

        return $this->returnSuccess([
            'value' => $institute->name,
            'message' => 'Saved.',
        ]);
    }

    public function destroy(Request $request, Institute $institute)
    {
        if (StudentQualification::where('institute_name', $institute->name)->exists()) {
            $message = 'This institute is used by existing qualifications and cannot be deleted.';

            return $request->expectsJson()
                ? $this->sendError($message, [], 422)
                : redirect()->back()->withErrors($message);
        }

        $name = $institute->name;
        $institute->delete();

        return $request->expectsJson()
            ? $this->returnSuccess(['message' => "Institute {$name} deleted."])
            : $this->gotoCrudIndex()->with('status', "Institute {$name} deleted.");
    }
}
