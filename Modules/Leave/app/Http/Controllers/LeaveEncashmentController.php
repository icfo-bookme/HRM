<?php

namespace Modules\Leave\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Leave\Services\LeaveEncashmentService;
use Modules\Leave\Http\Requests\StoreLeaveEncashmentRequest;
use Modules\Leave\Http\Requests\UpdateLeaveEncashmentRequest;
use Modules\Employee\Models\Employee;
use Modules\Leave\Models\LeaveType;
use Illuminate\Http\Request;

class LeaveEncashmentController extends Controller
{
    protected LeaveEncashmentService $leaveEncashmentService;

    public function __construct(LeaveEncashmentService $leaveEncashmentService)
    {
        $this->leaveEncashmentService = $leaveEncashmentService;
    }

    public function index()
    {
        $employees  = Employee::with('personalInfo')->orderBy('id')->get(['id', 'employee_code']);
        $leaveTypes = LeaveType::where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('leave::leave-encashment.index', compact('employees', 'leaveTypes'));
    }

    public function create()
    {
        $employees  = Employee::with('personalInfo')->orderBy('id')->get(['id', 'employee_code']);
        $leaveTypes = LeaveType::where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('leave::leave-encashment.create', compact('employees', 'leaveTypes'));
    }

    public function edit($id)
    {
        $result = $this->leaveEncashmentService->getLeaveEncashmentById($id);

        if ($result['status'] !== 'success') {
            return redirect()->route('leave-encashment.index')
                ->with('error', $result['message']);
        }

        $encashment = $result['data'];
        $employees  = Employee::with('personalInfo')->orderBy('id')->get(['id', 'employee_code']);
        $leaveTypes = LeaveType::where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('leave::leave-encashment.edit', compact('encashment', 'employees', 'leaveTypes'));
    }

    public function dataTable(Request $request)
    {
        return $this->leaveEncashmentService->getLeaveEncashmentDataTable($request);
    }

    public function store(StoreLeaveEncashmentRequest $request)
    {
        $result = $this->leaveEncashmentService->saveLeaveEncashment($request->validated());
        return response()->json($result);
    }

    public function show($id)
    {
        $result = $this->leaveEncashmentService->getLeaveEncashmentById($id);
        return response()->json($result);
    }

    public function update(UpdateLeaveEncashmentRequest $request, $id)
    {
        $data = $request->validated();
        $data['encashment_id'] = $id;

        $result = $this->leaveEncashmentService->saveLeaveEncashment($data);
        return response()->json($result);
    }

    public function destroy($id)
    {
        $result = $this->leaveEncashmentService->deleteLeaveEncashment($id);
        return response()->json($result);
    }

    public function approve(Request $request, $id)
    {
        $approvedBy = auth()->id();
        $result = $this->leaveEncashmentService->approve($id, $approvedBy);
        return response()->json($result);
    }

    public function getBalance(Request $request)
    {
        $employeeId  = $request->input('employee_id');
        $leaveTypeId = $request->input('leave_type_id');
        $remaining   = $this->leaveEncashmentService->getRemainingBalance($employeeId, $leaveTypeId);

        return response()->json([
            'remaining_days' => $remaining,
        ]);
    }
}