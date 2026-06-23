<x-app-layout>

    <div class="p-4">
        {{-- FILTERS --}}
        <div
            class="flex flex-col md:flex-row md:items-end gap-4 mb-5 bg-white p-4 rounded-xl border border-gray-200 shadow-sm">

            <div class="flex flex-col w-full md:w-1/4">
                <x-form-select label="Employee" id="filter_employee" class="dt-filter-leaveEncashmentTable">
                    <option value="">All Employees</option>
                    @foreach ($employees as $employee)
                        <option value="{{ $employee->id }}">{{ $employee->employee_code }} - {{ $employee->full_name }}</option>
                    @endforeach
                </x-form-select>
            </div>

            <div class="flex flex-col w-full md:w-1/4">
                <x-form-select label="Leave Type" id="filter_leave_type" class="dt-filter-leaveEncashmentTable">
                    <option value="">All Leave Types</option>
                    @foreach ($leaveTypes as $leaveType)
                        <option value="{{ $leaveType->id }}">{{ $leaveType->name }}</option>
                    @endforeach
                </x-form-select>
            </div>

            <div class="flex flex-col w-full md:w-1/4">
                <x-form-select label="Status" id="filter_status" class="dt-filter-leaveEncashmentTable">
                    <option value="">All Status</option>
                    <option value="Pending">Pending</option>
                    <option value="Approved">Approved</option>
                    <option value="Paid">Paid</option>
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
        <x-data-table id="leaveEncashmentTable" title="Leave Encashment Management" icon="fa-solid fa-money-bill-wave"
            buttonId="btnAddEncashment" buttonText="New Encashment Request"
            :columns="['SL', 'Employee', 'Leave Type', 'Date', 'Days', 'Rate', 'Total', 'Status', 'Created', 'Action']"
            :ajaxUrl="route('leave-encashment.dataTable')"
            :orderColumn="8"
            :orderDirection="'desc'"
            :dtColumns="[
                ['data' => 'DT_RowIndex', 'name' => 'id', 'width' => '40px', 'orderable' => false],
                ['data' => 'employee', 'width' => '170px'],
                ['data' => 'leave_type', 'width' => '120px'],
                ['data' => 'encashment_date', 'width' => '100px'],
                ['data' => 'days_encashed', 'width' => '60px', 'className' => 'text-center'],
                ['data' => 'amount_per_day', 'width' => '100px', 'className' => 'text-right'],
                ['data' => 'total_amount', 'width' => '120px', 'className' => 'text-right font-bold'],
                ['data' => 'status', 'width' => '90px'],
                ['data' => 'created_at', 'width' => '90px'],
                ['data' => 'action', 'orderable' => false, 'searchable' => false, 'width' => '100px'],
            ]"
            :filters="[
                'employee_id' => '#filter_employee',
                'leave_type_id' => '#filter_leave_type',
                'status' => '#filter_status',
            ]"
            :exportButtons="true" />
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                $(document).on('click', '#btnAddEncashment', function(e) {
                    e.preventDefault();
                    window.location.href = "{{ route('leave-encashment.create') }}";
                });
            });

            function leaveEncashmentEdit(id) {
                window.location.href = "{{ route('leave-encashment.edit', ':id') }}".replace(':id', id);
            }

            function leaveEncashmentDelete(id) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This will revert the leave balance if already approved.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#4b5563',
                    confirmButtonText: 'Yes, delete it!'
                }).then((r) => {
                    if (r.isConfirmed) {
                        $.post("{{ route('leave-encashment.destroy', ':id') }}".replace(':id', id), { _method: 'DELETE' }, function(res) {
                            if (res.status === 'success' || res.status === true) {
                                Toastify({ text: res.message, duration: 3000, gravity: "bottom", position: "right", style: { background: "linear-gradient(135deg, #dc2626, #f87171)" } }).showToast();
                                $('#leaveEncashmentTable').DataTable().ajax.reload(null, false);
                            } else {
                                Swal.fire('Error', res.message, 'error');
                            }
                        }).fail(function() { Swal.fire('Error', 'Server error.', 'error'); });
                    }
                });
            }

            function leaveEncashmentApprove(id) {
                Swal.fire({
                    title: 'Approve Encashment?',
                    text: "This will update the employee's leave balance (encashed_days will increase).",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#16a34a',
                    cancelButtonColor: '#4b5563',
                    confirmButtonText: 'Yes, Approve'
                }).then((r) => {
                    if (r.isConfirmed) {
                        $.post("{{ route('leave-encashment.approve', ':id') }}".replace(':id', id), function(res) {
                            if (res.status === 'success' || res.status === true) {
                                Toastify({ text: res.message, duration: 3000, gravity: "bottom", position: "right", style: { background: "linear-gradient(135deg, #16a34a, #4ade80)" } }).showToast();
                                $('#leaveEncashmentTable').DataTable().ajax.reload(null, false);
                            } else {
                                Swal.fire('Error', res.message, 'error');
                            }
                        }).fail(function() { Swal.fire('Error', 'Server error.', 'error'); });
                    }
                });
            }

            $('#resetFilters').on('click', function() {
                $('#filter_employee').val('');
                $('#filter_leave_type').val('');
                $('#filter_status').val('');
                $('.dt-filter-leaveEncashmentTable').trigger('change');
            });
        </script>
    @endpush
</x-app-layout>