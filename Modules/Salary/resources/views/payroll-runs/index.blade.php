<x-app-layout>

    <div class="p-4">
        <div
            class="flex flex-col md:flex-row md:items-end gap-4 mb-5 bg-white p-4 rounded-xl border border-gray-200 shadow-sm">

            {{-- Status Filter --}}
            <div class="flex flex-col w-full md:w-1/4">
                <x-form-select label="Status" id="filter_status" class="dt-filter-payrollRunTable">
                    <option value="">All Status</option>
                    <option value="Calculated">Calculated</option>
                    <option value="Approved">Approved</option>
                    <option value="Locked">Locked</option>
                    <option value="Cancelled">Cancelled</option>
                </x-form-select>
            </div>

            {{-- Run Type Filter --}}
            <div class="flex flex-col w-full md:w-1/4">
                <x-form-select label="Run Type" id="filter_run_type" class="dt-filter-payrollRunTable">
                    <option value="">All Types</option>
                    <option value="Regular">Regular</option>
                    <option value="Bonus">Bonus</option>
                    <option value="Advance">Advance</option>
                    <option value="Adjustment">Adjustment</option>
                </x-form-select>
            </div>

            {{-- Reset Button --}}
            <div class="w-full md:w-auto flex items-end">
                <button id="resetFilters"
                    class="px-4 py-2 text-sm font-medium text-white bg-gray-700 hover:bg-gray-800
                   rounded-lg transition active:scale-95">
                    Reset
                </button>
            </div>

        </div>

        <x-data-table id="payrollRunTable" title="Payroll Runs" icon="fa-solid fa-file-invoice"
            buttonId="btnAddPayrollRun" buttonText="Generate Payroll" :columns="[  'Fiscal Year', 'Month', 'Type', 'Employees', 'Gross', 'Deductions', 'Net', 'Status', 'Created At', 'Action']" :ajaxUrl="route('payroll-runs.dataTable')"
            :dtColumns="[
               
                ['data' => 'fiscal_year_id'],
                ['data' => 'run_month'],
                ['data' => 'run_type'],
                ['data' => 'total_employees'],
                ['data' => 'total_gross'],
                ['data' => 'total_deductions'],
                ['data' => 'total_net'],
                ['data' => 'status'],
                ['data' => 'created_at'],
                ['data' => 'action', 'orderable' => false, 'searchable' => false],
            ]" :filters="[
                'status' => '#filter_status',
                'run_type' => '#filter_run_type',
            ]" :exportButtons="true" />
    </div>

    @push('scripts')
        <script>
            function payrollRunDelete(id) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This action cannot be undone!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#4b5563',
                    confirmButtonText: 'Yes, delete it!'
                }).then((r) => {
                    if (r.isConfirmed) {
                        let deleteUrl = "{{ route('payroll-runs.destroy', ':id') }}".replace(':id', id);

                        $.post(deleteUrl, {
                            _method: 'DELETE',
                        }, function(res) {
                            if (res.status === 'success' || res.status === true) {
                                Toastify({
                                    text: res.message || 'Deleted successfully',
                                    duration: 3000,
                                    gravity: "bottom",
                                    position: "right",
                                    style: {
                                        background: "linear-gradient(135deg, #dc2626, #f87171)"
                                    },
                                }).showToast();
                                $('#payrollRunTable').DataTable().ajax.reload(null, false);
                            } else {
                                Swal.fire('Error', res.message || 'Deletion failed.', 'error');
                            }
                        }).fail(function() {
                            Swal.fire('Error', 'Failed to communicate with server.', 'error');
                        });
                    }
                });
            }

            $('#resetFilters').on('click', function() {
                $('#filter_status').val('');
                $('#filter_run_type').val('');
                $('.dt-filter-payrollRunTable').trigger('change');
            });

            $(document).ready(function() {
                $(document).on('click', '#btnAddPayrollRun', function(e) {
                    e.preventDefault();
                    window.location.href = '{{ route("payroll-runs.generate") }}';
                });
            });
        </script>
    @endpush
</x-app-layout>