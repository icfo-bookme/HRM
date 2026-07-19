<?php

namespace Modules\Salary\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Salary\Services\EmployeeSalaryStructureService;
use Modules\Salary\Http\Requests\StoreEmployeeSalaryStructureRequest;
use Illuminate\Http\Request;
use Modules\Employee\Models\Employee;
use Modules\Salary\Models\SalaryComponent;

class EmployeeSalaryStructureController extends Controller
{
    protected $employeeSalaryStructureService;

    public function __construct(EmployeeSalaryStructureService $employeeSalaryStructureService)
    {
        $this->employeeSalaryStructureService = $employeeSalaryStructureService;
    }

    public function index(Request $request)
    {
        $employees = Employee::with('personalInfo')->get();
        $components = SalaryComponent::active()->ordered()->get();
        return view('salary::employee-salary-structure.index', compact('employees', 'components'));
    }

    /**
     * Get employee salary structure data for DataTable AJAX
     */
    public function dataTable(Request $request)
    {
        return $this->employeeSalaryStructureService->getEmployeeSalaryStructureDataTable($request);
    }

    /**
     * Store new employee salary structure
     */
    public function store(StoreEmployeeSalaryStructureRequest $request)
    {
        $result = $this->employeeSalaryStructureService->saveEmployeeSalaryStructure($request->validated());
        return response()->json($result);
    }

    /**
     * Get single employee salary structure by ID
     */
    public function show($id)
    {
        $result = $this->employeeSalaryStructureService->getEmployeeSalaryStructureById($id);
        return response()->json($result);
    }

    /**
     * Update existing employee salary structure
     */
    public function update(StoreEmployeeSalaryStructureRequest $request, $id)
    {
        $data = $request->validated();
        $data['structure_id'] = $id;

        $result = $this->employeeSalaryStructureService->saveEmployeeSalaryStructure($data);
        return response()->json($result);
    }

    /**
     * Delete employee salary structure
     */
    public function destroy($id)
    {
        $result = $this->employeeSalaryStructureService->deleteEmployeeSalaryStructure($id);
        return response()->json($result);
    }
}