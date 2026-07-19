<?php

namespace Modules\Kpi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Kpi\Services\KpiDailyService;
use Modules\Kpi\Services\KpiMonthlyService;
use Modules\Kpi\Services\KpiTaskService;
use Modules\Kpi\Models\KpiCategory;
use Modules\Kpi\Models\KpiIndicator;
use Modules\Employee\Models\Employee;

class KpiController extends Controller
{
    protected KpiDailyService $dailyService;
    protected KpiMonthlyService $monthlyService;
    protected KpiTaskService $taskService;

    public function __construct(KpiDailyService $dailyService, KpiMonthlyService $monthlyService, KpiTaskService $taskService)
    {
        $this->dailyService = $dailyService;
        $this->monthlyService = $monthlyService;
        $this->taskService = $taskService;
    }

    /**
     * KPI Dashboard
     */
    public function dashboard()
    {
        $user = auth()->user();
        $employee = $user->employee;

        $dailyPerformance = null;
        $monthlyPerformance = null;
        $taskStats = null;

        if ($employee) {
            $dailyResult = $this->dailyService->getEmployeeDailyPerformance($employee->id);
            $dailyPerformance = $dailyResult['data'] ?? null;

            $monthlyResult = $this->monthlyService->getEmployeeMonthlyPerformance(
                $employee->id,
                now()->year,
                now()->month
            );
            $monthlyPerformance = $monthlyResult['data'] ?? null;

            $taskStats = $this->taskService->getTaskStatistics($employee->id);
        }

        return view('kpi::dashboard', compact('dailyPerformance', 'monthlyPerformance', 'employee', 'taskStats'));
    }

    /**
     * Daily Performance View
     */
    public function dailyPerformance(Request $request)
    {
        $user = auth()->user();
        $employee = $user->employee;
        $date = $request->get('date', now()->format('Y-m-d'));

        $performance = null;
        if ($employee) {
            $result = $this->dailyService->getEmployeeDailyPerformance($employee->id, $date);
            $performance = $result['data'] ?? null;
        }

        return view('kpi::daily', compact('performance', 'date', 'employee'));
    }

    /**
     * Monthly Performance View
     */
    public function monthlyPerformance(Request $request)
    {
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);

        $user = auth()->user();
        $employee = $user->employee;

        $performance = null;
        if ($employee) {
            $result = $this->monthlyService->getEmployeeMonthlyPerformance($employee->id, $year, $month);
            $performance = $result['data'] ?? null;
        }

        return view('kpi::monthly', compact('performance', 'year', 'month', 'employee'));
    }

    /**
     * Monthly Detail View
     */
    public function monthlyDetail(int $employeeId, int $year, int $month)
    {
        $employee = Employee::with('personalInfo', 'department', 'designation')->findOrFail($employeeId);
        $result = $this->monthlyService->getEmployeeMonthlyPerformance($employeeId, $year, $month);
        $performance = $result['data'] ?? null;

        return view('kpi::monthly-detail', compact('employee', 'performance', 'year', 'month'));
    }

    /**
     * KPI Settings View
     */
    public function settings()
    {
        $categories = KpiCategory::with('indicators')->ordered()->get();
        return view('kpi::settings', compact('categories'));
    }

    /**
     * Update Categories
     */
    public function updateCategories(Request $request)
    {
        $request->validate([
            'categories' => 'required|array',
            'categories.*.id' => 'required|exists:kpi_categories,id',
            'categories.*.weight_percentage' => 'required|numeric|min:0|max:100',
            'categories.*.is_active' => 'boolean',
        ]);

        foreach ($request->categories as $catData) {
            KpiCategory::where('id', $catData['id'])->update([
                'weight_percentage' => $catData['weight_percentage'],
                'is_active' => $catData['is_active'] ?? true,
            ]);
        }

        if ($request->ajax()) {
            return response()->json(['status' => 'success', 'message' => 'Categories updated successfully.']);
        }

        return redirect()->route('kpi.settings')->with('success', 'Categories updated successfully.');
    }

    /**
     * Update Indicators
     */
    public function updateIndicators(Request $request)
    {
        $request->validate([
            'indicators' => 'required|array',
            'indicators.*.id' => 'required|exists:kpi_indicators,id',
            'indicators.*.weight_percentage' => 'required|numeric|min:0|max:100',
            'indicators.*.point_per_unit' => 'nullable|numeric',
            'indicators.*.is_active' => 'boolean',
        ]);

        foreach ($request->indicators as $indData) {
            KpiIndicator::where('id', $indData['id'])->update([
                'weight_percentage' => $indData['weight_percentage'],
                'point_per_unit' => $indData['point_per_unit'] ?? null,
                'is_active' => $indData['is_active'] ?? true,
            ]);
        }

        if ($request->ajax()) {
            return response()->json(['status' => 'success', 'message' => 'Indicators updated successfully.']);
        }

        return redirect()->route('kpi.settings')->with('success', 'Indicators updated successfully.');
    }

    /**
     * API: Get daily performance
     */
    public function apiDaily(int $employeeId, Request $request)
    {
        $date = $request->get('date', now()->format('Y-m-d'));
        $result = $this->dailyService->getEmployeeDailyPerformance($employeeId, $date);
        return response()->json($result);
    }

    /**
     * API: Get monthly performance
     */
    public function apiMonthly(int $employeeId, int $year, int $month)
    {
        $result = $this->monthlyService->getEmployeeMonthlyPerformance($employeeId, $year, $month);
        return response()->json($result);
    }
}