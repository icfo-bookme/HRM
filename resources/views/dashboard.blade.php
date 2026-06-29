<x-app-layout>
    {{-- ===== TOP HEADER ===== --}}
    <div class="px-6 py-5 bg-white border-b border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Dashboard 👋</h1>
                <p class="text-sm text-gray-500 mt-0.5">Welcome back, {{ Auth::user()?->name ?? 'User' }}! Today is <span class="font-medium text-gray-700">{{ now()->format('l, F d, Y') }}</span></p>
            </div>
          
        </div>
    </div>

    <div class="p-6 space-y-6">

       

        {{-- ===== STATISTICS CARDS ===== --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            {{-- Card 1: Total Employees --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-lg transition-all duration-300 group">
                <div class="flex items-start justify-between mb-3">
                    <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-users text-[#006172] text-lg"></i>
                    </div>
                    <span class="inline-flex items-center gap-1 text-xs font-medium text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full">
                        <i class="fas fa-arrow-up text-[10px]"></i> 12.5%
                    </span>
                </div>
                <h3 class="text-2xl font-bold text-gray-900">{{ $totalEmployees }}</h3>
                <p class="text-sm text-gray-500 mt-0.5">Total Employees</p>
                <div class="mt-3 h-1 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full w-3/4 bg-indigo-500 rounded-full"></div>
                </div>
            </div>

            {{-- Card 2: Present Today --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-lg transition-all duration-300 group">
                <div class="flex items-start justify-between mb-3">
                    <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
                    </div>
                    <span class="inline-flex items-center gap-1 text-xs font-medium text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full">
                        <i class="fas fa-arrow-up text-[10px]"></i> 8.2%
                    </span>
                </div>
                <h3 class="text-2xl font-bold text-gray-900">{{ $presentToday }}</h3>
                <p class="text-sm text-gray-500 mt-0.5">Present Today</p>
                <div class="mt-3 h-1 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full w-4/5 bg-emerald-500 rounded-full"></div>
                </div>
            </div>

            {{-- Card 3: On Leave --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-lg transition-all duration-300 group">
                <div class="flex items-start justify-between mb-3">
                    <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-clock text-amber-600 text-lg"></i>
                    </div>
                    <span class="inline-flex items-center gap-1 text-xs font-medium text-amber-600 bg-amber-50 px-2 py-1 rounded-full">
                        <i class="fas fa-arrow-down text-[10px]"></i> 3.1%
                    </span>
                </div>
                <h3 class="text-2xl font-bold text-gray-900">{{ $onLeave }}</h3>
                <p class="text-sm text-gray-500 mt-0.5">On Leave</p>
                <div class="mt-3 h-1 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full w-1/5 bg-amber-500 rounded-full"></div>
                </div>
            </div>

            {{-- Card 4: Monthly Payroll --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-lg transition-all duration-300 group">
                <div class="flex items-start justify-between mb-3">
                    <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-wallet text-purple-600 text-lg"></i>
                    </div>
                    <span class="inline-flex items-center gap-1 text-xs font-medium text-rose-600 bg-rose-50 px-2 py-1 rounded-full">
                        <i class="fas fa-arrow-up text-[10px]"></i> 5.7%
                    </span>
                </div>
                <h3 class="text-2xl font-bold text-gray-900">৳{{ number_format($monthlyPayroll) }}</h3>
                <p class="text-sm text-gray-500 mt-0.5">Monthly Payroll</p>
                <div class="mt-3 h-1 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full w-2/3 bg-purple-500 rounded-full"></div>
                </div>
            </div>
        </div>

         {{-- ===== TOP ROW: NOTICE (col-span-2) + BIRTHDAYS (col-span-1) ===== --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left: Latest Notice (col-span-2) --}}
            <div class="lg:col-span-2">
                @php $latestNotice = $latestNotices->first(); @endphp
                @if($latestNotice)
                    @php
                        $typeColors = [
                            'General' => 'bg-slate-100 text-slate-700',
                            'HR' => 'bg-blue-100 text-blue-700',
                            'Holiday' => 'bg-green-100 text-green-700',
                            'Attendance' => 'bg-yellow-100 text-yellow-700',
                            'Payroll' => 'bg-purple-100 text-purple-700',
                            'Policy' => 'bg-indigo-100 text-indigo-700',
                            'Training' => 'bg-pink-100 text-pink-700',
                            'Event' => 'bg-orange-100 text-orange-700',
                            'Emergency' => 'bg-red-100 text-red-700',
                        ];
                        $priorityColors = [
                            'Low' => 'bg-green-50 text-green-700 border-green-200',
                            'Medium' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                            'High' => 'bg-orange-50 text-orange-700 border-orange-200',
                            'Urgent' => 'bg-red-50 text-red-700 border-red-200',
                        ];
                    @endphp
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-300 h-full {{ $latestNotice->is_pinned ? 'ring-2 ring-red-300 ring-offset-1' : '' }}">
                        @if($latestNotice->is_pinned)
                            <div class="bg-gradient-to-r from-red-500 to-red-600 px-5 py-2 flex items-center gap-2">
                                <i class="fas fa-thumbtack text-white text-xs"></i>
                                <span class="text-white text-xs font-semibold uppercase tracking-wider">Pinned Notice</span>
                            </div>
                        @endif
                        <div class="p-6">
                            <div class="flex items-start justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center">
                                        <i class="fa-solid fa-bullhorn text-indigo-600 text-lg"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">{{ $latestNotice->title }}</h3>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="{{ $typeColors[$latestNotice->notice_type] ?? 'bg-slate-100 text-slate-700' }} text-xs font-medium px-2.5 py-0.5 rounded-full">{{ $latestNotice->notice_type }}</span>
                                            <span class="{{ $priorityColors[$latestNotice->priority] ?? 'bg-slate-50 text-slate-700' }} text-xs font-medium px-2.5 py-0.5 rounded-full border">{{ $latestNotice->priority }}</span>
                                            <span class="text-xs text-gray-400"><i class="far fa-calendar-alt mr-1"></i>{{ $latestNotice->publish_date->format('d M Y, h:i A') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <a href="{{ route('notice.list') }}" class="text-xs text-indigo-600 hover:text-indigo-800 underline whitespace-nowrap">View All Notices</a>
                            </div>
                            <p class="text-sm text-gray-600 mt-3 leading-relaxed line-clamp-2">{{ Str::limit($latestNotice->description, 200) }}</p>
                        </div>
                    </div>
                @else
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-lg transition-all duration-300 h-full">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center">
                                <i class="fa-solid fa-bullhorn text-indigo-600 text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">No Notice</h3>
                                <p class="text-sm text-gray-500">No notices available at the moment.</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Right: Upcoming Birthdays (col-span-1) --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-lg transition-all duration-300">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-pink-100 flex items-center justify-center">
                        <i class="fas fa-cake-candles text-pink-500"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Upcoming Birthdays 🎂</h3>
                </div>
                <div class="space-y-3">
                    @forelse ($upcomingBirthdays as $item)
                        <div class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 hover:border-pink-200 hover:bg-pink-50/50 transition-all duration-200 group">
                            <div class="w-11 h-11 rounded-full bg-gradient-to-br from-pink-400 to-rose-500 flex items-center justify-center flex-shrink-0 text-white text-xs font-bold shadow-sm group-hover:scale-110 transition-transform">
                                @if (!empty($item->profile_photo))
                                    <img src="{{ asset('storage/' . $item->profile_photo) }}" alt="" class="w-full h-full rounded-full object-cover">
                                @else
                                    {{ substr($item->employee_name, 0, 2) }}
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-800 truncate">{{ $item->employee_name }}</p>
                                <p class="text-xs text-gray-500">{{ $item->designation ?? 'Employee' }}</p>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-[11px] text-pink-500 font-medium">
                                        @if ($item->days_until == 0) 🎉 Today!
                                        @elseif ($item->days_until == 1) Tomorrow
                                        @else In {{ $item->days_until }} days
                                        @endif
                                    </span>
                                    <span class="text-[11px] text-gray-400">• {{ \Carbon\Carbon::parse($item->date_of_birth)->format('M d') }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6">
                            <i class="fas fa-cake-candles text-3xl text-gray-200 mb-2"></i>
                            <p class="text-sm text-gray-400">No upcoming birthdays</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- ===== SECOND ROW ===== --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left: Attendance Overview --}}
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-lg transition-all duration-300">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center">
                            <i class="fas fa-calendar-check text-indigo-600"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">Attendance Overview</h3>
                    </div>
                    <div class="flex gap-1 bg-gray-100 rounded-lg p-1" id="attendanceTabs">
                        <button data-period="weekly"
                            class="attendance-tab px-3 py-1.5 text-xs font-medium rounded-md bg-white text-indigo-600 shadow-sm transition-all">Weekly</button>
                        <button data-period="monthly"
                            class="attendance-tab px-3 py-1.5 text-xs font-medium rounded-md text-gray-500 hover:text-gray-700 transition-all">Monthly</button>
                        <button data-period="yearly"
                            class="attendance-tab px-3 py-1.5 text-xs font-medium rounded-md text-gray-500 hover:text-gray-700 transition-all">Yearly</button>
                    </div>
                </div>
                {{-- Chart Placeholder with bars that change --}}
                <div class="relative h-48 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl border border-indigo-100 flex items-center justify-center overflow-hidden">
                    <div class="absolute inset-0 flex items-end px-4 pb-4 gap-2" id="attendanceBars">
                        @php
                            $weeklyHeights = [40, 55, 45, 70, 65, 50, 60];
                        @endphp
                        @foreach ($weeklyHeights as $h)
                            <div class="flex-1 bg-indigo-400/30 rounded-t-md transition-all duration-500" style="height: {{ $h }}%"></div>
                        @endforeach
                    </div>
                    <div class="relative bg-white/80 backdrop-blur-sm rounded-xl px-5 py-3 text-center shadow-sm border border-white/50">
                        <p class="text-3xl font-bold text-indigo-600">{{ $attendanceRate }}%</p>
                        <p class="text-xs text-gray-500 mt-0.5">Attendance Rate</p>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4 mt-5">
                    <div class="text-center">
                        <p class="text-lg font-bold text-gray-900">{{ $presentToday }}</p>
                        <p class="text-xs text-gray-500">Present</p>
                    </div>
                    <div class="text-center">
                        <p class="text-lg font-bold text-gray-900">{{ $onLeave }}</p>
                        <p class="text-xs text-gray-500">Absent</p>
                    </div>
                    <div class="text-center">
                        <p class="text-lg font-bold text-gray-900">{{ $activeEmployees }}</p>
                        <p class="text-xs text-gray-500">Active</p>
                    </div>
                </div>
            </div>

            {{-- Right: Employees by Department --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-lg transition-all duration-300">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-purple-100 flex items-center justify-center">
                        <i class="fas fa-building text-purple-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Employees by Department</h3>
                </div>
                {{-- Doughnut chart placeholder --}}
                <div class="flex justify-center mb-5">
                    <div class="relative w-36 h-36">
                        <svg viewBox="0 0 36 36" class="w-full h-full -rotate-90">
                            @php
                                $total = array_sum(array_column($departmentCounts, 'count')) ?: 1;
                                $offset = 0;
                                $colors = ['#6366f1', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#f97316'];
                            @endphp
                            @foreach ($departmentCounts as $idx => $dept)
                                @php
                                    $pct = ($dept['count'] / $total) * 100;
                                    $circumference = 2 * pi() * 15.9;
                                    $dash = ($pct / 100) * $circumference;
                                    $color = $colors[$idx % count($colors)];
                                @endphp
                                <circle cx="18" cy="18" r="15.9" fill="none" stroke="{{ $color }}" stroke-width="3"
                                    stroke-dasharray="{{ $dash }} {{ $circumference - $dash }}"
                                    stroke-dashoffset="{{ $offset }}"
                                    stroke-linecap="round" opacity="0.85" />
                                @php $offset -= $dash; @endphp
                            @endforeach
                            <circle cx="18" cy="18" r="10" fill="white" />
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="text-center">
                                <p class="text-lg font-bold text-gray-900">{{ $total }}</p>
                                <p class="text-[10px] text-gray-400">Total</p>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Department list --}}
                <div class="space-y-2.5">
                    @forelse ($departmentCounts as $dept)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background-color: {{ $dept['color'] }}"></span>
                                <span class="text-sm text-gray-600">{{ $dept['label'] }}</span>
                            </div>
                            <span class="text-sm font-semibold text-gray-800">{{ $dept['count'] }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 text-center py-4">No department data</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- ===== THIRD ROW ===== --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left: Recent Activities --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-lg transition-all duration-300">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center">
                        <i class="fas fa-clock-rotate-left text-emerald-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Recent Activities</h3>
                </div>
                <div class="space-y-0">
                    @forelse ($recentActivities as $activity)
                        <div class="flex gap-3 py-3 border-b border-gray-50 last:border-0 group hover:bg-gray-50/50 -mx-2 px-2 rounded-lg transition-colors">
                            <div class="w-9 h-9 rounded-lg {{ $activity['icon_bg'] }} flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="fas {{ $activity['icon'] }} {{ $activity['icon_color'] }} text-sm"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-800">{{ $activity['title'] }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $activity['description'] }}</p>
                            </div>
                            <span class="text-[11px] text-gray-400 whitespace-nowrap flex-shrink-0">{{ $activity['time'] }}</span>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <i class="fas fa-clock text-3xl text-gray-200 mb-2"></i>
                            <p class="text-sm text-gray-400">No recent activities</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Middle: Leave Summary --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-lg transition-all duration-300">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center">
                        <i class="fas fa-umbrella-beach text-amber-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Leave Summary</h3>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    @forelse ($leaveSummary as $leave)
                        @php
                            $colorMap = [
                                'indigo' => ['bg' => 'bg-indigo-100', 'text' => 'text-indigo-600', 'icon' => 'text-indigo-500'],
                                'rose' => ['bg' => 'bg-rose-100', 'text' => 'text-rose-600', 'icon' => 'text-rose-500'],
                                'amber' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-600', 'icon' => 'text-amber-500'],
                                'pink' => ['bg' => 'bg-pink-100', 'text' => 'text-pink-600', 'icon' => 'text-pink-500'],
                                'sky' => ['bg' => 'bg-sky-100', 'text' => 'text-sky-600', 'icon' => 'text-sky-500'],
                                'slate' => ['bg' => 'bg-slate-100', 'text' => 'text-slate-600', 'icon' => 'text-slate-500'],
                            ];
                            $c = $colorMap[$leave['color']] ?? $colorMap['slate'];
                        @endphp
                        <div class="rounded-xl border border-gray-100 p-4 hover:shadow-md transition-all duration-200 group">
                            <div class="flex items-center gap-2.5 mb-3">
                                <div class="w-9 h-9 rounded-lg {{ $c['bg'] }} flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <i class="fas {{ $leave['icon'] }} {{ $c['icon'] }} text-sm"></i>
                                </div>
                                <span class="text-xs font-medium text-gray-500">{{ $leave['name'] }}</span>
                            </div>
                            <p class="text-2xl font-bold {{ $c['text'] }}">{{ $leave['available'] }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">of {{ $leave['total'] }} days available</p>
                        </div>
                    @empty
                        <div class="col-span-2 text-center py-8">
                            <i class="fas fa-calendar-alt text-3xl text-gray-200 mb-2"></i>
                            <p class="text-sm text-gray-400">No leave types configured</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            // Attendance Overview tab switching
            const barData = {
                weekly: [40, 55, 45, 70, 65, 50, 60],
                monthly: [45, 60, 50, 75, 70, 85, 65, 80, 55, 90, 60, 95],
                yearly: [60, 55, 70, 65, 80, 75, 85, 70, 90, 85, 75, 80],
            };

            $('#attendanceTabs').on('click', '.attendance-tab', function() {
                // Update active tab styles
                $('#attendanceTabs .attendance-tab').removeClass('bg-white text-indigo-600 shadow-sm')
                    .addClass('text-gray-500 hover:text-gray-700');
                $(this).addClass('bg-white text-indigo-600 shadow-sm').removeClass('text-gray-500 hover:text-gray-700');

                // Get period and update bars
                const period = $(this).data('period');
                const heights = barData[period];
                const bars = $('#attendanceBars');

                bars.empty();
                heights.forEach(function(h) {
                    bars.append('<div class="flex-1 bg-indigo-400/30 rounded-t-md transition-all duration-500" style="height: ' + h + '%"></div>');
                });
            });
        });
    </script>
    @endpush
</x-app-layout>