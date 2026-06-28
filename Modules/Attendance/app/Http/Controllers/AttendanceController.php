<?php

namespace Modules\Attendance\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Attendance\Http\Requests\StoreAttendanceRequest;
use Modules\Attendance\Models\Attendance;
use Modules\Attendance\Services\AttendanceService;
use Modules\Employee\Models\Employee;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function __construct(
        protected AttendanceService $attendanceService
    ) {}

    public function index()
    {
        $employees = Employee::with('personalInfo')->active()->get();
        return view('attendance::index', compact('employees'));
    }

    public function edit($id)
    {
        $attendance = Attendance::with('employee.personalInfo')->findOrFail($id);
        $employees = Employee::with('personalInfo')->active()->get();
        $today = $attendance->attendance_date->format('Y-m-d');

        return view('attendance::create', [
            'employee' => $attendance->employee,
            'today' => $today,
            'defaultCheckIn' => $attendance->check_in_at ? \Carbon\Carbon::parse($attendance->check_in_at)->format('H:i') : now()->format('H:i'),
            'employees' => $employees,
            'todayAttendance' => $attendance,
        ]);
    }

    public function create(Request $request)
    {
        $today = Carbon::today()->format('Y-m-d');
        $defaultCheckIn = Carbon::now()->format('H:i');
        $employees = Employee::with('personalInfo')->active()->get();

        $employee = $request->employee_id
            ? Employee::with('personalInfo')->find($request->employee_id)
            : auth()->user()->employee;

        return view('attendance::create', [
            'employee' => $employee,
            'today' => $today,
            'defaultCheckIn' => $defaultCheckIn,
            'employees' => $employees,
            'todayAttendance' => $employee
                ? Attendance::where('employee_id', $employee->id)->whereDate('attendance_date', $today)->first()
                : null,
        ]);
    }

    public function dataTable(Request $request)
    {
        return $this->attendanceService->getAttendanceDataTable($request);
    }

    public function show($id): JsonResponse
    {
        return response()->json($this->attendanceService->getAttendanceById($id));
    }

    public function store(StoreAttendanceRequest $request): JsonResponse
    {
        $result = $this->attendanceService->saveManualAttendance($request->validated());

        return response()->json([
            'status' => $result['status'],
            'message' => $result['message'],
            'data' => $result['attendance'] ?? null,
        ]);
    }

    public function update(StoreAttendanceRequest $request, $id): JsonResponse
    {
        $result = $this->attendanceService->saveManualAttendance($request->validated(), $id);

        return response()->json([
            'status' => $result['status'],
            'message' => $result['message'],
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $result = $this->attendanceService->deleteAttendance($id);

        return response()->json([
            'status' => $result['status'],
            'message' => $result['message'],
        ]);
    }

    public function approve($id): JsonResponse
    {
        $result = $this->attendanceService->approveAttendance($id);

        return response()->json([
            'status' => $result['status'],
            'message' => $result['message'],
        ]);
    }

    public function disapprove($id): JsonResponse
    {
        $result = $this->attendanceService->disapproveAttendance($id);

        return response()->json([
            'status' => $result['status'],
            'message' => $result['message'],
        ]);
    }
}
