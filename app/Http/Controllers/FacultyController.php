<?php

namespace App\Http\Controllers;

use App\Http\Requests\FacultyRequest;
use App\Models\Faculty;
use App\Models\StudentQualification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class FacultyController extends BaseController
{
    public function __construct()
    {
        $this->title = 'Faculty';
        $this->subTitle = 'Faculty Management';
        $this->resources = 'admin.faculties.';
        $this->icon = 'heroicon-o-academic-cap';
        $this->route = 'admin.faculties.';
        $this->description = 'Manage the faculties offered on the student enrollment form\'s Academic Qualifications rows.';
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
            'items' => Faculty::orderBy('name')->get(),
        ]);
    }

    public function store(FacultyRequest $request)
    {
        $faculty = Faculty::create($request->validated());

        if ($request->expectsJson()) {
            return $this->returnSuccess([
                'id' => $faculty->getKey(),
                'name' => $faculty->name,
                'row' => view('admin.faculties.partials.row', ['item' => $faculty])->render(),
                'message' => "Faculty {$faculty->name} added.",
            ]);
        }

        return $this->gotoCrudIndex()
            ->with('status', "Faculty {$faculty->name} created successfully.");
    }

    /**
     * Save the name edited straight from the index table.
     */
    public function inlineUpdate(Request $request, Faculty $faculty): JsonResponse
    {
        $validator = Validator::make(
            ['name' => $request->input('value')],
            ['name' => ['required', 'string', 'max:255', Rule::unique('faculties', 'name')->ignore($faculty->getKey())]],
            [],
            ['name' => 'faculty']
        );

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first('name'), $validator->errors()->toArray(), 422);
        }

        $faculty->update(['name' => $validator->validated()['name']]);

        return $this->returnSuccess([
            'value' => $faculty->name,
            'message' => 'Saved.',
        ]);
    }

    public function destroy(Request $request, Faculty $faculty)
    {
        if (StudentQualification::where('faculty', $faculty->name)->exists()) {
            $message = 'This faculty is used by existing qualifications and cannot be deleted.';

            return $request->expectsJson()
                ? $this->sendError($message, [], 422)
                : redirect()->back()->withErrors($message);
        }

        $name = $faculty->name;
        $faculty->delete();

        return $request->expectsJson()
            ? $this->returnSuccess(['message' => "Faculty {$name} deleted."])
            : $this->gotoCrudIndex()->with('status', "Faculty {$name} deleted.");
    }
}
