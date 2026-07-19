<x-app-layout>

    <div class="p-4">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            {{-- Header --}}
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-blue-600 flex items-center justify-center">
                        <i class="fas fa-credit-card text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-800">Payment List</h2>
                        <p class="text-sm text-gray-500">All employee salary payments</p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                {{-- Filters --}}
                <div class="flex flex-wrap gap-4 mb-5 bg-gray-50 p-4 rounded-xl border border-gray-200">
                    <div class="flex flex-col w-full md:w-1/4">
                        <label class="text-xs font-medium text-gray-600 mb-1">Employee</label>
                        <select id="filter_employee"
                            class="dt-filter-paymentListTable w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Employees</option>
                            @foreach ($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->employee_code }} -
                                    {{ $emp->personalInfo?->full_name ?? 'N/A' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex flex-col w-full md:w-1/4">
                        <label class="text-xs font-medium text-gray-600 mb-1">Payroll Run</label>
                        <select id="filter_payroll_run"
                            class="dt-filter-paymentListTable w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Payroll Runs</option>
                            @foreach ($payrollRuns as $run)
                                <option value="{{ $run->id }}">{{ $run->run_label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end w-full md:w-auto">
                        <button id="resetPaymentFilters"
                            class="px-4 py-2 text-sm font-medium text-white bg-gray-700 hover:bg-gray-800 rounded-lg transition active:scale-95">Reset</button>
                    </div>
                </div>

                <x-data-table id="paymentListTable" title="Employee Payments" icon="fa-solid fa-money-bill-wave"
                    buttonId="seeDetails" buttonText="See Details About Payroll"
                   :columns="[
                        '#',
                        'Payroll Run',
                        'Employee Name',
                        'Code',
                        'Basic Salary',
                        'Gross',
                        'Deductions',
                        'Net',
                        'Created By',
                        'Payment Status',
                        'Action',
                    ]" :ajaxUrl="route('payment-list.dataTable')" :filters="[
                        'employee_id' => '#filter_employee',
                        'payroll_run_id' => '#filter_payroll_run',
                    ]" :dtColumns="[
                        ['data' => 'DT_RowIndex', 'orderable' => false, 'searchable' => false],
                        ['data' => 'payroll_run_id', 'name' => 'payroll_run_id'],
                    
                        ['data' => 'employee_name'],
                        ['data' => 'employee_code'],
                        ['data' => 'basic_salary'],
                        ['data' => 'gross'],
                        ['data' => 'deductions'],
                        ['data' => 'net'],
                        ['data' => 'created_by'],
                        ['data' => 'payment_status', 'orderable' => false, 'searchable' => false],
                        ['data' => 'action', 'orderable' => false, 'searchable' => false],
                    ]" />
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                $(document).on('click', '#seeDetails', function(e) {
                    e.preventDefault();
                    window.location.href = "{{ route('payroll-runs.index') }}";
                });

                $(document).on('click', '#resetPaymentFilters', function() {
                    $('#filter_employee').val('');
                    $('#filter_payroll_run').val('');
                    $('.dt-filter-paymentListTable').trigger('change');
                });
            });

            function markAsPaid(id) {
                Swal.fire({
                    title: 'Mark as Paid?',
                    text: "This will mark this employee's payment as completed.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#2563eb',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, Mark as Paid!',
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        return $.ajax({
                            url: '{{ route('payroll-run-details.mark-paid', ':id') }}'.replace(':id', id),
                            type: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(res) {
                                if (res.status === 'success') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Paid!',
                                        text: res.message,
                                        timer: 1500,
                                        showConfirmButton: false
                                    });
                                    $('#paymentListTable').DataTable().ajax.reload(null, false);
                                } else {
                                    Swal.fire('Error', res.message, 'error');
                                }
                            },
                            error: function(xhr) {
                                Swal.fire('Error', xhr.responseJSON?.message || 'Server error',
                                    'error');
                            }
                        });
                    },
                    allowOutsideClick: false
                });
            }
        </script>
    @endpush
</x-app-layout>
