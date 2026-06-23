<?php

namespace Modules\Salary\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Salary\Services\PayrollRunService;
use Modules\Salary\Http\Requests\StorePayrollRunRequest;
use Illuminate\Http\Request;
use Modules\Setting\Models\FiscalYear;

class PayrollRunController extends Controller
{
    protected $payrollRunService;

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

    /**
     * Show the payroll generation form
     */
    public function generate()
    {
        $fiscalYears = FiscalYear::all();
        return view('salary::payroll-runs.generate', compact('fiscalYears'));
    }

    /**
     * Preview payroll calculation
     */
    public function preview(Request $request)
    {
        $request->validate(['run_month' => 'required|date']);
        $preview = $this->payrollRunService->previewPayroll($request->run_month);
        return response()->json($preview);
    }

    /**
     * Generate new payroll run
     */
    public function store(StorePayrollRunRequest $request)
    {
        $result = $this->payrollRunService->generatePayroll($request->validated());
        return response()->json($result);
    }

    /**
     * Recalculate an existing payroll run
     */
    public function recalculate($id)
    {
        $result = $this->payrollRunService->recalculatePayroll($id);
        return response()->json($result);
    }

    /**
     * Approve payroll run
     */
    public function approve($id)
    {
        $result = $this->payrollRunService->approvePayroll($id);
        return response()->json($result);
    }

    /**
     * Lock payroll run
     */
    public function lock($id)
    {
        $result = $this->payrollRunService->lockPayroll($id);
        return response()->json($result);
    }

    /**
     * Show generated payroll run with employee details
     */
    public function showGenerated($id)
    {
        $data = $this->payrollRunService->getPayrollRunWithEmployees($id);
        return view('salary::payroll-runs.show', $data);
    }

    public function destroy($id)
    {
        $result = $this->payrollRunService->deletePayrollRun($id);
        return response()->json($result);
    }
}