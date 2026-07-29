<?php

namespace Modules\Department\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Branch\Models\Branch;
use Modules\Company\Models\Company;
use Modules\Department\Http\Requests\StoreDepartmentRequest;
use Modules\Department\Http\Requests\UpdateDepartmentRequest;
use Modules\Department\Models\Department;
use Modules\Department\Services\DepartmentService;

class DepartmentController extends Controller
{
    protected DepartmentService $departmentService;

    public function __construct(DepartmentService $departmentService)
    {
        $this->departmentService = $departmentService;
    }

    public function index(Request $request)
    {
        $companies = Company::all();
        $branches = Branch::all();
        $departments = Department::all();

        return view('department::index', compact('companies', 'branches', 'departments'));
    }

    public function dataTable(Request $request)
    {
        return $this->departmentService->getDepartmentDataTable($request);
    }

    public function store(StoreDepartmentRequest $request)
    {
        $result = $this->departmentService->saveDepartment($request->validated());

        return response()->json($result);
    }

    public function show($id)
    {
        $result = $this->departmentService->getDepartmentById($id);

        return response()->json($result);
    }

    public function update(UpdateDepartmentRequest $request, $id)
    {
        $data = $request->validated();
        $data['department_id'] = $id;

        $result = $this->departmentService->saveDepartment($data);

        return response()->json($result);
    }

    public function destroy($id)
    {
        $result = $this->departmentService->deleteDepartment($id);

        return response()->json($result);
    }
}
