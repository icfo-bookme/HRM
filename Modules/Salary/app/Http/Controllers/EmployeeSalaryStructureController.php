<?php

namespace Modules\Salary\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Employee\Models\Employee;
use Modules\Salary\Http\Requests\StoreEmployeeSalaryStructureRequest;
use Modules\Salary\Models\SalaryComponent;
use Modules\Salary\Services\EmployeeSalaryStructureService;

class EmployeeSalaryStructureController extends Controller
{
    protected EmployeeSalaryStructureService $employeeSalaryStructureService;

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

    public function dataTable(Request $request)
    {
        return $this->employeeSalaryStructureService->getEmployeeSalaryStructureDataTable($request);
    }

    public function store(StoreEmployeeSalaryStructureRequest $request)
    {
        $result = $this->employeeSalaryStructureService->saveEmployeeSalaryStructure($request->validated());

        return response()->json($result);
    }

    public function show($id)
    {
        $result = $this->employeeSalaryStructureService->getEmployeeSalaryStructureById($id);

        return response()->json($result);
    }

    public function update(StoreEmployeeSalaryStructureRequest $request, $id)
    {
        $data = $request->validated();
        $data['structure_id'] = $id;

        $result = $this->employeeSalaryStructureService->saveEmployeeSalaryStructure($data);

        return response()->json($result);
    }

    public function destroy($id)
    {
        $result = $this->employeeSalaryStructureService->deleteEmployeeSalaryStructure($id);

        return response()->json($result);
    }
}
