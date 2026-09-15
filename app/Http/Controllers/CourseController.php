<?php

namespace App\Http\Controllers;

use App\DataTables\CourseDataTable;
use App\Http\Requests\CourseRequest;
use App\Models\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CourseController extends BaseController
{
    /**
     * Fields that may be changed straight from the index table.
     */
    private const INLINE_EDITABLE = ['title', 'credits', 'description'];

    public function __construct()
    {
        $this->title = 'Course';
        $this->subTitle = 'Course Management';
        $this->resources = 'admin.courses.';
        $this->icon = 'heroicon-o-book-open';
        $this->route = 'admin.courses.';
        $this->description = 'Manage the courses offered by the college.';
        parent::__construct();
    }

    /**
     * Index backed by a server side DataTable (search, sort, paginate, export).
     */
    public function index(CourseDataTable $dataTable)
    {
        return $dataTable->render($this->indexResource(), $this->crudInfo() + [
            'add_button_name' => 'Add Course',
        ]);
    }

    public function create()
    {
        return view($this->createResource(), $this->crudInfo() + ['wide' => true]);
    }

    public function store(CourseRequest $request)
    {
        $course = Course::create($request->safe()->only(['title', 'display_title', 'credits', 'description']) + [
            'levels' => $request->levelsArray(),
        ]);

        return $this->gotoCrudIndex()
            ->with('status', "Course {$course->title} created successfully.");
    }

    public function show(Course $course)
    {
        return view($this->showResource(), $this->crudInfo() + ['item' => $course]);
    }

    public function edit(Course $course)
    {
        return view($this->editResource(), $this->crudInfo() + ['item' => $course, 'wide' => true]);
    }

    public function update(CourseRequest $request, Course $course)
    {
        $course->update($request->safe()->only(['title', 'display_title', 'credits', 'description']) + [
            'levels' => $request->levelsArray(),
        ]);

        return $this->gotoCrudIndex()
            ->with('status', "Course {$course->title} updated successfully.");
    }

    /**
     * Save a single cell edited straight from the index table.
     */
    public function inlineUpdate(Request $request, Course $course): JsonResponse
    {
        $field = (string) $request->input('field');

        if (! in_array($field, self::INLINE_EDITABLE, true)) {
            return $this->sendError('This field cannot be edited inline.', [], 422);
        }

        $rules = [
            'title' => ['required', 'string', 'max:255', Rule::unique('courses', 'title')->ignore($course->getKey())],
            'credits' => ['nullable', 'numeric', 'min:0', 'max:20'],
            'description' => ['nullable', 'string', 'max:2000'],
        ];

        // An empty string means "clear it" for the optional fields.
        $value = $request->input('value');
        if ($value === '' && $field !== 'title') {
            $value = null;
        }

        $validator = Validator::make(
            [$field => $value],
            [$field => $rules[$field]],
            [],
            ['title' => 'course title', 'credits' => 'credits', 'description' => 'description']
        );

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first($field), $validator->errors()->toArray(), 422);
        }

        $course->update([$field => $validator->validated()[$field]]);

        $display = $field === 'credits'
            ? ($course->credits === null ? '' : rtrim(rtrim((string) $course->credits, '0'), '.'))
            : (string) $course->{$field};

        return $this->returnSuccess([
            'field' => $field,
            'value' => $display,
            'message' => 'Saved.',
        ]);
    }

    public function destroy(Request $request, Course $course)
    {
        if ($course->students()->exists()) {
            $message = 'This course has enrolled students and cannot be deleted.';

            return $request->expectsJson()
                ? $this->sendError($message, [], 422)
                : redirect()->back()->withErrors($message);
        }

        $title = $course->title;
        $course->delete();

        return $request->expectsJson()
            ? $this->returnSuccess(['message' => "Course {$title} deleted."])
            : $this->gotoCrudIndex()->with('status', "Course {$title} deleted.");
    }
}
