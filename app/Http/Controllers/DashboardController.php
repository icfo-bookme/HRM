<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Modules\Attendance\Models\Attendance;
use Modules\Department\Models\Department;
use Modules\Employee\Models\Employee;
use Modules\Employee\Services\EmployeeService;
use Modules\Holidays\Models\Holiday;
use Modules\Holidays\Models\HolidayAssignment;
use Modules\Leave\Models\LeaveApplication;
use Modules\Leave\Models\LeaveType;
use Modules\Notice\Models\Notice;
use Modules\Notice\Models\NoticeView;

class DashboardController extends Controller
{
    private const RECENT_EMPLOYEES_LIMIT = 3;

    private const RECENT_LEAVES_LIMIT = 2;

    private const RECENT_NOTICES_LIMIT = 2;

    private const MAX_ACTIVITIES = 6;

    private const UPCOMING_HOLIDAYS_LIMIT = 5;

    private const LATEST_NOTICES_LIMIT = 6;

    private const BIRTHDAYS_DAYS_AHEAD = 7;

    private const DEPARTMENT_COLORS = [
        '#6366f1', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#f97316',
    ];

    /**
     * Display the application dashboard.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $today = now()->startOfDay();
        // ── Statistics ──
        $totalEmployees = Employee::count();
        $activeEmployees = Employee::where('status', 'Active')->count();

        $presentToday = Attendance::where('attendance_date', $today)
            ->where('is_absent', false)
            ->count();

        $onLeave = $this->getOnLeaveCount($today);

        // ── Monthly payroll ──
        $monthlyPayroll = $this->calculateMonthlyPayroll();

        // ── Department-wise employee counts ──
        $departmentCounts = $this->getDepartmentCounts();

        // ── Recent Activities ──
        $recentActivities = $this->buildRecentActivities();

        // ── Leave Summary ──
        $leaveSummary = $this->buildLeaveSummary();

        // ── Upcoming Holidays ──
        $upcomingHolidays = $this->getUpcomingHolidays($user?->employee, $today);

        // ── Latest Notices ──
        $latestNotices = $this->getLatestNotices();

        // ── Auto-track notice views ──
        $this->trackNoticeViews($user?->employee_id, $latestNotices);

        // ── Upcoming Birthdays ──
        $employeeService = app(EmployeeService::class);
        $upcomingBirthdays = $employeeService->getUpcomingBirthdays(self::BIRTHDAYS_DAYS_AHEAD);

        // ── Attendance Rate ──
        $attendanceRate = $totalEmployees > 0
            ? round(($presentToday / max($activeEmployees, 1)) * 100)
            : 0;

        return view('dashboard', compact(
            'upcomingHolidays',
            'latestNotices',
            'upcomingBirthdays',
            'totalEmployees',
            'activeEmployees',
            'presentToday',
            'onLeave',
            'monthlyPayroll',
            'departmentCounts',
            'recentActivities',
            'leaveSummary',
            'attendanceRate',
        ));
    }

    /**
     * Get attendance percentage chart data for week or month view.
     */
    public function attendanceChartData(Request $request): JsonResponse
    {
        $period = $request->get('period', 'month');
        $totalActive = max(Employee::where('status', 'Active')->count(), 1);

        if ($period === 'week') {
            return $this->getWeeklyChartData($totalActive);
        }

        return $this->getMonthlyChartData($totalActive);
    }

    //  Private Helpers

    /**
     * Count employees currently on leave.
     */
    private function getOnLeaveCount(Carbon $today): int
    {
        $onLeave = Attendance::where('attendance_date', $today)
            ->where('is_absent', true)
            ->count();

        if ($onLeave === 0) {
            $onLeave = LeaveApplication::where('status', LeaveApplication::STATUS_APPROVED)
                ->whereDate('from_date', '<=', $today)
                ->whereDate('to_date', '>=', $today)
                ->count();
        }

        return $onLeave;
    }

    /**
     * Calculate approximate monthly payroll.
     */
    private function calculateMonthlyPayroll(): int
    {
        return (int) (Employee::where('status', 'Active')
            ->join('salary_grades', 'employees.grade_id', '=', 'salary_grades.id')
            ->sum('salary_grades.min_salary') ?: 0);
    }

    /**
     * Build department-wise employee count array.
     */
    private function getDepartmentCounts(): array
    {
        $deptEmployees = Employee::select('department_id', DB::raw('count(*) as total'))
            ->whereNotNull('department_id')
            ->groupBy('department_id')
            ->pluck('total', 'department_id');

        $departments = Department::all()->pluck('name', 'id');
        $counts = [];
        $i = 0;

        foreach ($departments as $id => $name) {
            $counts[] = [
                'label' => $name,
                'count' => $deptEmployees[$id] ?? 0,
                'color' => self::DEPARTMENT_COLORS[$i % count(self::DEPARTMENT_COLORS)],
            ];
            $i++;
        }

        return $counts;
    }

    /**
     * Build recent activities collection.
     */
    private function buildRecentActivities(): Collection
    {
        $activities = collect();

        // Newest employees
        Employee::with('personalInfo')
            ->latest()
            ->take(self::RECENT_EMPLOYEES_LIMIT)
            ->get()
            ->each(function (Employee $emp) use ($activities) {
                $name = $emp->personalInfo?->first_name ?? 'Employee';
                $activities->push([
                    'icon' => 'fa-user-plus',
                    'icon_bg' => 'bg-emerald-100',
                    'icon_color' => 'text-emerald-600',
                    'title' => 'New Employee Added',
                    'description' => "{$name} joined the company",
                    'time' => $emp->created_at->diffForHumans(),
                ]);
            });

        // Recent leave approvals
        LeaveApplication::approved()
            ->latest('updated_at')
            ->take(self::RECENT_LEAVES_LIMIT)
            ->get()
            ->each(function (LeaveApplication $leave) use ($activities) {
                $activities->push([
                    'icon' => 'fa-check-circle',
                    'icon_bg' => 'bg-blue-100',
                    'icon_color' => 'text-blue-600',
                    'title' => 'Leave Approved',
                    'description' => ($leave->leaveType?->name ?? 'Leave').' approved',
                    'time' => $leave->updated_at->diffForHumans(),
                ]);
            });

        // Recent notices
        Notice::where('is_active', true)
            ->latest('publish_date')
            ->take(self::RECENT_NOTICES_LIMIT)
            ->get()
            ->each(function (Notice $notice) use ($activities) {
                $activities->push([
                    'icon' => 'fa-bullhorn',
                    'icon_bg' => 'bg-purple-100',
                    'icon_color' => 'text-purple-600',
                    'title' => 'Notice Published',
                    'description' => $notice->title,
                    'time' => $notice->publish_date->diffForHumans(),
                ]);
            });

        return $activities->sortByDesc('time')->take(self::MAX_ACTIVITIES);
    }

    /**
     * Build leave summary for all leave types.
     */
    private function buildLeaveSummary(): array
    {
        $leaveTypes = LeaveType::all();
        $summary = [];

        foreach ($leaveTypes as $lt) {
            $used = (int) LeaveApplication::where('leave_type_id', $lt->id)
                ->approved()
                ->whereYear('applied_at', now()->year)
                ->sum('total_days');

            $name = strtolower($lt->name);

            $icon = match (true) {
                str_contains($name, 'annual') => 'fa-calendar-check',
                str_contains($name, 'sick') => 'fa-thermometer-half',
                str_contains($name, 'casual') => 'fa-umbrella-beach',
                str_contains($name, 'maternity') => 'fa-baby',
                str_contains($name, 'paternity') => 'fa-child',
                default => 'fa-calendar-alt',
            };

            $color = match (true) {
                str_contains($name, 'annual') => 'indigo',
                str_contains($name, 'sick') => 'rose',
                str_contains($name, 'casual') => 'amber',
                str_contains($name, 'maternity') => 'pink',
                str_contains($name, 'paternity') => 'sky',
                default => 'slate',
            };

            $summary[] = [
                'name' => $lt->name,
                'icon' => $icon,
                'color' => $color,
                'available' => ($lt->max_days ?? 0) - $used,
                'total' => $lt->max_days ?? 0,
            ];
        }

        return $summary;
    }

    /**
     * Get upcoming holidays assigned to the employee.
     */
    private function getUpcomingHolidays(?Employee $employee, Carbon $today): Collection
    {
        if ($employee) {
            $assignedHolidayIds = HolidayAssignment::where(function ($q) use ($employee) {
                $q->whereNull('branch_id')->orWhere('branch_id', $employee->branch_id);
            })
                ->where(function ($q) use ($employee) {
                    $q->whereNull('department_id')->orWhere('department_id', $employee->department_id);
                })
                ->pluck('holiday_id');

            $holidays = Holiday::whereIn('id', $assignedHolidayIds)
                ->where('holiday_date', '>=', $today)
                ->orderBy('holiday_date')
                ->take(self::UPCOMING_HOLIDAYS_LIMIT)
                ->get();

            if ($holidays->isNotEmpty()) {
                return $holidays;
            }
        }

        return Holiday::where('holiday_date', '>=', $today)
            ->orderBy('holiday_date')
            ->take(self::UPCOMING_HOLIDAYS_LIMIT)
            ->get();
    }

    /**
     * Fetch latest active notices.
     */
    private function getLatestNotices(): Collection
    {
        return Notice::where('is_active', true)
            ->where('publish_date', '<=', now())
            ->where(function ($q) {
                $q->whereNull('expiry_date')->orWhere('expiry_date', '>=', now());
            })
            ->orderBy('is_pinned', 'desc')
            ->orderBy('publish_date', 'desc')
            ->take(self::LATEST_NOTICES_LIMIT)
            ->get();
    }

    /**
     * Auto-track views for notices shown on dashboard.
     */
    private function trackNoticeViews(?int $employeeId, Collection $notices): void
    {
        if ($employeeId === null || $notices->isEmpty()) {
            return;
        }

        $noticeIds = $notices->pluck('id')->toArray();
        $existingViewNoticeIds = NoticeView::whereIn('notice_id', $noticeIds)
            ->where('employee_id', $employeeId)
            ->pluck('notice_id')
            ->toArray();

        $newViewNoticeIds = array_diff($noticeIds, $existingViewNoticeIds);

        if (empty($newViewNoticeIds)) {
            return;
        }

        $now = now();
        $views = [];

        foreach ($newViewNoticeIds as $nid) {
            $views[] = [
                'notice_id' => $nid,
                'employee_id' => $employeeId,
                'created_at' => $now,
            ];
        }

        NoticeView::insert($views);
    }

    /**
     * Build weekly chart data (Sun-Sat) with single query.
     */
    private function getWeeklyChartData(int $totalActive): JsonResponse
    {
        $weekStart = now()->startOfWeek(Carbon::SUNDAY);
        $weekEnd = now()->endOfWeek(Carbon::SATURDAY);

        // Single query: aggregate present count per date
        $attendanceData = Attendance::whereBetween('attendance_date', [$weekStart, $weekEnd])
            ->select('attendance_date', DB::raw('count(*) as present_count'))
            ->where('is_absent', false)
            ->groupBy('attendance_date')
            ->pluck('present_count', 'attendance_date');

        $labels = [];
        $data = [];

        for ($day = clone $weekStart; $day->lte($weekEnd); $day->addDay()) {
            $dateStr = $day->format('Y-m-d');
            $presentCount = (int) ($attendanceData[$dateStr] ?? 0);

            $labels[] = $day->format('D');
            $data[] = round(($presentCount / $totalActive) * 100);
        }

        return response()->json([
            'labels' => $labels,
            'data' => $data,
            'period' => 'week',
        ]);
    }

    /**
     * Build monthly chart data (day 1 to end of month) with single query.
     */
    private function getMonthlyChartData(int $totalActive): JsonResponse
    {
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();

        // Single query: aggregate present count per date
        $attendanceData = Attendance::whereBetween('attendance_date', [$monthStart, $monthEnd])
            ->select('attendance_date', DB::raw('count(*) as present_count'))
            ->where('is_absent', false)
            ->groupBy('attendance_date')
            ->pluck('present_count', 'attendance_date');

        $daysInMonth = now()->daysInMonth;
        $labels = [];
        $data = [];

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = now()->startOfMonth()->addDays($day - 1);
            $dateStr = $date->format('Y-m-d');
            $presentCount = (int) ($attendanceData[$dateStr] ?? 0);

            $labels[] = $date->format('d');
            $data[] = round(($presentCount / $totalActive) * 100);
        }

        return response()->json([
            'labels' => $labels,
            'data' => $data,
            'period' => 'month',
        ]);
    }
}
