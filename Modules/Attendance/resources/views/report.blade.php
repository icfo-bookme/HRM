<x-app-layout>
    <div class="p-4">
        {{-- FILTER SECTION --}}
        <div class="flex flex-col md:flex-row md:items-end gap-4 mb-5 bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            {{-- Month Filter --}}
            <div class="flex flex-col w-full md:w-1/5">
                <x-form-select label="Month" id="filter_month" class="dt-filter-attendanceReportTable">
                    @for ($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $m == now()->month ? 'selected' : '' }}>
                            {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                        </option>
                    @endfor
                </x-form-select>
            </div>

            {{-- Year Filter --}}
            <div class="flex flex-col w-full md:w-1/5">
                <x-form-select label="Year" id="filter_year" class="dt-filter-attendanceReportTable">
                    @for ($y = now()->year - 2; $y <= now()->year + 1; $y++)
                        <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </x-form-select>
            </div>

            {{-- Employee Filter --}}
            <div class="flex flex-col w-full md:w-1/4">
                <x-form-select label="Employee" id="filter_employee" class="dt-filter-attendanceReportTable">
                    <option value="">All Employees</option>
                    @foreach ($employees as $employee)
                        <option value="{{ $employee->id }}">{{ $employee->employee_code }} - {{ $employee->full_name }}</option>
                    @endforeach
                </x-form-select>
            </div>

            {{-- Reset Button --}}
            <div class="w-full md:w-auto flex items-end">
                <button id="resetFilters"
                    class="px-4 py-2 text-sm font-medium text-white bg-gray-700 hover:bg-gray-800 rounded-lg transition active:scale-95">
                    Reset
                </button>
            </div>
        </div>

        {{-- LEGEND SECTION --}}
        <div class="mb-5 bg-white p-3 rounded-xl border border-gray-200 shadow-sm">
            <div class="flex flex-wrap items-center gap-x-5 gap-y-2 text-xs font-medium">
                <span class="text-gray-700 font-semibold">Legend:</span>
                <span class="inline-flex items-center gap-1.5">
                    <span class="w-4 h-4 rounded bg-green-100 border border-green-300 flex items-center justify-center text-[10px] font-bold text-green-700">P</span> Present
                </span>
                <span class="inline-flex items-center gap-1.5">
                    <span class="w-4 h-4 rounded bg-orange-100 border border-orange-300 flex items-center justify-center text-[10px] font-bold text-orange-700">LP</span> Late Present
                </span>
                <span class="inline-flex items-center gap-1.5">
                    <span class="w-4 h-4 rounded bg-yellow-100 border border-yellow-300 flex items-center justify-center text-[10px] font-bold text-yellow-700">EL</span> Early Leave Office
                </span>
                <span class="inline-flex items-center gap-1.5">
                    <span class="w-4 h-4 rounded bg-red-100 border border-red-300 flex items-center justify-center text-[10px] font-bold text-red-700">A</span> Absent
                </span>
                <span class="inline-flex items-center gap-1.5">
                    <span class="w-4 h-4 rounded bg-blue-100 border border-blue-300 flex items-center justify-center text-[10px] font-bold text-blue-700">H</span> Holiday
                </span>
                <span class="inline-flex items-center gap-1.5">
                    <span class="w-4 h-4 rounded bg-yellow-100 border border-yellow-300 flex items-center justify-center text-[10px] font-bold text-yellow-700">L</span> On Leave
                </span>
                <span class="inline-flex items-center gap-1.5">
                    <span class="w-4 h-4 rounded bg-orange-100 border border-orange-300 flex items-center justify-center text-[10px] font-bold text-orange-700">HD</span> Half Day
                </span>
                <span class="inline-flex items-center gap-1.5">
                    <span class="w-4 h-4 rounded bg-purple-100 border border-purple-300 flex items-center justify-center text-[10px] font-bold text-purple-700">W</span> Weekend
                </span>
            </div>
        </div>

        {{-- DATA-TABLE COMPONENT --}}
        @php
            $dayHeaders = range(1, 31);
            $baseCols = ['SL', 'Employee Name'];
            $allCols = array_merge($baseCols, $dayHeaders, ['Summary']);

            $dtBase = [
                ['data' => 'DT_RowIndex', 'name' => 'id', 'width' => '5px', 'orderable' => false, 'searchable' => false],
                ['data' => 'employee_name', 'name' => 'employee_name'],
            ];
            $dtDayCols = array_map(function($d) {
                return ['data' => 'day_' . $d, 'name' => 'day_' . $d, 'orderable' => false, 'searchable' => false, 'width' => '28px'];
            }, range(1, 31));
            $dtSummary = [['data' => 'summary', 'name' => 'summary', 'orderable' => false, 'searchable' => false]];
            $allDtCols = array_merge($dtBase, $dtDayCols, $dtSummary);
        @endphp

        <x-data-table id="attendanceReportTable" title="Monthly Attendance Report" icon="fa-solid fa-calendar-alt"
            buttonId="" buttonText="" :columns="$allCols"
            :ajaxUrl="route('attendance.report.dataTable')"
            :dtColumns="$allDtCols"
            :filters="[
                'month' => '#filter_month',
                'year' => '#filter_year',
                'employee_id' => '#filter_employee',
            ]" :exportButtons="true" :orderColumn="0" :orderDirection="'asc'" />
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                $('#resetFilters').on('click', function() {
                    $('#filter_month').val('{{ now()->month }}');
                    $('#filter_year').val('{{ now()->year }}');
                    $('#filter_employee').val('');
                    $('.dt-filter-attendanceReportTable').trigger('change');
                });
            });
        </script>
    @endpush
</x-app-layout>