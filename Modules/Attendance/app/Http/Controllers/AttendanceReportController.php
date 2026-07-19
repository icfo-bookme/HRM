<?php

namespace Modules\Attendance\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Attendance\Services\AttendanceReportService;
use Modules\Employee\Models\Employee;

class AttendanceReportController extends Controller
{
    public function __construct(
        protected AttendanceReportService $reportService
    ) {}

    public function index()
    {
        $employees = Employee::with('personalInfo')->active()->get();
        return view('attendance::report', compact('employees'));
    }

    public function dataTable(Request $request): JsonResponse
    {
        return $this->reportService->getMonthlyReportDataTable($request);
    }

    public function overtimeIndex()
    {
        $employees = Employee::with('personalInfo')->active()->get();
        return view('attendance::overtime-report', compact('employees'));
    }

    public function overtimeDataTable(Request $request): JsonResponse
    {
        return $this->reportService->getOvertimeReportDataTable($request);
    }
}
