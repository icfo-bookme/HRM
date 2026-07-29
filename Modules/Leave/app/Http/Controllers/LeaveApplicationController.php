<?php

namespace Modules\Leave\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Employee\Models\Employee;
use Modules\Leave\Http\Requests\StoreLeaveApplicationRequest;
use Modules\Leave\Http\Requests\UpdateLeaveApplicationRequest;
use Modules\Leave\Models\LeaveType;
use Modules\Leave\Services\LeaveApplicationService;

class LeaveApplicationController extends Controller
{
    protected LeaveApplicationService $leaveApplicationService;

    public function __construct(LeaveApplicationService $leaveApplicationService)
    {
        $this->leaveApplicationService = $leaveApplicationService;
    }

    public function index()
    {
        $employees = Employee::with('personalInfo')->orderBy('id')->get(['id', 'employee_code']);
        $leaveTypes = LeaveType::where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('leave::leave-applications.index', compact('employees', 'leaveTypes'));
    }

    public function create()
    {
        $employees = Employee::with('personalInfo')->orderBy('id')->get(['id', 'employee_code']);
        $leaveTypes = LeaveType::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $loggedInEmployeeId = auth()->user()->employee_id;

        return view('leave::leave-applications.create', compact('employees', 'leaveTypes', 'loggedInEmployeeId'));
    }

    public function edit($id)
    {
        $result = $this->leaveApplicationService->getLeaveApplicationById($id);

        if ($result['status'] !== 'success') {
            return redirect()->route('leave-applications.index')
                ->with('error', $result['message']);
        }

        $application = $result['data'];
        $employees = Employee::with('personalInfo')->orderBy('id')->get(['id', 'employee_code']);
        $leaveTypes = LeaveType::where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('leave::leave-applications.edit', compact('application', 'employees', 'leaveTypes'));
    }

    public function my()
    {
        return view('leave::leave-applications.my');
    }

    public function myDataTable(Request $request)
    {
        $request->merge(['employee_id' => Auth::user()->employee_id]);

        return $this->leaveApplicationService->getLeaveApplicationDataTable($request, false);
    }

    public function dataTable(Request $request)
    {
        return $this->leaveApplicationService->getLeaveApplicationDataTable($request);
    }

    public function store(StoreLeaveApplicationRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('document_file')) {
            $path = $request->file('document_file')->store('leave-applications/documents', 'public');
            $data['document_path'] = $path;
        }
        unset($data['document_file']);

        $result = $this->leaveApplicationService->saveLeaveApplication($data);

        if ($result['status'] === 'success') {
            return redirect()->route('leave-applications.my')
                ->with('success', $result['message']);
        }

        return redirect()->back()
            ->withInput()
            ->with('error', $result['message']);
    }

    public function show($id)
    {
        $result = $this->leaveApplicationService->getLeaveApplicationById($id);

        return response()->json($result);
    }

    public function update(UpdateLeaveApplicationRequest $request, $id)
    {
        $data = $request->validated();
        $data['application_id'] = $id;

        if ($request->hasFile('document_file')) {
            $path = $request->file('document_file')->store('leave-applications/documents', 'public');
            $data['document_path'] = $path;
        }
        unset($data['document_file']);

        $result = $this->leaveApplicationService->saveLeaveApplication($data);

        return response()->json($result);
    }

    public function destroy($id)
    {
        $result = $this->leaveApplicationService->deleteLeaveApplication($id);

        return response()->json($result);
    }

    public function checkApprovalBalance(Request $request, $id)
    {
        $result = $this->leaveApplicationService->checkApprovalBalance($id);

        return response()->json($result);
    }

    public function approve(Request $request, $id)
    {
        $approvedBy = auth()->id();
        $result = $this->leaveApplicationService->approve($id, $approvedBy);

        return response()->json($result);
    }

    public function disapprove(Request $request, $id)
    {
        $result = $this->leaveApplicationService->disapprove($id);

        return response()->json($result);
    }
}
