<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;


class AdmissionYearController extends BaseController
{

    public function __construct(){
        $this->title = 'Admission Year';
        $this->subTitle = 'Admission Year';
        $this->resources = 'admin.admissionYears.';
        $this->icon = 'fas fa-calendar';
        $this->route = "admission-year.";
        $this->description = "Create the Admission Year that will be active for the system.";
        parent::__construct();
        // $this->generateAllMiddlewareByPermission();
    }

    public function index(Request $request)
{
    $query = AdmissionYear::query();

    if ($request->filled('search')) {
        $query->where('year', 'like', '%'.$request->search.'%');
    }

    if ($request->filled('sort')) {
        $query->orderBy($request->sort, $request->get('dir', 'asc'));
    } else {
        $query->latest();
    }

    $admissionYears = $query->paginate($request->get('per_page', 10));

    if ($request->ajax()) {
        return view('admin.admissionYears._table', compact('admissionYears'))->render();
    }

    return view('admin.admissionYears.index', [
        'title' => 'Admission Year',
        'route' => 'admission-year.',
        'admissionYears' => $admissionYears,
        'exportRoutes' => [
            'pdf'   => 'admission-year.export.pdf',
            'excel' => 'admission-year.export.excel',
            'csv'   => 'admission-year.export.csv',
        ],
        // 'hideExport' => true, // uncomment to hide export button entirely
    ]);
}
        /**
     * Show the admission year setup form.
     */
    public function showAdmissionYearSetup()
    {
        return view($this->resources.'setup');
    }
    public function getAddmissionYearCreate()
    {
        $info = $this->crudInfo();
        return view($this->resources.'create', $info);
    }

    /**
     * Store the active admission year.
     */
    public function storeAdmissionYear(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255', 'unique:admission_years,title'],
            'year' => ['required', 'string', 'max:4'],
        ]);

        AdmissionYear::where('is_active', true)->update(['is_active' => false]);
        AdmissionYear::create($validated + ['is_active' => true]);

        return redirect()->route('admin.dashboard')->with('status', 'Admission year set up successfully.');
    }

    /**
     * Activate an existing admission year and deactivate all others.
     */
    public function activateAdmissionYear(AdmissionYear $admissionYear)
    {
        AdmissionYear::where('is_active', true)->update(['is_active' => false]);
        $admissionYear->update(['is_active' => true]);

        return redirect()->back()->with('status', 'Academic year ' . $admissionYear->title . ' is now active.');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
