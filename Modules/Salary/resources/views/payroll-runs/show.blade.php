<x-app-layout>

    <div class="p-4">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            {{-- Header with Status --}}
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-blue-600 flex items-center justify-center">
                            <i class="fas fa-file-invoice text-white"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-800">{{ $run->run_label }}</h2>
                            <p class="text-sm text-gray-500">{{ $run->run_month->format('F Y') }} -
                                {{ $run->fiscalYear?->label ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div>
                        @php
                            $statusColors = [
                                'Calculated' => 'bg-yellow-100 text-yellow-800',
                                'Approved' => 'bg-green-100 text-green-800',
                                'Locked' => 'bg-purple-100 text-purple-800',
                            ];
                            $color = $statusColors[$run->status] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <span
                            class="px-4 py-2 text-sm font-bold rounded-full {{ $color }}">{{ $run->status }}</span>
                    </div>
                </div>
            </div>

            <div class="p-6">
                {{-- Workflow Action Buttons --}}
                <div class="flex flex-wrap gap-3 mb-6 p-4 bg-gray-50 rounded-xl border border-gray-200">
                    @if ($run->status === 'Calculated')
                        <button id="btnRecalculate" data-id="{{ $run->id }}"
                            class="px-5 py-2.5 text-sm font-semibold text-white bg-amber-500 hover:bg-amber-600 rounded-lg transition flex items-center gap-2 shadow-sm">
                            <i class="fas fa-sync-alt"></i> Recalculate
                        </button>
                        <button id="btnApprove" data-id="{{ $run->id }}"
                            class="px-5 py-2.5 text-sm font-semibold text-white bg-green-600 hover:bg-green-700 rounded-lg transition flex items-center gap-2 shadow-sm">
                            <i class="fas fa-check"></i> Approve
                        </button>
                    @elseif($run->status === 'Approved')
                        <button id="btnRecalculate" data-id="{{ $run->id }}"
                            class="px-5 py-2.5 text-sm font-semibold text-white bg-amber-500 hover:bg-amber-600 rounded-lg transition flex items-center gap-2 shadow-sm">
                            <i class="fas fa-sync-alt"></i> Recalculate
                        </button>
                        <button id="btnLock" data-id="{{ $run->id }}"
                            class="px-5 py-2.5 text-sm font-semibold text-white bg-purple-600 hover:bg-purple-700 rounded-lg transition flex items-center gap-2 shadow-sm">
                            <i class="fas fa-lock"></i> Lock
                        </button>
                    @elseif($run->status === 'Locked')
                        <div
                            class="px-5 py-2.5 text-sm font-semibold text-purple-700 bg-purple-100 rounded-lg flex items-center gap-2">
                            <i class="fas fa-lock"></i> Payroll is Locked - No further actions
                        </div>

                    @endif
                    <a href="{{ route('payroll-runs.index') }}"
                        class="px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg transition flex items-center gap-2">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>

                {{-- Summary Cards --}}
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
                    <div class="bg-blue-50 rounded-xl p-4 border border-blue-200">
                        <p class="text-xs font-medium text-blue-600 uppercase tracking-wide">Employees</p>
                        <p class="text-2xl font-bold text-blue-800 mt-1">{{ $totals['count'] }}</p>
                    </div>
                    <div class="bg-green-50 rounded-xl p-4 border border-green-200">
                        <p class="text-xs font-medium text-green-600 uppercase tracking-wide">Total Gross</p>
                        <p class="text-2xl font-bold text-green-800 mt-1">{{ number_format($totals['gross'], 2) }}</p>
                    </div>
                    <div class="bg-red-50 rounded-xl p-4 border border-red-200">
                        <p class="text-xs font-medium text-red-600 uppercase tracking-wide">Total Deductions</p>
                        <p class="text-2xl font-bold text-red-800 mt-1">{{ number_format($totals['deductions'], 2) }}
                        </p>
                    </div>
                    <div class="bg-purple-50 rounded-xl p-4 border border-purple-200">
                        <p class="text-xs font-medium text-purple-600 uppercase tracking-wide">Total Net Pay</p>
                        <p class="text-2xl font-bold text-purple-800 mt-1">{{ number_format($totals['net'], 2) }}</p>
                    </div>
                    <div class="bg-amber-50 rounded-xl p-4 border border-amber-200">
                        <p class="text-xs font-medium text-amber-600 uppercase tracking-wide">Avg. Per Employee</p>
                        <p class="text-2xl font-bold text-amber-800 mt-1">
                            {{ $totals['count'] > 0 ? number_format($totals['net'] / $totals['count'], 2) : '0.00' }}
                        </p>
                    </div>
                </div>

                {{-- Run Details --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6 p-4 bg-gray-50 rounded-xl">
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Run Type</p>
                        <p class="text-sm font-semibold text-gray-800">{{ $run->run_type }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Run Month</p>
                        <p class="text-sm font-semibold text-gray-800">{{ $run->run_month->format('F Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Approved By</p>
                        <p class="text-sm font-semibold text-gray-800">{{ $run->approvedBy?->name ?? 'Not yet' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Created By</p>
                        <p class="text-sm font-semibold text-gray-800">{{ $run->createdBy?->name ?? 'System' }}</p>
                    </div>
                    @if ($run->notes)
                        <div class="col-span-full">
                            <p class="text-xs text-gray-500 font-medium">Notes</p>
                            <p class="text-sm text-gray-700">{{ $run->notes }}</p>
                        </div>
                    @endif
                </div>

                {{-- Employee Breakdown Table --}}
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-md font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-users text-blue-600"></i>
                        Employee Payroll Breakdown
                    </h3>
                    <span class="text-sm text-gray-500">({{ count($employees) }} employees)</span>
                </div>

                <div class="space-y-3">
                    @forelse($employees as $i => $emp)
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            {{-- Employee Summary Row (clickable) --}}
                            <button onclick="toggleBreakdown({{ $i }})"
                                class="w-full flex items-center justify-between px-4 py-3 bg-gray-50 hover:bg-gray-100 transition text-left">
                                <div class="flex items-center gap-4">
                                    <span class="text-sm font-medium text-gray-500 w-6">{{ $i + 1 }}</span>
                                    <div>
                                        <span
                                            class="text-sm font-semibold text-gray-800">{{ $emp['employee_name'] }}</span>
                                        <span class="text-xs text-gray-500 ml-2">({{ $emp['employee_code'] }})</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-6">
                                    <div class="text-right">
                                        <p class="text-xs text-gray-500">Gross</p>
                                        <p class="text-sm font-semibold text-green-700">
                                            {{ number_format($emp['gross'], 2) }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xs text-gray-500">Deductions</p>
                                        <p class="text-sm font-semibold text-red-700">
                                            {{ number_format($emp['deductions'], 2) }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xs text-gray-500">Net Pay</p>
                                        <p class="text-sm font-bold text-purple-700">
                                            {{ number_format($emp['net'], 2) }}</p>
                                    </div>
                                    <i class="fas fa-chevron-down text-gray-400 transition-transform"
                                        id="chevron-{{ $i }}"></i>
                                </div>
                            </button>

                            {{-- Component Breakdown (hidden by default) --}}
                            <div id="breakdown-{{ $i }}" class="hidden border-t border-gray-200">
                                <div class="p-4 bg-white">
                                    {{-- Attendance Summary --}}
                                    @if (isset($emp['attendance_summary']))
                                        <div class="mb-4 p-3 bg-gray-50 rounded-lg border border-gray-200">
                                            <p
                                                class="text-xs font-semibold text-gray-600 uppercase mb-2 flex items-center gap-1">
                                                <i class="fas fa-calendar-check text-blue-500"></i> Attendance Summary
                                            </p>
                                           <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-2 text-xs">
                                                <div class="text-center p-2 bg-green-50 rounded">
                                                    <p class="font-bold text-green-700">
                                                        {{ $emp['attendance_summary']['present'] }}</p>
                                                    <p class="text-gray-500">Present</p>
                                                </div>
                                                <div class="text-center p-2 bg-blue-50 rounded">
                                                    <p class="font-bold text-blue-700">
                                                        {{ $emp['attendance_summary']['working_days'] }}</p>
                                                    <p class="text-gray-500">Working Days</p>
                                                </div>
                                                <div class="text-center p-2 bg-red-50 rounded">
                                                    <p class="font-bold text-red-700">
                                                        {{ $emp['attendance_summary']['late_days'] ?? 0 }} days</p>
                                                    <p class="text-gray-500">Late
                                                        ({{ $emp['attendance_summary']['late_minutes'] }}m)</p>
                                                </div>
                                                <div class="text-center p-2 bg-purple-50 rounded">
                                                    <p class="font-bold text-purple-700">
                                                        {{ $emp['attendance_summary']['overtime_minutes'] }}m</p>
                                                    <p class="text-gray-500">Overtime</p>
                                                </div>
                                                <div class="text-center p-2 bg-orange-50 rounded">
                                                    <p class="font-bold text-orange-700">
                                                        {{ $emp['attendance_summary']['half_days'] }}</p>
                                                    <p class="text-gray-500">Half Days</p>
                                                </div>
                                                <div class="text-center p-2 bg-red-100 rounded">
                                                    <p class="font-bold text-red-800">
                                                        {{ $emp['attendance_summary']['absent'] }}</p>
                                                    <p class="text-gray-500">Absent</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Basic Salary:
                                        {{ number_format($emp['basic_salary'], 2) }}</p>
                                    <table class="min-w-full divide-y divide-gray-100">
                                        <thead>
                                            <tr class="text-xs text-gray-500 uppercase">
                                                <th class="px-3 py-2 text-left">Component</th>
                                                <th class="px-3 py-2 text-left">Type</th>
                                                <th class="px-3 py-2 text-right">Value</th>
                                                <th class="px-3 py-2 text-left">Calculation</th>
                                                <th class="px-3 py-2 text-right font-semibold">Amount
                                                    ({{ session('currency', 'BDT') }})</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-50">
                                            @foreach ($emp['components'] as $comp)
                                                <tr class="text-sm">
                                                    <td class="px-3 py-2 text-gray-800">{{ $comp['name'] }}</td>
                                                    <td class="px-3 py-2">
                                                        @if ($comp['type'] === 'Earning')
                                                            <span
                                                                class="text-green-700 bg-green-50 px-2 py-0.5 rounded text-xs font-medium">Earning</span>
                                                        @else
                                                            <span
                                                                class="text-red-700 bg-red-50 px-2 py-0.5 rounded text-xs font-medium">Deduction</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-3 py-2 text-right text-gray-600">
                                                        @if ($comp['is_pct'])
                                                            {{ $comp['value'] }}%
                                                        @else
                                                            {{ number_format($comp['value'], 2) }}
                                                        @endif
                                                    </td>
                                                    <td class="px-3 py-2 text-gray-500 text-xs">{{ $comp['calc'] }}
                                                    </td>
                                                    <td
                                                        class="px-3 py-2 text-right font-medium {{ $comp['type'] === 'Earning' ? 'text-green-700' : 'text-red-700' }}">
                                                        {{ $comp['type'] === 'Earning' ? '+' : '-' }}{{ number_format($comp['amount'], 2) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="bg-gray-50 text-sm font-semibold">
                                            <tr>
                                                <td colspan="4" class="px-3 py-2 text-gray-700">Net Pay</td>
                                                <td class="px-3 py-2 text-right text-purple-700">
                                                    {{ number_format($emp['net'], 2) }}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500">No employee data found for this payroll run.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function toggleBreakdown(index) {
                const el = document.getElementById('breakdown-' + index);
                const chevron = document.getElementById('chevron-' + index);
                if (el.classList.contains('hidden')) {
                    el.classList.remove('hidden');
                    chevron.style.transform = 'rotate(180deg)';
                } else {
                    el.classList.add('hidden');
                    chevron.style.transform = 'rotate(0deg)';
                }
            }

            $(document).ready(function() {
                // Recalculate button
                $(document).on('click', '#btnRecalculate', function() {
                    const id = $(this).data('id');
                    Swal.fire({
                        title: 'Recalculate Payroll?',
                        text: 'This will re-calculate all employee amounts based on current salary structures.',
                        icon: 'info',
                        showCancelButton: true,
                        confirmButtonColor: '#f59e0b',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Yes, Recalculate!',
                        showLoaderOnConfirm: true,
                        preConfirm: () => {
                            return $.ajax({
                                url: '{{ route('payroll-runs.recalculate', ':id') }}'
                                    .replace(':id', id),
                                type: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                                        'content')
                                },
                                success: function(res) {
                                    if (res.status === 'success') {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Recalculated!',
                                            text: res.message
                                        });
                                        location.reload();
                                    } else {
                                        Swal.fire('Error', res.message, 'error');
                                    }
                                },
                                error: function(xhr) {
                                    Swal.fire('Error', xhr.responseJSON?.message ||
                                        'Server error', 'error');
                                }
                            });
                        },
                        allowOutsideClick: false
                    });
                });

                // Approve button
                $(document).on('click', '#btnApprove', function() {
                    const id = $(this).data('id');
                    Swal.fire({
                        title: 'Approve Payroll?',
                        text: 'Once approved, you can lock the payroll. Are you sure?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#16a34a',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Yes, Approve!',
                        showLoaderOnConfirm: true,
                        preConfirm: () => {
                            return $.ajax({
                                url: '{{ route('payroll-runs.approve', ':id') }}'.replace(
                                    ':id', id),
                                type: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                                        'content')
                                },
                                success: function(res) {
                                    if (res.status === 'success') {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Approved!',
                                            text: res.message
                                        });
                                        location.reload();
                                    } else {
                                        Swal.fire('Error', res.message, 'error');
                                    }
                                },
                                error: function(xhr) {
                                    Swal.fire('Error', xhr.responseJSON?.message ||
                                        'Server error', 'error');
                                }
                            });
                        },
                        allowOutsideClick: false
                    });
                });

                // Lock button
                $(document).on('click', '#btnLock', function() {
                    const id = $(this).data('id');
                    Swal.fire({
                        title: 'Lock Payroll?',
                        text: 'Once locked, no further changes can be made. This action cannot be undone!',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#9333ea',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Yes, Lock!',
                        showLoaderOnConfirm: true,
                        preConfirm: () => {
                            return $.ajax({
                                url: '{{ route('payroll-runs.lock', ':id') }}'.replace(
                                    ':id', id),
                                type: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                                        'content')
                                },
                                success: function(res) {
                                    if (res.status === 'success') {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Locked!',
                                            text: res.message
                                        });
                                        location.reload();
                                    } else {
                                        Swal.fire('Error', res.message, 'error');
                                    }
                                },
                                error: function(xhr) {
                                    Swal.fire('Error', xhr.responseJSON?.message ||
                                        'Server error', 'error');
                                }
                            });
                        },
                        allowOutsideClick: false
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>
