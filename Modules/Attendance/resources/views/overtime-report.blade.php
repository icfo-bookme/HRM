<x-app-layout>
    <div class="p-4">
        {{-- LEGEND --}}
        <div class="mb-4 flex flex-wrap items-center gap-x-5 gap-y-2 text-xs font-medium bg-white p-3 rounded-xl border border-gray-200 shadow-sm">
            <span class="text-gray-700 font-semibold">Status:</span>
            <span class="inline-flex items-center gap-1"><span class="w-3 h-3 rounded bg-green-100 border border-green-300"></span> Present</span>
            <span class="inline-flex items-center gap-1"><span class="w-3 h-3 rounded bg-red-100 border border-red-300"></span> Absent</span>
            <span class="inline-flex items-center gap-1"><span class="w-3 h-3 rounded bg-blue-100 border border-blue-300"></span> Holiday</span>
            <span class="inline-flex items-center gap-1"><span class="w-3 h-3 rounded bg-yellow-100 border border-yellow-300"></span> On Leave</span>
            <span class="inline-flex items-center gap-1"><span class="w-3 h-3 rounded bg-orange-100 border border-orange-300"></span> Half Day</span>
            <span class="inline-flex items-center gap-1"><span class="w-3 h-3 rounded bg-purple-100 border border-purple-300"></span> Weekend</span>
            <span class="ml-2 text-purple-700 font-semibold">| <span class="bg-purple-100 px-1.5 py-0.5 rounded text-purple-700 border border-purple-300">1h 30m</span> = Overtime shown in hours/minutes</span>
        </div>

        {{-- FILTER SECTION --}}
        <div class="flex flex-col md:flex-row md:items-end gap-4 mb-5 bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <div class="flex flex-col w-full md:w-1/5">
                <x-form-select label="Month" id="filter_month" class="dt-filter-attendanceOvertimeTable">
                    @for ($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $m == now()->month ? 'selected' : '' }}>
                            {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                        </option>
                    @endfor
                </x-form-select>
            </div>

            <div class="flex flex-col w-full md:w-1/5">
                <x-form-select label="Year" id="filter_year" class="dt-filter-attendanceOvertimeTable">
                    @for ($y = now()->year - 2; $y <= now()->year + 1; $y++)
                        <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </x-form-select>
            </div>

            <div class="flex flex-col w-full md:w-1/4">
                <x-form-select label="Employee" id="filter_employee" class="dt-filter-attendanceOvertimeTable">
                    <option value="">All Employees</option>
                    @foreach ($employees as $employee)
                        <option value="{{ $employee->id }}">{{ $employee->employee_code }} - {{ $employee->full_name }}</option>
                    @endforeach
                </x-form-select>
            </div>

            <div class="w-full md:w-auto flex items-end">
                <button id="resetFilters"
                    class="px-4 py-2 text-sm font-medium text-white bg-gray-700 hover:bg-gray-800 rounded-lg transition active:scale-95">
                    Reset
                </button>
            </div>
        </div>

        {{-- DATA-TABLE --}}
        @php
            $dayHeaders = range(1, 31);
            $allCols = array_merge(['SL', 'Employee Name', 'Total Overtime'], $dayHeaders);

            $dtCols = array_merge(
                [
                    ['data' => 'DT_RowIndex', 'name' => 'id', 'width' => '5px', 'orderable' => false, 'searchable' => false],
                    ['data' => 'employee_name', 'name' => 'employee_name'],
                    ['data' => 'total_overtime', 'name' => 'total_overtime', 'orderable' => false, 'searchable' => false],
                ],
                array_map(function($d) {
                    return ['data' => 'day_' . $d, 'name' => 'day_' . $d, 'orderable' => false, 'searchable' => false, 'width' => '60px'];
                }, range(1, 31))
            );
        @endphp

        <x-data-table id="attendanceOvertimeTable" title="Monthly Overtime Report" icon="fa-solid fa-clock"
            buttonId="" buttonText="" :columns="$allCols"
            :ajaxUrl="route('attendance.overtime.dataTable')"
            :dtColumns="$dtCols"
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
                    $('.dt-filter-attendanceOvertimeTable').trigger('change');
                });
            });
        </script>
    @endpush
</x-app-layout>