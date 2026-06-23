<x-app-layout>
    <div class="p-4">
        {{-- HEADER --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-xl font-bold text-gray-800">Daily Performance</h1>
                    <p class="text-sm text-gray-500 mt-1">Track your daily KPI performance</p>
                </div>
                <div class="flex gap-2">
                    <input type="date" id="datePicker" value="{{ $date }}" 
                        class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <a href="{{ route('kpi.daily') }}?date={{ date('Y-m-d', strtotime($date . ' -1 day')) }}"
                        class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-sm font-medium transition">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                    <a href="{{ route('kpi.daily') }}?date={{ date('Y-m-d', strtotime($date . ' +1 day')) }}"
                        class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-sm font-medium transition">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
            </div>

            {{-- PERFORMANCE CONTENT --}}
            <div class="p-6">
                @if($performance)
                    {{-- OVERALL SCORE --}}
                    <div class="mb-6 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg p-6 border border-blue-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Overall Daily Score</p>
                                <p class="text-4xl font-bold {{ $performance['overall_percentage'] >= 80 ? 'text-green-600' : ($performance['overall_percentage'] >= 60 ? 'text-blue-600' : 'text-orange-600') }}">
                                    {{ number_format($performance['overall_percentage'], 1) }}%
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-600 mb-1">Score</p>
                                <p class="text-2xl font-bold text-gray-800">
                                    {{ number_format($performance['total_obtained'], 1) }}/{{ number_format($performance['total_target'], 1) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- INDICATOR BREAKDOWN --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($performance['indicators'] ?? [] as $indicator)
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-check-circle text-blue-500"></i>
                                        <h4 class="text-sm font-semibold text-gray-700">{{ $indicator['name'] }}</h4>
                                    </div>
                                    <span class="text-sm font-bold {{ $indicator['percentage'] >= 80 ? 'text-green-600' : ($indicator['percentage'] >= 60 ? 'text-blue-600' : 'text-orange-600') }}">
                                        {{ number_format($indicator['percentage'], 1) }}%
                                    </span>
                                </div>
                                <div class="space-y-2">
                                    <div class="flex justify-between text-xs">
                                        <span class="text-gray-600">Target:</span>
                                        <span class="font-medium text-gray-800">{{ $indicator['target'] }}</span>
                                    </div>
                                    <div class="flex justify-between text-xs">
                                        <span class="text-gray-600">Obtained:</span>
                                        <span class="font-medium text-gray-800">{{ $indicator['obtained'] }}</span>
                                    </div>
                                    @if($indicator['remarks'] ?? '')
                                        <div class="mt-2 pt-2 border-t border-gray-200">
                                            <p class="text-xs text-gray-600">Remarks:</p>
                                            <p class="text-xs text-gray-800 mt-1">{{ $indicator['remarks'] }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach

                        @if(empty($performance['indicators']))
                            <div class="col-span-2 text-center py-8 text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-2 text-gray-300"></i>
                                <p>No performance data available for this date</p>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="text-center py-12 text-gray-500">
                        <i class="fas fa-calendar-times text-5xl mb-3 text-gray-300"></i>
                        <p class="text-lg">No performance data available</p>
                        <p class="text-sm mt-1">Performance tracking will appear here once recorded</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                $('#datePicker').on('change', function() {
                    let date = $(this).val();
                    window.location.href = "{{ route('kpi.daily') }}?date=" + date;
                });
            });
        </script>
    @endpush
</x-app-layout>