<?php

namespace Modules\Leave\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Leave\Services\LeaveApplicationService;
use Modules\Leave\Http\Requests\StoreLeaveApplicationRequest;
use Modules\Leave\Http\Requests\UpdateLeaveApplicationRequest;
use Modules\Employee\Models\Employee;
use Modules\Leave\Models\LeaveType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveApplicationController extends Controller
{
    protected LeaveApplicationService $leaveApplicationService;

    public function __construct(LeaveApplicationService $leaveApplicationService)
    {
        $this->leaveApplicationService = $leaveApplicationService;
    }

    /**
     * Display leave application listing page
     */
    public function index()
    {
        $employees  = Employee::with('personalInfo')->orderBy('id')->get(['id', 'employee_code']);
        $leaveTypes = LeaveType::where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('leave::leave-applications.index', compact('employees', 'leaveTypes'));
    }

    /**
     * Show the form for creating a new leave application.
     */
    public function create()
    {
        $employees  = Employee::with('personalInfo')->orderBy('id')->get(['id', 'employee_code']);
        $leaveTypes = LeaveType::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $loggedInEmployeeId = auth()->user()->employee_id;

        return view('leave::leave-applications.create', compact('employees', 'leaveTypes', 'loggedInEmployeeId'));
    }

    /**
     * Show the form for editing the specified leave application.
     */
    public function edit($id)
    {
        $result = $this->leaveApplicationService->getLeaveApplicationById($id);

        if ($result['status'] !== 'success') {
            return redirect()->route('leave-applications.index')
                ->with('error', $result['message']);
        }

        $application = $result['data'];
        $employees   = Employee::with('personalInfo')->orderBy('id')->get(['id', 'employee_code']);
        $leaveTypes  = LeaveType::where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('leave::leave-applications.edit', compact('application', 'employees', 'leaveTypes'));
    }

    /**
     * Show my leave applications (employee-specific)
     */
    public function my()
    {
        return view('leave::leave-applications.my');
    }

    /**
     * Get my leave application data for DataTable AJAX
     */
    public function myDataTable(Request $request)
    {
        $request->merge(['employee_id' => Auth::user()->employee_id]);
        return $this->leaveApplicationService->getLeaveApplicationDataTable($request, false);
    }

    /**
     * Get leave application data for DataTable AJAX
     */
    public function dataTable(Request $request)
    {
        return $this->leaveApplicationService->getLeaveApplicationDataTable($request);
    }

    /**
     * Store new leave application
     */
    public function store(StoreLeaveApplicationRequest $request)
    {
        $data = $request->validated();

        // Handle document file upload
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

    /**
     * Get single leave application by ID
     */
    public function show($id)
    {
        $result = $this->leaveApplicationService->getLeaveApplicationById($id);
        return response()->json($result);
    }

    /**
     * Update existing leave application
     */
    public function update(UpdateLeaveApplicationRequest $request, $id)
    {
        $data = $request->validated();
        $data['application_id'] = $id;

        // Handle document file upload for update
        if ($request->hasFile('document_file')) {
            $path = $request->file('document_file')->store('leave-applications/documents', 'public');
            $data['document_path'] = $path;
        }
        unset($data['document_file']);

        $result = $this->leaveApplicationService->saveLeaveApplication($data);
        return response()->json($result);
    }

    /**
     * Delete leave application
     */
    public function destroy($id)
    {
        $result = $this->leaveApplicationService->deleteLeaveApplication($id);
        return response()->json($result);
    }

    /**
     * Check leave balance before approving
     */
    public function checkApprovalBalance(Request $request, $id)
    {
        $result = $this->leaveApplicationService->checkApprovalBalance($id);
        return response()->json($result);
    }

    /**
     * Approve a leave application
     */
    public function approve(Request $request, $id)
    {
        $approvedBy = auth()->id();
        $result = $this->leaveApplicationService->approve($id, $approvedBy);

        return response()->json($result);
    }

    /**
     * Disapprove a leave application (revert from Approved back to Pending)
     */
    public function disapprove(Request $request, $id)
    {
        $result = $this->leaveApplicationService->disapprove($id);
        return response()->json($result);
    }
}
