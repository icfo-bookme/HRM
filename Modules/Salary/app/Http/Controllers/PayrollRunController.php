<?php

namespace Modules\Salary\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Salary\PreviewPayrollRequest;
use Illuminate\Http\Request;
use Modules\Employee\Models\Employee;
use Modules\Salary\Http\Requests\StorePayrollRunRequest;
use Modules\Salary\Models\PayrollRun;
use Modules\Salary\Services\PayrollRunService;
use Modules\Setting\Models\FiscalYear;

class PayrollRunController extends Controller
{
    protected PayrollRunService $payrollRunService;

    public function __construct(PayrollRunService $payrollRunService)
    {
        $this->payrollRunService = $payrollRunService;
    }

    public function index()
    {
        $fiscalYears = FiscalYear::all();

        return view('salary::payroll-runs.index', compact('fiscalYears'));
    }

    public function dataTable(Request $request)
    {
        return $this->payrollRunService->getPayrollRunDataTable($request);
    }

    public function generate()
    {
        $fiscalYears = FiscalYear::all();

        return view('salary::payroll-runs.generate', compact('fiscalYears'));
    }

    public function preview(PreviewPayrollRequest $request)
    {
        $preview = $this->payrollRunService->previewPayroll($request->run_month);

        return response()->json($preview);
    }

    public function store(StorePayrollRunRequest $request)
    {
        $result = $this->payrollRunService->generatePayroll($request->validated());

        return response()->json($result);
    }

    public function recalculate($id)
    {
        $result = $this->payrollRunService->recalculatePayroll($id);

        return response()->json($result);
    }

    public function approve($id)
    {
        $result = $this->payrollRunService->approvePayroll($id);

        return response()->json($result);
    }

    public function lock($id)
    {
        $result = $this->payrollRunService->lockPayroll($id);

        return response()->json($result);
    }

    public function showGenerated($id)
    {
        $data = $this->payrollRunService->getPayrollRunWithEmployees($id);

        return view('salary::payroll-runs.show', $data);
    }

    public function paymentListIndex()
    {
        $employees = Employee::with('personalInfo')->get();
        $payrollRuns = PayrollRun::select('id', 'run_label', 'run_month')
            ->where('status', 'Locked')
            ->orderBy('run_month', 'desc')
            ->get();

        return view('salary::payroll-runs.payment-list', compact('employees', 'payrollRuns'));
    }

    public function paymentListDataTable(Request $request)
    {
        return $this->payrollRunService->getPaymentListDataTable($request);
    }

    public function markAsPaid($detailId)
    {
        $result = $this->payrollRunService->markDetailAsPaid($detailId);

        return response()->json($result);
    }

    public function destroy($id)
    {
        $result = $this->payrollRunService->deletePayrollRun($id);

        return response()->json($result);
    }
}
