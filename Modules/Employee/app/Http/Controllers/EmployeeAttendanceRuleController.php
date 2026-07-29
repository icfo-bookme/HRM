<?php

namespace Modules\Employee\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\StoreAttendanceRuleRequest;
use Illuminate\Http\Request;
use Modules\Employee\Services\EmployeeAttendanceRuleService;

class EmployeeAttendanceRuleController extends Controller
{
    protected EmployeeAttendanceRuleService $attendanceRuleService;

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

    public function store(StoreAttendanceRuleRequest $request)
    {
        $result = $this->attendanceRuleService->storeRule($request->validated());

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
