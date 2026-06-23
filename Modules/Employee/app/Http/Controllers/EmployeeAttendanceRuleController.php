<?php

namespace Modules\Employee\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Employee\Services\EmployeeAttendanceRuleService;

class EmployeeAttendanceRuleController extends Controller
{
    protected $attendanceRuleService;

    public function __construct(EmployeeAttendanceRuleService $attendanceRuleService)
    {
        $this->attendanceRuleService = $attendanceRuleService;
    }

    public function index(Request $request)
    {
        $search = $request->get('search');
        $employees = $this->attendanceRuleService->getPaginatedEmployees($request);

        if ($request->ajax()) {
            return response()->json($this->attendanceRuleService->prepareAjaxResponse($request));
        }

        return view('employee::attendance-rules.index', compact('employees', 'search'));
    }

    public function store(Request $request)
    {
        $result = $this->attendanceRuleService->storeRule($request);

        if ($result['status'] === 'success') {
            return response()->json($result);
        }

        return response()->json($result, 500);
    }

    public function show($employeeId)
    {
        return $this->attendanceRuleService->getRuleByEmployeeId($employeeId);
    }
}
