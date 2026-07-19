<?php

namespace Modules\Designation\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Designation\Services\DesignationService;
use Modules\Designation\Http\Requests\StoreDesignationRequest;
use Modules\Designation\Http\Requests\UpdateDesignationRequest;
use Illuminate\Http\Request;
use Modules\Company\Models\Company;
use Modules\Department\Models\Department;
use Modules\SalaryGrade\Models\SalaryGrade;

class DesignationController extends Controller
{
    protected $designationService;

    public function __construct(DesignationService $designationService)
    {
        $this->designationService = $designationService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $companies   = Company::all();
        $departments = Department::all();
        $grades      = SalaryGrade::all();

        return view('designation::index', compact('companies', 'departments', 'grades'));
    }

    /**
     * Get designation data for DataTable.
     */
    public function dataTable(Request $request)
    {
        return $this->designationService->getDesignationDataTable($request);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDesignationRequest $request)
    {
        $result = $this->designationService->saveDesignation($request->validated());
        return response()->json($result);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $result = $this->designationService->getDesignationById($id);
        return response()->json($result);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return redirect()->route('designation.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDesignationRequest $request, $id)
    {
        $data = $request->validated();
        $data['designation_id'] = $id;

        $result = $this->designationService->saveDesignation($data);
        return response()->json($result);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $result = $this->designationService->deleteDesignation($id);
        return response()->json($result);
    }
}
