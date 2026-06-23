<x-app-layout>
    <div class="p-4">
        {{-- HEADER --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-xl font-bold text-gray-800">Monthly Performance Report</h1>
                    <p class="text-sm text-gray-500 mt-1">
                        {{ $employee->personalInfo?->full_name ?? 'N/A' }} - {{ $year }} / {{ \Carbon\Carbon::createFromDate($year, $month, 1)->format('F') }}
                    </p>
                </div>
                <a href="{{ route('kpi.monthly') }}?month={{ $month }}&year={{ $year }}"
                    class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-sm font-medium transition">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>
            </div>

            {{-- REPORT CONTENT --}}
            <div class="p-6">
                @if($performance)
                    {{-- OVERALL SCORE --}}
                    <div class="mb-6 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg p-6 border border-blue-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Overall Monthly Score</p>
                                <p class="text-4xl font-bold {{ $performance['overall_percentage'] >= 80 ? 'text-green-600' : ($performance['overall_percentage'] >= 60 ? 'text-blue-600' : 'text-orange-600') }}">
                                    {{ number_format($performance['overall_percentage'], 1) }}%
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-600 mb-1">Rating</p>
                                <p class="text-3xl font-bold text-gray-800">{{ $performance['rating'] ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- EMPLOYEE INFO --}}
                    <div class="mb-6 bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold text-xl">
                                {{ $employee->personalInfo?->full_name ? strtoupper(substr($employee->personalInfo->full_name, 0, 2)) : 'NA' }}
                            </div>
                            <div>
                                <p class="font-bold text-gray-800 text-lg">{{ $employee->personalInfo?->full_name ?? 'N/A' }}</p>
                                <p class="text-sm text-gray-600">{{ $employee->employee_code ?? 'N/A' }}</p>
                                <p class="text-sm text-gray-600">{{ $employee->department?->name ?? 'N/A' }} | {{ $employee->designation?->name ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- DETAILED BREAKDOWN --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Attendance Details --}}
                        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                            <div class="px-5 py-3 bg-blue-50 border-b border-blue-100">
                                <h3 class="text-sm font-semibold text-gray-800 flex items-center gap-2">
                                    <i class="fas fa-clock text-blue-600"></i>
                                    Attendance Details
                                </h3>
                            </div>
                            <div class="p-5 space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Working Days:</span>
                                    <span class="text-sm font-bold text-gray-800">{{ $performance['working_days'] ?? 0 }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Present Days:</span>
                                    <span class="text-sm font-bold text-gray-800">{{ $performance['present_days'] ?? 0 }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Late Days:</span>
                                    <span class="text-sm font-bold text-gray-800">{{ $performance['late_days'] ?? 0 }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Attendance Target:</span>
                                    <span class="text-sm font-bold text-gray-800">{{ number_format($performance['attendance_target'] ?? 0, 1) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Attendance Obtained:</span>
                                    <span class="text-sm font-bold text-gray-800">{{ number_format($performance['attendance_obtained'] ?? 0, 1) }}</span>
                                </div>
                                <div class="flex justify-between pt-2 border-t border-gray-200">
                                    <span class="text-sm font-semibold text-gray-700">Attendance Score:</span>
                                    <span class="text-sm font-bold {{ ($performance['attendance_percentage'] ?? 0) >= 80 ? 'text-green-600' : (($performance['attendance_percentage'] ?? 0) >= 60 ? 'text-blue-600' : 'text-orange-600') }}">
                                        {{ number_format($performance['attendance_percentage'] ?? 0, 1) }}%
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Task Details --}}
                        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                            <div class="px-5 py-3 bg-green-50 border-b border-green-100">
                                <h3 class="text-sm font-semibold text-gray-800 flex items-center gap-2">
                                    <i class="fas fa-tasks text-green-600"></i>
                                    Task Details
                                </h3>
                            </div>
                            <div class="p-5 space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Total Assigned:</span>
                                    <span class="text-sm font-bold text-gray-800">{{ $performance['total_assigned_tasks'] ?? 0 }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Completed:</span>
                                    <span class="text-sm font-bold text-gray-800">{{ $performance['completed_tasks'] ?? 0 }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Task Target:</span>
                                    <span class="text-sm font-bold text-gray-800">{{ number_format($performance['task_target'] ?? 0, 1) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Task Obtained:</span>
                                    <span class="text-sm font-bold text-gray-800">{{ number_format($performance['task_obtained'] ?? 0, 1) }}</span>
                                </div>
                                <div class="flex justify-between pt-2 border-t border-gray-200">
                                    <span class="text-sm font-semibold text-gray-700">Task Score:</span>
                                    <span class="text-sm font-bold {{ ($performance['task_percentage'] ?? 0) >= 80 ? 'text-green-600' : (($performance['task_percentage'] ?? 0) >= 60 ? 'text-blue-600' : 'text-orange-600') }}">
                                        {{ number_format($performance['task_percentage'] ?? 0, 1) }}%
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Behavior Details --}}
                        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                            <div class="px-5 py-3 bg-yellow-50 border-b border-yellow-100">
                                <h3 class="text-sm font-semibold text-gray-800 flex items-center gap-2">
                                    <i class="fas fa-smile text-yellow-600"></i>
                                    Behavior Details
                                </h3>
                            </div>
                            <div class="p-5 space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Behavior Target:</span>
                                    <span class="text-sm font-bold text-gray-800">{{ number_format($performance['behavior_target'] ?? 0, 1) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Behavior Obtained:</span>
                                    <span class="text-sm font-bold text-gray-800">{{ number_format($performance['behavior_obtained'] ?? 0, 1) }}</span>
                                </div>
                                <div class="flex justify-between pt-2 border-t border-gray-200">
                                    <span class="text-sm font-semibold text-gray-700">Behavior Score:</span>
                                    <span class="text-sm font-bold {{ ($performance['behavior_percentage'] ?? 0) >= 80 ? 'text-green-600' : (($performance['behavior_percentage'] ?? 0) >= 60 ? 'text-blue-600' : 'text-orange-600') }}">
                                        {{ number_format($performance['behavior_percentage'] ?? 0, 1) }}%
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Bonus/Penalty Details --}}
                        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                            <div class="px-5 py-3 bg-purple-50 border-b border-purple-100">
                                <h3 class="text-sm font-semibold text-gray-800 flex items-center gap-2">
                                    <i class="fas fa-star text-purple-600"></i>
                                    Bonus & Penalty Details
                                </h3>
                            </div>
                            <div class="p-5 space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Bonus Target:</span>
                                    <span class="text-sm font-bold text-gray-800">{{ number_format($performance['bonus_target'] ?? 0, 1) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Bonus Obtained:</span>
                                    <span class="text-sm font-bold text-gray-800">{{ number_format($performance['bonus_obtained'] ?? 0, 1) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Penalty Target:</span>
                                    <span class="text-sm font-bold text-gray-800">{{ number_format($performance['penalty_target'] ?? 0, 1) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Penalty Obtained:</span>
                                    <span class="text-sm font-bold text-gray-800">{{ number_format($performance['penalty_obtained'] ?? 0, 1) }}</span>
                                </div>
                                <div class="flex justify-between pt-2 border-t border-gray-200">
                                    <span class="text-sm font-semibold text-gray-700">Combined Score:</span>
                                    <span class="text-sm font-bold {{ ($performance['bonus_penalty_percentage'] ?? 0) >= 80 ? 'text-green-600' : (($performance['bonus_penalty_percentage'] ?? 0) >= 60 ? 'text-blue-600' : 'text-orange-600') }}">
                                        {{ number_format($performance['bonus_penalty_percentage'] ?? 0, 1) }}%
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TOTAL SUMMARY --}}
                    <div class="mt-6 bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg p-6 border border-gray-200">
                        <h3 class="text-base font-bold text-gray-800 mb-4">Total Score Summary</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="text-center">
                                <p class="text-xs text-gray-600 mb-1">Total Target</p>
                                <p class="text-lg font-bold text-gray-800">{{ number_format($performance['total_target'] ?? 0, 1) }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-xs text-gray-600 mb-1">Total Obtained</p>
                                <p class="text-lg font-bold text-gray-800">{{ number_format($performance['total_obtained'] ?? 0, 1) }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-xs text-gray-600 mb-1">Overall Percentage</p>
                                <p class="text-lg font-bold {{ ($performance['overall_percentage'] ?? 0) >= 80 ? 'text-green-600' : (($performance['overall_percentage'] ?? 0) >= 60 ? 'text-blue-600' : 'text-orange-600') }}">
                                    {{ number_format($performance['overall_percentage'] ?? 0, 1) }}%
                                </p>
                            </div>
                            <div class="text-center">
                                <p class="text-xs text-gray-600 mb-1">Rating</p>
                                <p class="text-lg font-bold text-gray-800">{{ $performance['rating'] ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-12 text-gray-500">
                        <i class="fas fa-chart-bar text-5xl mb-3 text-gray-300"></i>
                        <p class="text-lg">No performance data available</p>
                        <p class="text-sm mt-1">Monthly performance will appear here once calculated</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>