<x-app-layout>
    <div class="p-4">
        {{-- PAGE HEADER --}}
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">KPI Dashboard</h1>
            <p class="text-sm text-gray-500 mt-1">Welcome back, {{ $employee?->personalInfo?->full_name ?? 'User' }}</p>
        </div>

        {{-- STATS CARDS --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            {{-- Daily Performance Card --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 hover:shadow-md transition">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center">
                        <i class="fas fa-calendar-day text-blue-600 text-xl"></i>
                    </div>
                    <span class="text-xs font-medium text-gray-500">Today</span>
                </div>
                <h3 class="text-sm text-gray-600 mb-1">Daily Performance</h3>
                <p class="text-2xl font-bold text-gray-800">
                    {{ $dailyPerformance ? number_format(($dailyPerformance['overall_percentage'] ?? $dailyPerformance['daily_percentage'] ?? 0), 1) . '%' : 'N/A' }}
                </p>
                @if($dailyPerformance)
                    <div class="mt-2 flex items-center text-xs">
                        <span class="text-gray-500">Score: </span>
                        <span class="font-semibold text-gray-700 ml-1">{{ number_format($dailyPerformance['total_obtained'] ?? $dailyPerformance['daily_obtained'] ?? 0, 1) }}/{{ number_format($dailyPerformance['total_target'] ?? $dailyPerformance['daily_target'] ?? 0, 1) }}</span>
                    </div>
                @endif
            </div>

            {{-- Monthly Performance Card --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 hover:shadow-md transition">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-lg bg-green-100 flex items-center justify-center">
                        <i class="fas fa-chart-line text-green-600 text-xl"></i>
                    </div>
                    <span class="text-xs font-medium text-gray-500">{{ now()->format('M Y') }}</span>
                </div>
                <h3 class="text-sm text-gray-600 mb-1">Monthly Performance</h3>
                <p class="text-2xl font-bold text-gray-800">
                    {{ $monthlyPerformance ? number_format($monthlyPerformance['overall_percentage'], 1) . '%' : 'N/A' }}
                </p>
                @if($monthlyPerformance)
                    <div class="mt-2 flex items-center text-xs">
                        <span class="text-gray-500">Rating: </span>
                        <span class="font-semibold text-gray-700 ml-1">{{ $monthlyPerformance['rating'] ?? 'N/A' }}</span>
                    </div>
                @endif
            </div>

            {{-- Tasks Card --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 hover:shadow-md transition">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-lg bg-purple-100 flex items-center justify-center">
                        <i class="fas fa-tasks text-purple-600 text-xl"></i>
                    </div>
                    <a href="{{ route('kpi.tasks.index') }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium">View All</a>
                </div>
                <h3 class="text-sm text-gray-600 mb-1">My Tasks</h3>
                <p class="text-2xl font-bold text-gray-800">{{ $taskStats['total'] ?? 0 }}</p>
                <div class="mt-2 flex items-center gap-3 text-xs">
                    <span class="text-yellow-600">{{ $taskStats['pending'] ?? 0 }} Pending</span>
                    <span class="text-green-600">{{ $taskStats['completed'] ?? 0 }} Completed</span>
                </div>
            </div>

            {{-- Completion Rate Card --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 hover:shadow-md transition">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-lg bg-orange-100 flex items-center justify-center">
                        <i class="fas fa-percentage text-orange-600 text-xl"></i>
                    </div>
                </div>
                <h3 class="text-sm text-gray-600 mb-1">Task Completion</h3>
                <p class="text-2xl font-bold text-gray-800">{{ $taskStats['completion_rate'] ?? 0 }}%</p>
                <div class="mt-2">
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-orange-500 h-2 rounded-full" style="width: {{ $taskStats['completion_rate'] ?? 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- QUICK ACTIONS --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Quick Actions</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <a href="{{ route('kpi.tasks.index') }}"
                    class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 hover:border-blue-300 hover:bg-blue-50 transition">
                    <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                        <i class="fas fa-list-check text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">View Tasks</p>
                        <p class="text-xs text-gray-500">Manage KPI tasks</p>
                    </div>
                </a>

                <a href="{{ route('kpi.reviews.index') }}"
                    class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 hover:border-green-300 hover:bg-green-50 transition">
                    <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center">
                        <i class="fas fa-clipboard-check text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">Monthly Reviews</p>
                        <p class="text-xs text-gray-500">View performance reviews</p>
                    </div>
                </a>

                <a href="{{ route('kpi.daily') }}"
                    class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 hover:border-purple-300 hover:bg-purple-50 transition">
                    <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center">
                        <i class="fas fa-calendar-alt text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">Daily Performance</p>
                        <p class="text-xs text-gray-500">View daily tracking</p>
                    </div>
                </a>
            </div>
        </div>

        {{-- MONTHLY PERFORMANCE DETAILS --}}
        @if($monthlyPerformance)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50/50">
                    <h2 class="text-lg font-bold text-gray-800">Monthly Performance Breakdown</h2>
                    <p class="text-sm text-gray-500 mt-1">{{ now()->format('F Y') }}</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        {{-- Attendance --}}
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                            <div class="flex items-center gap-2 mb-2">
                                <i class="fas fa-clock text-blue-500"></i>
                                <h4 class="text-sm font-semibold text-gray-700">Attendance</h4>
                            </div>
                            <p class="text-xl font-bold text-gray-800">{{ number_format($monthlyPerformance['attendance_percentage'] ?? 0, 1) }}%</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $monthlyPerformance['present_days'] ?? 0 }}/{{ $monthlyPerformance['working_days'] ?? 0 }} days</p>
                        </div>

                        {{-- Tasks --}}
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                            <div class="flex items-center gap-2 mb-2">
                                <i class="fas fa-tasks text-green-500"></i>
                                <h4 class="text-sm font-semibold text-gray-700">Tasks</h4>
                            </div>
                            <p class="text-xl font-bold text-gray-800">{{ number_format($monthlyPerformance['task_percentage'] ?? 0, 1) }}%</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $monthlyPerformance['completed_tasks'] ?? 0 }}/{{ $monthlyPerformance['total_assigned_tasks'] ?? 0 }} tasks</p>
                        </div>

                        {{-- Behavior --}}
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                            <div class="flex items-center gap-2 mb-2">
                                <i class="fas fa-smile text-yellow-500"></i>
                                <h4 class="text-sm font-semibold text-gray-700">Behavior</h4>
                            </div>
                            <p class="text-xl font-bold text-gray-800">{{ number_format($monthlyPerformance['behavior_percentage'] ?? 0, 1) }}%</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $monthlyPerformance['behavior_obtained'] ?? 0 }}/{{ $monthlyPerformance['behavior_target'] ?? 0 }} points</p>
                        </div>

                        {{-- Overall --}}
                        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg p-4 border border-blue-100">
                            <div class="flex items-center gap-2 mb-2">
                                <i class="fas fa-star text-indigo-500"></i>
                                <h4 class="text-sm font-semibold text-gray-700">Overall</h4>
                            </div>
                            <p class="text-xl font-bold {{ ($monthlyPerformance['overall_percentage'] ?? 0) >= 80 ? 'text-green-600' : (($monthlyPerformance['overall_percentage'] ?? 0) >= 60 ? 'text-blue-600' : 'text-orange-600') }}">
                                {{ number_format($monthlyPerformance['overall_percentage'] ?? 0, 1) }}%
                            </p>
                            <p class="text-xs text-gray-500 mt-1">Rating: {{ $monthlyPerformance['rating'] ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>