<?php

namespace Modules\Loan\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Loan\StoreLoanRequest;
use Illuminate\Http\Request;
use Modules\Employee\Models\Employee;
use Modules\Loan\Models\Loan;
use Modules\Loan\Services\LoanService;

class LoanController extends Controller
{
    protected LoanService $loanService;

    public function __construct(LoanService $loanService)
    {
        $this->loanService = $loanService;
    }

    public function index()
    {
        $statistics = $this->loanService->getLoanStatistics();
        $employees = Employee::with('personalInfo')->active()->get();

        return view('loan::index', compact('statistics', 'employees'));
    }

    public function dataTable(Request $request)
    {
        return $this->loanService->getLoanDataTable($request);
    }

    public function create()
    {
        $employee = auth()->user()->employee;
        if (! $employee) {
            return redirect()->back()->with('error', 'No employee profile found.');
        }

        $loanSummary = $this->loanService->getEmployeeLoanSummary($employee->id);

        return view('loan::create', compact('employee', 'loanSummary'));
    }

    public function store(StoreLoanRequest $request)
    {
        $employee = auth()->user()->employee;
        if (! $employee) {
            return redirect()->back()->with('error', 'No employee profile found.')->withInput();
        }

        $validated = $request->validated();
        $validated['employee_id'] = $employee->id;
        $validated['created_by'] = auth()->id();
        $validated['application_date'] = now()->format('Y-m-d');
        $validated['interest_rate'] = $validated['interest_rate'] ?? 0;

        $result = $this->loanService->saveLoan($validated);

        if ($result['status'] === 'success') {
            return redirect()->route('loan.my')->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message'])->withInput();
    }

    public function show($id)
    {
        $result = $this->loanService->getLoanById($id);
        if ($result['status'] === 'error') {
            return redirect()->route('loan.index')->with('error', $result['message']);
        }

        return view('loan::show', [
            'loan' => $result['loan'],
            'summary' => $result['summary'],
        ]);
    }

    public function edit($id)
    {
        $result = $this->loanService->getLoanById($id);
        if ($result['status'] === 'error') {
            return redirect()->route('loan.index')->with('error', $result['message']);
        }

        $loan = $result['loan'];
        if (! in_array($loan->status, ['Pending', 'Rejected'])) {
            return redirect()->route('loan.show', $id)->with('error', 'Only pending or rejected loans can be edited.');
        }

        $employees = Employee::with('personalInfo')->active()->get();

        return view('loan::edit', compact('loan', 'employees'));
    }

    public function update(StoreLoanRequest $request, $id)
    {
        $employee = auth()->user()->employee;
        if (! $employee) {
            return redirect()->back()->with('error', 'No employee profile found.')->withInput();
        }

        $validated = $request->validated();
        $validated['loan_id'] = $id;
        $validated['employee_id'] = $employee->id;
        $validated['interest_rate'] = $validated['interest_rate'] ?? 0;

        $result = $this->loanService->saveLoan($validated);

        if ($result['status'] === 'success') {
            return redirect()->route('loan.show', $id)->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message'])->withInput();
    }

    public function destroy(Request $request, $id)
    {
        if (! auth()->user()->can('manage-loans')) {
            if ($request->ajax()) {
                return response()->json(['status' => 'error', 'message' => 'You are not authorized to delete loans.']);
            }

            return redirect()->back()->with('error', 'You are not authorized to delete loans.');
        }

        $result = $this->loanService->deleteLoan($id);

        if ($request->ajax()) {
            return response()->json($result);
        }

        if ($result['status'] === 'success') {
            return redirect()->route('loan.index')->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }

    public function approve($id)
    {
        if (! auth()->user()->can('manage-loans')) {
            if (request()->ajax()) {
                return response()->json(['status' => 'error', 'message' => 'You are not authorized to approve loans.']);
            }

            return redirect()->back()->with('error', 'You are not authorized to approve loans.');
        }

        $result = $this->loanService->approveLoan($id);

        if (request()->ajax()) {
            return response()->json($result);
        }

        if ($result['status'] === 'success') {
            return redirect()->route('loan.show', $id)->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }

    public function reject(Request $request, $id)
    {
        if (! auth()->user()->can('manage-loans')) {
            if ($request->ajax()) {
                return response()->json(['status' => 'error', 'message' => 'You are not authorized to reject loans.']);
            }

            return redirect()->back()->with('error', 'You are not authorized to reject loans.');
        }

        $reason = $request->input('rejection_reason');
        $result = $this->loanService->rejectLoan($id, $reason);

        if ($request->ajax()) {
            return response()->json($result);
        }

        if ($result['status'] === 'success') {
            return redirect()->route('loan.index')->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }

    public function disburse($id)
    {
        if (! auth()->user()->can('manage-loans')) {
            if (request()->ajax()) {
                return response()->json(['status' => 'error', 'message' => 'You are not authorized to disburse loans.']);
            }

            return redirect()->back()->with('error', 'You are not authorized to disburse loans.');
        }

        $result = $this->loanService->disburseLoan($id);

        if (request()->ajax()) {
            return response()->json($result);
        }

        if ($result['status'] === 'success') {
            return redirect()->route('loan.show', $id)->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }

    public function myLoans()
    {
        $employee = auth()->user()->employee;
        if (! $employee) {
            return redirect()->back()->with('error', 'No employee profile found.');
        }

        $loanSummary = $this->loanService->getEmployeeLoanSummary($employee->id);

        return view('loan::my', compact('employee', 'loanSummary'));
    }

    public function myLoansDataTable(Request $request)
    {
        $employee = auth()->user()->employee;
        if (! $employee) {
            return response()->json(['data' => []]);
        }

        return $this->loanService->getMyLoanDataTable($request, $employee->id);
    }

    public function calculate(Request $request)
    {
        $amount = $request->input('amount', 0);
        $interestRate = $request->input('interest_rate', 0);
        $installments = $request->input('installments', 1);

        $calculations = Loan::calculatePayable($amount, $interestRate, $installments);

        return response()->json($calculations);
    }
}
