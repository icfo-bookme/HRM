<?php

namespace Modules\Employee\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Employee\Services\EmployeeWeekendService;

class EmployeeWeekendController extends Controller
{
    protected $weekendService;

    public function __construct(EmployeeWeekendService $weekendService)
    {
        $this->weekendService = $weekendService;
    }

    public function index(Request $request)
    {
        $search = $request->get('search');
        $employees = $this->weekendService->getPaginatedEmployees($request);

        if ($request->ajax()) {
            return response()->json($this->weekendService->prepareAjaxResponse($request));
        }

        return view('employee::weekends.index', compact('employees', 'search'));
    }

    public function store(Request $request)
    {
        $result = $this->weekendService->storeWeekend($request);

        if ($result['status'] === 'success') {
            return response()->json($result);
        }

        return response()->json($result, 500);
    }

    public function show($employeeId)
    {
        return $this->weekendService->getWeekendByEmployeeId($employeeId);
    }
}
