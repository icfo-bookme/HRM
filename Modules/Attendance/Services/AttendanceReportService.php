<?php

namespace Modules\Attendance\Services;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\Attendance\Models\Attendance;
use Modules\Employee\Models\Employee;
use Yajra\DataTables\Facades\DataTables;

class AttendanceReportService
{
    public function getMonthlyReportDataTable(Request $request)
    {
        $year = (int) $request->input('year', Carbon::now()->year);
        $month = (int) $request->input('month', Carbon::now()->month);
        $employeeId = $request->input('employee_id');

        $startDate = Carbon::create($year, $month, 1)->startOfMonth()->toDateString();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth()->toDateString();
        $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;

        $query = Employee::with(['personalInfo', 'weekend']);

        if ($employeeId) {
            $query->where('id', $employeeId);
        }

        $attendances = Attendance::whereBetween('attendance_date', [$startDate, $endDate])
            ->get()
            ->groupBy('employee_id')
            ->map(function ($items) {
                return $items->keyBy(function ($item) {
                    return Carbon::parse($item->attendance_date)->format('Y-m-d');
                });
            });

        $dataTable = DataTables::of($query)
            ->addIndexColumn()

            ->addColumn('employee_name', function ($employee) {
                $name = $employee->full_name ?? 'Unknown';
                $code = $employee->employee_code ?? '-';
                $initial = strtoupper(substr($name, 0, 1));

                return '
                    <div class="flex items-center gap-3 min-w-[180px]">
                        <div class="w-8 h-8 rounded-full bg-blue-200 flex items-center justify-center text-blue-700 font-bold">
                            ' . $initial . '
                        </div>
                        <div>
                            <div class="font-medium">' . e($name) . '</div>
                            <div class="text-xs text-gray-500">' . e($code) . '</div>
                        </div>
                    </div>
                ';
            });

        for ($day = 1; $day <= 31; $day++) {
            $dayNum = $day;

            $dataTable->addColumn('day_' . $dayNum, function ($employee) use (
                $year,
                $month,
                $dayNum,
                $daysInMonth,
                $attendances
            ) {
                if ($dayNum > $daysInMonth) {
                    return '';
                }

                $date = Carbon::create($year, $month, $dayNum);
                $dateStr = $date->format('Y-m-d');
                $dayOfWeek = $date->dayOfWeek;

                $employeeAttendance = $attendances
                    ->get($employee->id, collect())
                    ->get($dateStr);

                if ($employeeAttendance) {
                    $status = $employeeAttendance->attendance_status;
                    $isLate = $employeeAttendance->is_late ?? false;
                    $isEarlyOut = $employeeAttendance->is_early_out ?? false;

                    if ($status === 'Present' && $isLate) {
                        return '<span class="inline-flex items-center justify-center w-7 h-7 rounded text-xs font-bold border bg-orange-100 text-orange-700 border-orange-300" title="Late Present - ' . $employeeAttendance->late_minutes . ' min">LP</span>';
                    }

                    if ($status === 'Present') {
                        $title = 'Present';
                        if ($isEarlyOut) {
                            $title .= ' - Early Leave (' . $employeeAttendance->early_out_minutes . ' min)';
                        }
                        $colorClass = $isEarlyOut
                            ? 'bg-yellow-100 text-yellow-700 border-yellow-300'
                            : 'bg-green-100 text-green-700 border-green-300';
                        $letter = $isEarlyOut ? 'EL' : 'P';
                        return '<span class="inline-flex items-center justify-center w-7 h-7 rounded text-xs font-bold border ' . $colorClass . '" title="' . $title . '">' . $letter . '</span>';
                    }

                    return sprintf(
                        '<span class="inline-flex items-center justify-center w-7 h-7 rounded text-xs font-bold border %s" title="%s">%s</span>',
                        $this->getStatusColorClass($status),
                        $status . ' - ' . $dateStr,
                        $this->getStatusLetter($status)
                    );
                }

                $weekendDays = $employee->weekend?->weekend_days ?? [];
                if (in_array($dayOfWeek, $weekendDays)) {
                    return '<span class="inline-flex items-center justify-center w-7 h-7 rounded text-xs font-bold border bg-purple-100 text-purple-700 border-purple-300" title="Weekend">W</span>';
                }

                return '<span class="inline-flex items-center justify-center w-7 h-7 rounded text-xs font-bold border bg-red-100 text-red-700 border-red-300" title="Absent - ' . $dateStr . '">A</span>';
            });
        }

        $rawColumns = ['employee_name'];

        for ($day = 1; $day <= 31; $day++) {
            $rawColumns[] = 'day_' . $day;
        }

        $rawColumns[] = 'summary';

        return $dataTable

            ->addColumn('summary', function ($employee) use (
                $year,
                $month,
                $daysInMonth,
                $attendances
            ) {
                $present = 0;
                $absent = 0;
                $holiday = 0;
                $leave = 0;
                $halfDay = 0;
                $latePresent = 0;
                $earlyLeave = 0;

                $employeeAttendance = $attendances->get($employee->id, collect());
                $weekendDays = $employee->weekend?->weekend_days ?? [];

                for ($day = 1; $day <= $daysInMonth; $day++) {
                    $date = Carbon::create($year, $month, $day);
                    $dateStr = $date->format('Y-m-d');
                    $dayOfWeek = $date->dayOfWeek;

                    $record = $employeeAttendance->get($dateStr);

                    if ($record) {
                        $isLate = $record->is_late ?? false;
                        $isEarlyOut = $record->is_early_out ?? false;

                        switch ($record->attendance_status) {
                            case 'Present':
                                if ($isLate) {
                                    $latePresent++;
                                } else {
                                    if ($isEarlyOut) {
                                        $earlyLeave++;
                                    }
                                    $present++;
                                }
                                break;

                            case 'Absent':
                                $absent++;
                                break;

                            case 'Holiday':
                                $holiday++;
                                break;

                            case 'On Leave':
                                $leave++;
                                break;

                            case 'Half Day':
                                $halfDay++;
                                break;
                        }
                    } else {
                        if (!in_array($dayOfWeek, $weekendDays)) {
                            $absent++;
                        }
                    }
                }

                return '
                    <div class="flex flex-wrap gap-x-3 gap-y-1 text-xs">
                        <span class="text-green-600 font-semibold" title="Present">P: ' . $present . '</span>
                        <span class="text-orange-600 font-semibold" title="Late Present">LP: ' . $latePresent . '</span>
                        <span class="text-yellow-600 font-semibold" title="Early Leave">EL: ' . $earlyLeave . '</span>
                        <span class="text-red-600 font-semibold" title="Absent">A: ' . $absent . '</span>
                        <span class="text-blue-600 font-semibold" title="Holiday">H: ' . $holiday . '</span>
                        <span class="text-yellow-700 font-semibold" title="On Leave">L: ' . $leave . '</span>
                        <span class="text-orange-700 font-semibold" title="Half Day">HD: ' . $halfDay . '</span>
                    </div>
                ';
            })

            ->filterColumn('employee_name', function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('employee_code', 'like', "%{$keyword}%")
                        ->orWhereHas('personalInfo', function ($pq) use ($keyword) {
                            $pq->where('first_name', 'like', "%{$keyword}%")
                                ->orWhere('last_name', 'like', "%{$keyword}%");
                        });
                });
            })

            ->orderColumn('employee_name', function ($query, $order) {
                $query->orderBy('employee_code', $order);
            })

            ->rawColumns($rawColumns)

            ->make(true);
    }

    /**
     * Overtime Report: Shows overtime minutes per day + total at end.
     */
    public function getOvertimeReportDataTable(Request $request)
    {
        $year = (int) $request->input('year', Carbon::now()->year);
        $month = (int) $request->input('month', Carbon::now()->month);
        $employeeId = $request->input('employee_id');

        $startDate = Carbon::create($year, $month, 1)->startOfMonth()->toDateString();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth()->toDateString();
        $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;

        $query = Employee::with(['personalInfo', 'weekend']);

        if ($employeeId) {
            $query->where('id', $employeeId);
        }

        $attendances = Attendance::whereBetween('attendance_date', [$startDate, $endDate])
            ->get()
            ->groupBy('employee_id')
            ->map(function ($items) {
                return $items->keyBy(function ($item) {
                    return Carbon::parse($item->attendance_date)->format('Y-m-d');
                });
            });

        $dataTable = DataTables::of($query)
            ->addIndexColumn()

            ->addColumn('employee_name', function ($employee) {
                $name = $employee->full_name ?? 'Unknown';
                $code = $employee->employee_code ?? '-';
                $initial = strtoupper(substr($name, 0, 1));

                return '
                    <div class="flex items-center gap-3 min-w-[180px]">
                        <div class="w-8 h-8 rounded-full bg-blue-200 flex items-center justify-center text-blue-700 font-bold">
                            ' . $initial . '
                        </div>
                        <div>
                            <div class="font-medium">' . e($name) . '</div>
                            <div class="text-xs text-gray-500">' . e($code) . '</div>
                        </div>
                    </div>
                ';
            });

        // Add individual day columns showing overtime minutes
        for ($day = 1; $day <= 31; $day++) {
            $dayNum = $day;

            $dataTable->addColumn('day_' . $dayNum, function ($employee) use (
                $year,
                $month,
                $dayNum,
                $daysInMonth,
                $attendances
            ) {
                if ($dayNum > $daysInMonth) {
                    return '';
                }

                $date = Carbon::create($year, $month, $dayNum);
                $dateStr = $date->format('Y-m-d');
                $dayOfWeek = $date->dayOfWeek;

                $employeeAttendance = $attendances
                    ->get($employee->id, collect())
                    ->get($dateStr);

                if ($employeeAttendance) {
                    $otMinutes = (int) ($employeeAttendance->overtime_minutes ?? 0);

                    if ($otMinutes > 0) {
                        $hours = intdiv($otMinutes, 60);
                        $mins = $otMinutes % 60;
                        $display = $hours > 0 ? "{$hours}h {$mins}m" : "{$mins}m";
                        return '<span class="inline-flex items-center justify-center w-14 h-7 rounded text-xs font-semibold border bg-purple-100 text-purple-700 border-purple-300" title="Overtime: ' . $otMinutes . ' min">' . $display . '</span>';
                    }

                    // Other statuses
                    $status = $employeeAttendance->attendance_status;
                    $statusMap = [
                        'Present' => ['P', 'bg-green-100 text-green-700 border-green-300'],
                        'Absent' => ['A', 'bg-red-100 text-red-700 border-red-300'],
                        'Holiday' => ['H', 'bg-blue-100 text-blue-700 border-blue-300'],
                        'On Leave' => ['L', 'bg-yellow-100 text-yellow-700 border-yellow-300'],
                        'Half Day' => ['HD', 'bg-orange-100 text-orange-700 border-orange-300'],
                        'Weekend' => ['W', 'bg-purple-100 text-purple-700 border-purple-300'],
                    ];
                    $default = ['?', 'bg-gray-100 text-gray-700 border-gray-300'];
                    [$letter, $color] = $statusMap[$status] ?? $default;

                    return '<span class="inline-flex items-center justify-center w-7 h-7 rounded text-xs font-bold border ' . $color . '" title="' . $status . '">' . $letter . '</span>';
                }

                $weekendDays = $employee->weekend?->weekend_days ?? [];
                if (in_array($dayOfWeek, $weekendDays)) {
                    return '<span class="inline-flex items-center justify-center w-7 h-7 rounded text-xs font-bold border bg-purple-100 text-purple-700 border-purple-300" title="Weekend">W</span>';
                }

                return '<span class="inline-flex items-center justify-center w-7 h-7 rounded text-xs font-bold border bg-red-100 text-red-700 border-red-300" title="Absent - ' . $dateStr . '">A</span>';
            });
        }

        $rawColumns = ['employee_name'];

        for ($day = 1; $day <= 31; $day++) {
            $rawColumns[] = 'day_' . $day;
        }

        $rawColumns[] = 'total_overtime';

        return $dataTable

            ->addColumn('total_overtime', function ($employee) use (
                $startDate,
                $endDate
            ) {
                $totalMinutes = Attendance::where('employee_id', $employee->id)
                    ->whereBetween('attendance_date', [$startDate, $endDate])
                    ->sum('overtime_minutes');

                $totalMinutes = (int) $totalMinutes;
                $hours = intdiv($totalMinutes, 60);
                $mins = $totalMinutes % 60;

                if ($totalMinutes > 0) {
                    return '<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-bold bg-purple-600 text-white">' . $hours . 'h ' . $mins . 'm</span>';
                }

                return '<span class="text-gray-400">—</span>';
            })

            ->filterColumn('employee_name', function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('employee_code', 'like', "%{$keyword}%")
                        ->orWhereHas('personalInfo', function ($pq) use ($keyword) {
                            $pq->where('first_name', 'like', "%{$keyword}%")
                                ->orWhere('last_name', 'like', "%{$keyword}%");
                        });
                });
            })

            ->orderColumn('employee_name', function ($query, $order) {
                $query->orderBy('employee_code', $order);
            })

            ->rawColumns($rawColumns)

            ->make(true);
    }

    /**
     * Get calendar events for FullCalendar view.
     * Each employee gets color-coded events on their attendance days.
     */
    public function getCalendarEvents(Request $request): array
    {
        $year = (int) $request->input('year', Carbon::now()->year);
        $month = (int) $request->input('month', Carbon::now()->month);
        $employeeId = $request->input('employee_id');

        $startDate = Carbon::create($year, $month, 1)->startOfMonth()->toDateString();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth()->toDateString();
        $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;

        $employees = Employee::with(['personalInfo', 'weekend']);

        if ($employeeId) {
            $employees->where('id', $employeeId);
        }

        $employees = $employees->get();

        $attendances = Attendance::whereBetween('attendance_date', [$startDate, $endDate])
            ->get()
            ->groupBy('employee_id')
            ->map(function ($items) {
                return $items->keyBy(function ($item) {
                    return Carbon::parse($item->attendance_date)->format('Y-m-d');
                });
            });

        $events = [];

        foreach ($employees as $employee) {
            $weekendDays = $employee->weekend?->weekend_days ?? [];
            $name = $employee->full_name ?? 'Unknown';
            $code = $employee->employee_code ?? '-';
            $shortName = strlen($name) > 12 ? substr($name, 0, 12) . '...' : $name;

            $colorMap = [
                'Present' => '#16a34a',
                'Absent' => '#dc2626',
                'Holiday' => '#2563eb',
                'On Leave' => '#ca8a04',
                'Half Day' => '#ea580c',
                'Weekend' => '#9333ea',
            ];

            for ($day = 1; $day <= $daysInMonth; $day++) {
                $date = Carbon::create($year, $month, $day);
                $dateStr = $date->format('Y-m-d');
                $dayOfWeek = $date->dayOfWeek;

                $record = $attendances->get($employee->id, collect())->get($dateStr);

                $status = null;
                $displayStatus = null;
                $bgColor = null;
                $tooltip = null;
                $checkIn = null;
                $checkOut = null;
                $lateMinutes = null;
                $earlyOutMinutes = null;
                $workingHours = null;

                if ($record) {
                    $status = $record->attendance_status;
                    $isLate = $record->is_late ?? false;
                    $isEarlyOut = $record->is_early_out ?? false;

                    if ($status === 'Present' && $isLate) {
                        $displayStatus = 'Late Present';
                        $bgColor = '#ea580c';
                        $lateMinutes = $record->late_minutes;
                    } elseif ($status === 'Present' && $isEarlyOut) {
                        $displayStatus = 'Early Leave';
                        $bgColor = '#ca8a04';
                        $earlyOutMinutes = $record->early_out_minutes;
                    } else {
                        $displayStatus = $status;
                        $bgColor = $colorMap[$status] ?? '#6b7280';
                    }

                    $checkIn = $record->check_in_at ? Carbon::parse($record->check_in_at)->format('h:i A') : null;
                    $checkOut = $record->check_out_at ? Carbon::parse($record->check_out_at)->format('h:i A') : null;
                    $lateMinutes = $lateMinutes ?? ($record->late_minutes ?: null);
                    $earlyOutMinutes = $earlyOutMinutes ?? ($record->early_out_minutes ?: null);

                    if ($record->net_working_minutes && $record->net_working_minutes > 0) {
                        $h = intdiv($record->net_working_minutes, 60);
                        $m = $record->net_working_minutes % 60;
                        $workingHours = "{$h}h {$m}m";
                    }
                } else {
                    if (in_array($dayOfWeek, $weekendDays)) {
                        $status = 'Weekend';
                        $displayStatus = 'Weekend';
                        $bgColor = '#9333ea';
                    } else {
                        $status = 'Absent';
                        $displayStatus = 'Absent';
                        $bgColor = '#dc2626';
                    }
                }

                $tooltip = "{$name} - {$displayStatus}";

                $events[] = [
                    'id' => 'att-' . $employee->id . '-' . $dateStr,
                    'title' => $shortName . ': ' . ($this->getStatusLetter($status) ?? '?'),
                    'start' => $dateStr,
                    'backgroundColor' => $bgColor,
                    'borderColor' => $bgColor,
                    'textColor' => '#ffffff',
                    'display' => 'auto',
                    'extendedProps' => [
                        'employee_name' => $name,
                        'employee_code' => $code,
                        'status' => $status,
                        'display_status' => $displayStatus,
                        'check_in' => $checkIn,
                        'check_out' => $checkOut,
                        'late_minutes' => $lateMinutes,
                        'early_out_minutes' => $earlyOutMinutes,
                        'working_hours' => $workingHours,
                        'tooltip' => $tooltip,
                    ],
                ];
            }
        }

        return $events;
    }

    private function getStatusLetter(string $status): string
    {
        return match ($status) {
            'Present' => 'P',
            'Absent' => 'A',
            'Holiday' => 'H',
            'On Leave' => 'L',
            'Half Day' => 'HD',
            'Weekend' => 'W',
            default => '?',
        };
    }

    private function getStatusColorClass(string $status): string
    {
        return match ($status) {
            'Present' => 'bg-green-100 text-green-700 border-green-300',
            'Absent' => 'bg-red-100 text-red-700 border-red-300',
            'Holiday' => 'bg-blue-100 text-blue-700 border-blue-300',
            'On Leave' => 'bg-yellow-100 text-yellow-700 border-yellow-300',
            'Half Day' => 'bg-orange-100 text-orange-700 border-orange-300',
            'Weekend' => 'bg-purple-100 text-purple-700 border-purple-300',
            default => 'bg-gray-100 text-gray-700 border-gray-300',
        };
    }
}