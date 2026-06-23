<?php

namespace Modules\Department\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Department\Services\DepartmentService;
use Modules\Department\Http\Requests\StoreDepartmentRequest;
use Modules\Department\Http\Requests\UpdateDepartmentRequest;
use Illuminate\Http\Request;
use Modules\Branch\Models\Branch;
use Modules\Company\Models\Company;
use Modules\Department\Models\Department;

class DepartmentController extends Controller
{
    protected $departmentService;

    public function __construct(DepartmentService $departmentService)
    {
        $this->departmentService = $departmentService;
    }

    /**
     * Display department listing page
     */
    public function index(Request $request)
    {   
        $companies = Company::all();
        $branches = Branch::all();
        $departments = Department::all();
        return view('department::index', compact('companies', 'branches', 'departments'));
    }

    /**
     * Get department data for DataTable AJAX
     */
    public function dataTable(Request $request)
    {
        return $this->departmentService->getDepartmentDataTable($request);
    }


    /**
     * Store new department
     */
    public function store(StoreDepartmentRequest $request)
    {
        $result = $this->departmentService->saveDepartment($request->validated());
        return response()->json($result);
    }

    /**
     * Get single department by ID
     */
    public function show($id)
    {
        $result = $this->departmentService->getDepartmentById($id);
        return response()->json($result);
    }


    /**
     * Update existing department
     */
    public function update(UpdateDepartmentRequest $request, $id)
    {
        $data = $request->validated();
        $data['department_id'] = $id;

        $result = $this->departmentService->saveDepartment($data);
        return response()->json($result);
    }

    /**
     * Delete department
     */
    public function destroy($id)
    {
        $result = $this->departmentService->deleteDepartment($id);
        return response()->json($result);
    }
}
