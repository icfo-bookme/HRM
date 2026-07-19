<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Holidays\Models\Holiday;
use Modules\Holidays\Models\HolidayAssignment;
use Modules\Notice\Models\Notice;
use Modules\Notice\Models\NoticeView;
use Modules\Employee\Models\Employee;
use Modules\Employee\Models\EmployeePersonalInfo;
use Modules\Employee\Services\EmployeeService;
use Modules\Attendance\Models\Attendance;
use Modules\Leave\Models\LeaveType;
use Modules\Leave\Models\LeaveApplication;

class DashboardController extends Controller
{
    /**
     * Show the application dashboard.
     */
    public function index()
    {
        $user = Auth::user();

        // ── Statistics ──
        $totalEmployees = Employee::count();
        $activeEmployees = Employee::where('status', 'Active')->count();

        $today = now()->startOfDay();
        $presentToday = Attendance::where('attendance_date', $today)
            ->where('is_absent', false)
            ->count();

        $onLeave = Attendance::where('attendance_date', $today)
            ->where('is_absent', true)
            ->count();

        // Fallback: count approved leave applications for today
        if ($onLeave == 0) {
            $onLeave = LeaveApplication::where('status', LeaveApplication::STATUS_APPROVED)
                ->whereDate('from_date', '<=', $today)
                ->whereDate('to_date', '>=', $today)
                ->count();
        }

        // Monthly payroll (placeholder — sum salaries or use a static demo)
        $monthlyPayroll = Employee::where('status', 'Active')
            ->join('salary_grades', 'employees.grade_id', '=', 'salary_grades.id')
            ->sum('salary_grades.min_salary') ?: 1250000;

        // ── Department-wise employee counts ──
        $deptEmployees = Employee::select('department_id', DB::raw('count(*) as total'))
            ->whereNotNull('department_id')
            ->groupBy('department_id')
            ->pluck('total', 'department_id');

        $departments = \Modules\Department\Models\Department::all()->pluck('name', 'id');
        $departmentLabels = [];
        $departmentCounts = [];
        $departmentColors = ['#6366f1', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#f97316'];
        $i = 0;
        foreach ($departments as $id => $name) {
            $departmentLabels[] = $name;
            $departmentCounts[] = ['label' => $name, 'count' => $deptEmployees[$id] ?? 0, 'color' => $departmentColors[$i % count($departmentColors)]];
            $i++;
        }

        // ── Recent Activities ──
        $recentActivities = collect();

        // Newest employees
        $newEmployees = Employee::with('personalInfo')
            ->latest()
            ->take(3)
            ->get();
        foreach ($newEmployees as $emp) {
            $name = $emp->personalInfo?->first_name ?? 'Employee';
            $recentActivities->push([
                'icon' => 'fa-user-plus',
                'icon_bg' => 'bg-emerald-100',
                'icon_color' => 'text-emerald-600',
                'title' => "New Employee Added",
                'description' => "{$name} joined the company",
                'time' => $emp->created_at->diffForHumans(),
            ]);
        }

        // Recent leave approvals
        $recentLeaves = LeaveApplication::approved()
            ->latest('updated_at')
            ->take(2)
            ->get();
        foreach ($recentLeaves as $leave) {
            $recentActivities->push([
                'icon' => 'fa-check-circle',
                'icon_bg' => 'bg-blue-100',
                'icon_color' => 'text-blue-600',
                'title' => 'Leave Approved',
                'description' => ($leave->leaveType?->name ?? 'Leave') . " approved",
                'time' => $leave->updated_at->diffForHumans(),
            ]);
        }

        // Recent notices
        $recentNotices = Notice::where('is_active', true)
            ->latest('publish_date')
            ->take(2)
            ->get();
        foreach ($recentNotices as $notice) {
            $recentActivities->push([
                'icon' => 'fa-bullhorn',
                'icon_bg' => 'bg-purple-100',
                'icon_color' => 'text-purple-600',
                'title' => 'Notice Published',
                'description' => $notice->title,
                'time' => $notice->publish_date->diffForHumans(),
            ]);
        }

        $recentActivities = $recentActivities->sortByDesc('time')->take(6);

        // ── Leave Summary ──
        $leaveTypes = LeaveType::all();
        $leaveSummary = [];
        foreach ($leaveTypes as $lt) {
            $used = LeaveApplication::where('leave_type_id', $lt->id)
                ->approved()
                ->whereYear('applied_at', now()->year)
                ->sum('total_days') ?: 0;

            $leaveSummary[] = [
                'name' => $lt->name,
                'icon' => match (strtolower($lt->name)) {
                    'annual', 'annual leave' => 'fa-calendar-check',
                    'sick', 'sick leave' => 'fa-thermometer-half',
                    'casual', 'casual leave' => 'fa-umbrella-beach',
                    'maternity', 'maternity leave' => 'fa-baby',
                    'paternity', 'paternity leave' => 'fa-child',
                    default => 'fa-calendar-alt',
                },
                'color' => match (strtolower($lt->name)) {
                    'annual', 'annual leave' => 'indigo',
                    'sick', 'sick leave' => 'rose',
                    'casual', 'casual leave' => 'amber',
                    'maternity', 'maternity leave' => 'pink',
                    'paternity', 'paternity leave' => 'sky',
                    default => 'slate',
                },
                'available' => ($lt->max_days ?? 0) - $used,
                'total' => $lt->max_days ?? 0,
            ];
        }

        // ── Upcoming Holidays ──
        $upcomingHolidays = collect();
        $employee = $user?->employee;

        if ($employee) {
            $assignedHolidayIds = HolidayAssignment::where(function ($q) use ($employee) {
                $q->whereNull('branch_id')->orWhere('branch_id', $employee->branch_id);
            })
                ->where(function ($q) use ($employee) {
                    $q->whereNull('department_id')->orWhere('department_id', $employee->department_id);
                })
                ->pluck('holiday_id');

            $upcomingHolidays = Holiday::whereIn('id', $assignedHolidayIds)
                ->where('holiday_date', '>=', $today)
                ->orderBy('holiday_date')
                ->take(5)
                ->get();
        }

        if ($upcomingHolidays->isEmpty()) {
            $upcomingHolidays = Holiday::where('holiday_date', '>=', $today)
                ->orderBy('holiday_date')
                ->take(5)
                ->get();
        }

        // ── Latest Notices ──
        $latestNotices = Notice::where('is_active', true)
            ->where('publish_date', '<=', now())
            ->where(function ($q) {
                $q->whereNull('expiry_date')->orWhere('expiry_date', '>=', now());
            })
            ->orderBy('is_pinned', 'desc')
            ->orderBy('publish_date', 'desc')
            ->take(6)
            ->get();

        // Auto-track views for notices shown on dashboard
        $employeeId = $user?->employee_id;
        if ($employeeId && $latestNotices->isNotEmpty()) {
            $noticeIds = $latestNotices->pluck('id')->toArray();
            $existingViewNoticeIds = NoticeView::whereIn('notice_id', $noticeIds)
                ->where('employee_id', $employeeId)
                ->pluck('notice_id')
                ->toArray();

            $newViewNoticeIds = array_diff($noticeIds, $existingViewNoticeIds);

            if (!empty($newViewNoticeIds)) {
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
        }

        // ── Upcoming Birthdays ──
        $employeeService = app(EmployeeService::class);
        $upcomingBirthdays = $employeeService->getUpcomingBirthdays(7);

        // ── Attendance Overview Stats ──
        $totalWorkingDays = now()->daysInMonth;
        $attendanceRate = $totalEmployees > 0 ? round(($presentToday / max($activeEmployees, 1)) * 100) : 0;

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
}
