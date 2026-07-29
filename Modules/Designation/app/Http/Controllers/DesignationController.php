<?php

namespace Modules\Designation\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Company\Models\Company;
use Modules\Department\Models\Department;
use Modules\Designation\Http\Requests\StoreDesignationRequest;
use Modules\Designation\Http\Requests\UpdateDesignationRequest;
use Modules\Designation\Services\DesignationService;
use Modules\SalaryGrade\Models\SalaryGrade;

class DesignationController extends Controller
{
    protected DesignationService $designationService;

    public function __construct(DesignationService $designationService)
    {
        $this->designationService = $designationService;
    }

    public function index(Request $request)
    {
        $companies = Company::all();
        $departments = Department::all();
        $grades = SalaryGrade::all();

        return view('designation::index', compact('companies', 'departments', 'grades'));
    }

    public function dataTable(Request $request)
    {
        return $this->designationService->getDesignationDataTable($request);
    }

    public function store(StoreDesignationRequest $request)
    {
        $result = $this->designationService->saveDesignation($request->validated());

        return response()->json($result);
    }

    public function show($id)
    {
        $result = $this->designationService->getDesignationById($id);

        return response()->json($result);
    }

    public function edit($id)
    {
        return redirect()->route('designation.index');
    }

    public function update(UpdateDesignationRequest $request, $id)
    {
        $data = $request->validated();
        $data['designation_id'] = $id;

        $result = $this->designationService->saveDesignation($data);

        return response()->json($result);
    }

    public function destroy($id)
    {
        $result = $this->designationService->deleteDesignation($id);

        return response()->json($result);
    }
}
