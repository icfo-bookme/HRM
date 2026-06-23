<?php

namespace Modules\Loan\Http\Controllers;

use App\Http\Controllers\Controller;
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

    /**
     * Display a listing of all loan applications (Admin view).
     */
    public function index()
    {
        $statistics = $this->loanService->getLoanStatistics();
        $employees = Employee::with('personalInfo')->active()->get();
        return view('loan::index', compact('statistics', 'employees'));
    }

    /**
     * DataTable for all loans (Admin)
     */
    public function dataTable(Request $request)
    {
        return $this->loanService->getLoanDataTable($request);
    }

    /**
     * Show the form for creating a new loan application (Employee).
     */
    public function create()
    {
        $employee = auth()->user()->employee;
        if (!$employee) {
            return redirect()->back()->with('error', 'No employee profile found.');
        }

        // Get active loans summary
        $loanSummary = $this->loanService->getEmployeeLoanSummary($employee->id);

        return view('loan::create', compact('employee', 'loanSummary'));
    }

    /**
     * Store a newly created loan application.
     */
    public function store(Request $request)
    {
        $employee = auth()->user()->employee;
        if (!$employee) {
            return redirect()->back()->with('error', 'No employee profile found.')->withInput();
        }

        $validated = $request->validate([
            'loan_type'         => 'required|in:Personal,Emergency,Education,Medical,Vehicle,Home,Other',
            'loan_amount'       => 'required|numeric|min:1',
            'total_installments' => 'required|integer|min:1|max:120',
            'interest_rate'     => 'nullable|numeric|min:0|max:100',
            'purpose'           => 'nullable|string|max:1000',
            'notes'             => 'nullable|string|max:1000',
        ]);

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

    /**
     * Display the specified loan application.
     */
    public function show($id)
    {
        $result = $this->loanService->getLoanById($id);
        if ($result['status'] === 'error') {
            return redirect()->route('loan.index')->with('error', $result['message']);
        }

        return view('loan::show', [
            'loan'    => $result['loan'],
            'summary' => $result['summary'],
        ]);
    }

    /**
     * Show the form for editing the specified loan application.
     */
    public function edit($id)
    {
        $result = $this->loanService->getLoanById($id);
        if ($result['status'] === 'error') {
            return redirect()->route('loan.index')->with('error', $result['message']);
        }

        $loan = $result['loan'];
        if (!in_array($loan->status, ['Pending', 'Rejected'])) {
            return redirect()->route('loan.show', $id)->with('error', 'Only pending or rejected loans can be edited.');
        }

        $employees = Employee::with('personalInfo')->active()->get();
        return view('loan::edit', compact('loan', 'employees'));
    }

    /**
     * Update the specified loan application.
     */
    public function update(Request $request, $id)
    {
        $employee = auth()->user()->employee;
        if (!$employee) {
            return redirect()->back()->with('error', 'No employee profile found.')->withInput();
        }

        $validated = $request->validate([
            'loan_type'         => 'required|in:Personal,Emergency,Education,Medical,Vehicle,Home,Other',
            'loan_amount'       => 'required|numeric|min:1',
            'total_installments' => 'required|integer|min:1|max:120',
            'interest_rate'     => 'nullable|numeric|min:0|max:100',
            'purpose'           => 'nullable|string|max:1000',
            'notes'             => 'nullable|string|max:1000',
        ]);

        $validated['loan_id'] = $id;
        $validated['employee_id'] = $employee->id;
        $validated['interest_rate'] = $validated['interest_rate'] ?? 0;

        $result = $this->loanService->saveLoan($validated);

        if ($result['status'] === 'success') {
            return redirect()->route('loan.show', $id)->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message'])->withInput();
    }

    /**
     * Remove the specified loan application.
     */
    public function destroy(Request $request, $id)
    {
        if (!auth()->user()->can('manage-loans')) {
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

    /**
     * Approve a loan application.
     */
    public function approve($id)
    {
        if (!auth()->user()->can('manage-loans')) {
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

    /**
     * Reject a loan application.
     */
    public function reject(Request $request, $id)
    {
        if (!auth()->user()->can('manage-loans')) {
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

    /**
     * Disburse a loan.
     */
    public function disburse($id)
    {
        //
    }

    /**
     * Display my loan applications (Employee view).
     */
    public function myLoans()
    {
        $employee = auth()->user()->employee;
        if (!$employee) {
            return redirect()->back()->with('error', 'No employee profile found.');
        }

        $loanSummary = $this->loanService->getEmployeeLoanSummary($employee->id);

        return view('loan::my', compact('employee', 'loanSummary'));
    }

    /**
     * DataTable for my loans (Employee)
     */
    public function myLoansDataTable(Request $request)
    {
        $employee = auth()->user()->employee;
        if (!$employee) {
            return response()->json(['data' => []]);
        }

        return $this->loanService->getMyLoanDataTable($request, $employee->id);
    }

    /**
     * Calculate loan preview (AJAX).
     */
    public function calculate(Request $request)
    {
        $amount = $request->input('amount', 0);
        $interestRate = $request->input('interest_rate', 0);
        $installments = $request->input('installments', 1);

        $calculations = Loan::calculatePayable($amount, $interestRate, $installments);

        return response()->json($calculations);
    }
}