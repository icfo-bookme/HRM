<?php

namespace Modules\Employee\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Employee\Models\Employee;
use Modules\Employee\Services\EmployeeLeaveBalanceService;
use Modules\Employee\Http\Requests\StoreEmployeeLeaveBalanceRequest;
use Modules\Employee\Http\Requests\UpdateEmployeeLeaveBalanceRequest;
use Modules\Leave\Models\LeaveType;
use Illuminate\Http\Request;
use Modules\Setting\Models\FiscalYear;

class EmployeeLeaveBalanceController extends Controller
{
    protected EmployeeLeaveBalanceService $employeeLeaveBalanceService;

    public function __construct(EmployeeLeaveBalanceService $employeeLeaveBalanceService)
    {
        $this->employeeLeaveBalanceService = $employeeLeaveBalanceService;
    }

    /**
     * Display leave balance listing page
     */
    public function index()
    {
        $employees    = Employee::with('personalInfo')->orderBy('id')->get(['id', 'employee_code']);
        $leaveTypes   = LeaveType::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $fiscalYears  = FiscalYear::all(); // No FiscalYear module exists yet

        return view('employee::leave-balance.index', compact('employees', 'leaveTypes', 'fiscalYears'));
    }

    /**
     * Get leave balance data for DataTable AJAX
     */
    public function dataTable(Request $request)
    {
        return $this->employeeLeaveBalanceService->getEmployeeLeaveBalanceDataTable($request);
    }

    /**
     * Store new leave balance record
     */
    public function store(StoreEmployeeLeaveBalanceRequest $request)
    {
        $result = $this->employeeLeaveBalanceService->saveEmployeeLeaveBalance($request->validated());
        return response()->json($result);
    }

    /**
     * Get single leave balance record by ID
     */
    public function show($id)
    {
        $result = $this->employeeLeaveBalanceService->getEmployeeLeaveBalanceById($id);
        return response()->json($result);
    }

    /**
     * Update existing leave balance record
     */
    public function update(UpdateEmployeeLeaveBalanceRequest $request, $id)
    {
        $data = $request->validated();
        $data['balance_id'] = $id;

        $result = $this->employeeLeaveBalanceService->saveEmployeeLeaveBalance($data);
        return response()->json($result);
    }

    /**
     * Delete leave balance record
     */
    public function destroy($id)
    {
        $result = $this->employeeLeaveBalanceService->deleteEmployeeLeaveBalance($id);
        return response()->json($result);
    }
}