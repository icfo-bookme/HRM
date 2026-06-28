<?php

namespace Modules\Employee\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Department\Models\Department;
use Modules\Designation\Models\Designation;
use Modules\Employee\Services\EmployeeReportService;

class EmployeeReportController extends Controller
{
    public function __construct(
        protected EmployeeReportService $reportService
    ) {}

    public function index()
    {
        $departments = Department::all();
        $designations = Designation::all();
        return view('employee::employee-report', compact('departments', 'designations'));
    }

    public function searchEmployee(Request $request): JsonResponse
    {
        $employee = $this->reportService->searchEmployee($request);
        return response()->json($employee);
    }

    public function attendanceData(Request $request): JsonResponse
    {
        $data = $this->reportService->getAttendanceCalendar($request);
        return response()->json($data);
    }

    public function overtimeData(Request $request): JsonResponse
    {
        $data = $this->reportService->getOvertimeHistory($request);
        return response()->json($data);
    }

    public function salaryData(Request $request): JsonResponse
    {
        $data = $this->reportService->getSalaryHistory($request);
        return response()->json($data);
    }

    public function kpiData(Request $request): JsonResponse
    {
        $data = $this->reportService->getKpiHistory($request);
        return response()->json($data);
    }

    public function loanData(Request $request): JsonResponse
    {
        $data = $this->reportService->getLoanHistory($request);
        return response()->json($data);
    }

    public function monthlyKpiHistory(Request $request): JsonResponse
    {
        $data = $this->reportService->getMonthlyKpiHistory($request);
        return response()->json($data);
    }

    public function monthlySalaryData(Request $request): JsonResponse
    {
        $data = $this->reportService->getMonthlySalary($request);
        return response()->json($data);
    }
}
