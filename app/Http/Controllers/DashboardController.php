<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Holidays\Models\Holiday;
use Modules\Holidays\Models\HolidayAssignment;
use Modules\Notice\Models\Notice;
use Modules\Employee\Services\EmployeeService;

class DashboardController extends Controller
{
    /**
     * Show the application dashboard.
     */
    public function index()
    {
        $upcomingHolidays = collect();
        $user = Auth::user();

        if ($user) {
            $employee = $user->employee;

            if ($employee) {
                // Get holidays assigned to this employee's department/branch
                $assignedHolidayIds = HolidayAssignment::where(function ($q) use ($employee) {
                    $q->whereNull('branch_id')->orWhere('branch_id', $employee->branch_id);
                })
                ->where(function ($q) use ($employee) {
                    $q->whereNull('department_id')->orWhere('department_id', $employee->department_id);
                })
                ->pluck('holiday_id');

                $upcomingHolidays = Holiday::whereIn('id', $assignedHolidayIds)
                    ->where('holiday_date', '>=', now()->startOfDay())
                    ->orderBy('holiday_date')
                    ->take(5)
                    ->get();
            }

            // If no employee record or no assignments, show all upcoming public holidays
            if ($upcomingHolidays->isEmpty()) {
                $upcomingHolidays = Holiday::where('holiday_date', '>=', now()->startOfDay())
                    ->orderBy('holiday_date')
                    ->take(5)
                    ->get();
            }
        }

        // Latest active notices
        $latestNotices = Notice::where('is_active', true)
            ->where('publish_date', '<=', now())
            ->where(function ($q) {
                $q->whereNull('expiry_date')->orWhere('expiry_date', '>=', now());
            })
            ->orderBy('is_pinned', 'desc')
            ->orderBy('publish_date', 'desc')
            ->take(6)
            ->get();

        // Upcoming birthdays within 7 days
        $employeeService = app(EmployeeService::class);
        $upcomingBirthdays = $employeeService->getUpcomingBirthdays(7);

        return view('dashboard', compact('upcomingHolidays', 'latestNotices', 'upcomingBirthdays'));
    }
}