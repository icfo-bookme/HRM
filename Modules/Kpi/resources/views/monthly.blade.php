<x-app-layout>
    <div class="p-4">
        {{-- HEADER --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-xl font-bold text-gray-800">Monthly Performance</h1>
                    <p class="text-sm text-gray-500 mt-1">View your monthly KPI performance</p>
                </div>
                <div class="flex gap-2">
                    <select id="monthPicker" class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @for ($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ $month == $i ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::createFromDate(2024, $i, 1)->format('F') }}
                            </option>
                        @endfor
                    </select>
                    <input type="number" id="yearPicker" value="{{ $year }}" min="2020" max="2099"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-24">
                    <button onclick="goToMonthly()"
                        class="px-4 py-2 bg-blue-900 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>

            {{-- PERFORMANCE CONTENT --}}
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

                    {{-- CATEGORY BREAKDOWN --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Attendance --}}
                        <div class="bg-gray-50 rounded-lg p-5 border border-gray-100">
                            <div class="flex items-center gap-2 mb-4">
                                <i class="fas fa-clock text-blue-500 text-lg"></i>
                                <h3 class="text-base font-semibold text-gray-800">Attendance</h3>
                            </div>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Present Days:</span>
                                    <span class="text-sm font-bold text-gray-800">{{ $performance['present_days'] ?? 0 }}/{{ $performance['working_days'] ?? 0 }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Late Days:</span>
                                    <span class="text-sm font-bold text-gray-800">{{ $performance['late_days'] ?? 0 }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Score:</span>
                                    <span class="text-sm font-bold {{ ($performance['attendance_percentage'] ?? 0) >= 80 ? 'text-green-600' : (($performance['attendance_percentage'] ?? 0) >= 60 ? 'text-blue-600' : 'text-orange-600') }}">
                                        {{ number_format($performance['attendance_percentage'] ?? 0, 1) }}%
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Tasks --}}
                        <div class="bg-gray-50 rounded-lg p-5 border border-gray-100">
                            <div class="flex items-center gap-2 mb-4">
                                <i class="fas fa-tasks text-green-500 text-lg"></i>
                                <h3 class="text-base font-semibold text-gray-800">Tasks</h3>
                            </div>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Completed:</span>
                                    <span class="text-sm font-bold text-gray-800">{{ $performance['completed_tasks'] ?? 0 }}/{{ $performance['total_assigned_tasks'] ?? 0 }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Score:</span>
                                    <span class="text-sm font-bold {{ ($performance['task_percentage'] ?? 0) >= 80 ? 'text-green-600' : (($performance['task_percentage'] ?? 0) >= 60 ? 'text-blue-600' : 'text-orange-600') }}">
                                        {{ number_format($performance['task_percentage'] ?? 0, 1) }}%
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Behavior --}}
                        <div class="bg-gray-50 rounded-lg p-5 border border-gray-100">
                            <div class="flex items-center gap-2 mb-4">
                                <i class="fas fa-smile text-yellow-500 text-lg"></i>
                                <h3 class="text-base font-semibold text-gray-800">Behavior</h3>
                            </div>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Score:</span>
                                    <span class="text-sm font-bold text-gray-800">{{ number_format($performance['behavior_obtained'] ?? 0, 1) }}/{{ number_format($performance['behavior_target'] ?? 0, 1) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Percentage:</span>
                                    <span class="text-sm font-bold {{ ($performance['behavior_percentage'] ?? 0) >= 80 ? 'text-green-600' : (($performance['behavior_percentage'] ?? 0) >= 60 ? 'text-blue-600' : 'text-orange-600') }}">
                                        {{ number_format($performance['behavior_percentage'] ?? 0, 1) }}%
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Bonus/Penalty --}}
                        <div class="bg-gray-50 rounded-lg p-5 border border-gray-100">
                            <div class="flex items-center gap-2 mb-4">
                                <i class="fas fa-star text-purple-500 text-lg"></i>
                                <h3 class="text-base font-semibold text-gray-800">Bonus & Penalty</h3>
                            </div>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Bonus:</span>
                                    <span class="text-sm font-bold text-gray-800">{{ number_format($performance['bonus_obtained'] ?? 0, 1) }}/{{ number_format($performance['bonus_target'] ?? 0, 1) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Penalty:</span>
                                    <span class="text-sm font-bold text-gray-800">{{ number_format($performance['penalty_obtained'] ?? 0, 1) }}/{{ number_format($performance['penalty_target'] ?? 0, 1) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Combined:</span>
                                    <span class="text-sm font-bold {{ ($performance['bonus_penalty_percentage'] ?? 0) >= 80 ? 'text-green-600' : (($performance['bonus_penalty_percentage'] ?? 0) >= 60 ? 'text-blue-600' : 'text-orange-600') }}">
                                        {{ number_format($performance['bonus_penalty_percentage'] ?? 0, 1) }}%
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ACTION BUTTONS --}}
                    <div class="mt-6 flex gap-3">
                        <a href="{{ route('kpi.monthly.detail', [$employee->id, $year, $month]) }}"
                            class="px-4 py-2 bg-blue-900 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition">
                            <i class="fas fa-eye mr-1"></i> View Detailed Report
                        </a>
                        @if($performance['status'] === 'Open')
                            <a href="{{ route('kpi.reviews.create', $employee->id) }}?year={{ $year }}&month={{ $month }}"
                                class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium transition">
                                <i class="fas fa-clipboard-check mr-1"></i> Create Review
                            </a>
                        @endif
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

    @push('scripts')
        <script>
            function goToMonthly() {
                let month = $('#monthPicker').val();
                let year = $('#yearPicker').val();
                window.location.href = "{{ route('kpi.monthly') }}?month=" + month + "&year=" + year;
            }
        </script>
    @endpush
</x-app-layout>