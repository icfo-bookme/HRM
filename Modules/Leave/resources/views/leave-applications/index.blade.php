<x-app-layout>

    <div class="p-4">
        {{-- FILTERS --}}
        <div
            class="flex flex-col md:flex-row md:items-end gap-4 mb-5 bg-white p-4 rounded-xl border border-gray-200 shadow-sm">

            {{-- Employee Filter --}}
            <div class="flex flex-col w-full md:w-1/5">
                <x-form-select label="Employee" id="filter_employee" class="dt-filter-leaveApplicationTable">
                    <option value="">All Employees</option>
                    @foreach ($employees ?? [] as $employee)
                        <option value="{{ $employee->id }}">{{ $employee->employee_code }} - {{ $employee->full_name }}</option>
                    @endforeach
                </x-form-select>
            </div>

            {{-- Leave Type Filter --}}
            <div class="flex flex-col w-full md:w-1/5">
                <x-form-select label="Leave Type" id="filter_leave_type" class="dt-filter-leaveApplicationTable">
                    <option value="">All Leave Types</option>
                    @foreach ($leaveTypes ?? [] as $leaveType)
                        <option value="{{ $leaveType->id }}">{{ $leaveType->name }}</option>
                    @endforeach
                </x-form-select>
            </div>

            {{-- Status Filter --}}
            <div class="flex flex-col w-full md:w-1/5">
                <x-form-select label="Status" id="filter_status" class="dt-filter-leaveApplicationTable">
                    <option value="">All Status</option>
                    <option value="Draft">Draft</option>
                    <option value="Pending">Pending</option>
                    <option value="Approved">Approved</option>
                    <option value="Rejected">Rejected</option>
                    <option value="Cancelled">Cancelled</option>
                    <option value="Withdrawn">Withdrawn</option>
                </x-form-select>
            </div>

            {{-- Date From --}}
            <div class="flex flex-col w-full md:w-1/5">
                <x-form-input type="date" label="From Date" name="filter_from_date" id="filter_from_date" class="dt-filter-leaveApplicationTable" />
            </div>

            {{-- Date To --}}
            <div class="flex flex-col w-full md:w-1/5">
                <x-form-input type="date" label="To Date" name="filter_to_date" id="filter_to_date" class="dt-filter-leaveApplicationTable" />
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

        {{-- DATA-TABLE COMPONENT --}}
        <x-data-table id="leaveApplicationTable" title="Leave Applications" icon="fa-solid fa-umbrella-beach"
            buttonId="btnAddLeaveApplication" buttonText="New Leave Application"
            :columns="['SL', 'App No', 'Employee', 'Leave Type', 'From', 'To', 'Days', 'Status', 'Applied At', 'Action']"
            :ajaxUrl="route('leave-applications.dataTable')"
            :dtColumns="[
                ['data' => 'DT_RowIndex', 'name' => 'id', 'width' => '40px'],
                ['data' => 'application_no', 'width' => '120px'],
                ['data' => 'employee', 'width' => '180px'],
                ['data' => 'leave_type', 'width' => '120px'],
                ['data' => 'from_date', 'width' => '100px'],
                ['data' => 'to_date', 'width' => '100px'],
                ['data' => 'total_days', 'width' => '70px', 'className' => 'text-center'],
                ['data' => 'status', 'width' => '100px'],
                ['data' => 'applied_at', 'width' => '100px'],
                ['data' => 'action', 'orderable' => false, 'searchable' => false, 'width' => '80px'],
            ]"
            :filters="[
                'employee_id' => '#filter_employee',
                'leave_type_id' => '#filter_leave_type',
                'status' => '#filter_status',
                'from_date' => '#filter_from_date',
                'to_date' => '#filter_to_date',
            ]"
            :exportButtons="true" />
    </div>

    @push('scripts')
        <script>
            // Add New - redirect to create page
            $(document).ready(function() {
                $(document).on('click', '#btnAddLeaveApplication', function(e) {
                    e.preventDefault();
                    window.location.href = "{{ route('leave-applications.create') }}";
                });
            });

            // Edit - redirect to edit page
            function leaveApplicationEdit(id) {
                window.location.href = "{{ route('leave-applications.edit', ':id') }}".replace(':id', id);
            }

            // Approve - check balance first, then confirm
            function approveLeave(id) {
                // First check balance
                let checkUrl = "/leave-applications/" + id + "/check-balance";
                $.get(checkUrl, function(checkRes) {
                    let warningMsg = checkRes.warning;
                    
                    if (warningMsg) {
                        // Show warning with confirm/cancel
                        Swal.fire({
                            title: '⚠️ Balance Issue Detected',
                            html: warningMsg,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#16a34a',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes, Approve Anyway',
                            cancelButtonText: 'Cancel'
                        }).then((r) => {
                            if (r.isConfirmed) {
                                doApprove(id);
                            }
                        });
                    } else {
                        // No warning, normal confirmation
                        Swal.fire({
                            title: 'Approve Leave?',
                            text: "Are you sure you want to approve this leave application?",
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#16a34a',
                            cancelButtonColor: '#4b5563',
                            confirmButtonText: 'Yes, Approve'
                        }).then((r) => {
                            if (r.isConfirmed) {
                                doApprove(id);
                            }
                        });
                    }
                }).fail(function() {
                    // If check fails, fallback to normal approve
                    Swal.fire({
                        title: 'Approve Leave?',
                        text: "Are you sure you want to approve this leave application?",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#16a34a',
                        cancelButtonColor: '#4b5563',
                        confirmButtonText: 'Yes, Approve'
                    }).then((r) => {
                        if (r.isConfirmed) {
                            doApprove(id);
                        }
                    });
                });
            }

            // Do the actual approval
            function doApprove(id) {
                let url = "/leave-applications/" + id + "/approve";
                $.post(url, function(res) {
                    if (res.status === 'success') {
                        Toastify({
                            text: res.message,
                            duration: 3000,
                            gravity: "bottom",
                            position: "right",
                            style: { background: "linear-gradient(135deg, #16a34a, #4ade80)" },
                        }).showToast();
                        $('#leaveApplicationTable').DataTable().ajax.reload(null, false);
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                }).fail(function() {
                    Swal.fire('Error', 'Server error.', 'error');
                });
            }

            // Disapprove - called from button when already approved
            function disapproveLeave(id) {
                Swal.fire({
                    title: 'Disapprove Leave?',
                    text: "This will revert the application back to Pending status.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ea580c',
                    cancelButtonColor: '#4b5563',
                    confirmButtonText: 'Yes, Disapprove'
                }).then((r) => {
                    if (r.isConfirmed) {
                        let url = "/leave-applications/" + id + "/disapprove";
                        $.post(url, function(res) {
                            if (res.status === 'success') {
                                Toastify({
                                    text: res.message,
                                    duration: 3000,
                                    gravity: "bottom",
                                    position: "right",
                                    style: { background: "linear-gradient(135deg, #ea580c, #f97316)" },
                                }).showToast();
                                $('#leaveApplicationTable').DataTable().ajax.reload(null, false);
                            } else {
                                Swal.fire('Error', res.message, 'error');
                            }
                        }).fail(function() {
                            Swal.fire('Error', 'Server error.', 'error');
                        });
                    }
                });
            }

            // Delete
            function leaveApplicationDelete(id) {
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
                        let deleteUrl = "{{ route('leave-applications.destroy', ':id') }}".replace(':id', id);

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
                                $('#leaveApplicationTable').DataTable().ajax.reload(null, false);
                            } else {
                                Swal.fire('Error', res.message || 'Deletion failed.', 'error');
                            }
                        }).fail(function() {
                            Swal.fire('Error', 'Failed to communicate with server.', 'error');
                        });
                    }
                });
            }

            // Reset Filters
            $('#resetFilters').on('click', function() {
                $('#filter_employee').val('');
                $('#filter_leave_type').val('');
                $('#filter_status').val('');
                $('#filter_from_date').val('');
                $('#filter_to_date').val('');
                $('.dt-filter-leaveApplicationTable').trigger('change');
            });

            // Reject action
            function leaveApplicationReject(id) {
                Swal.fire({
                    title: 'Reject Leave?',
                    input: 'textarea',
                    inputLabel: 'Rejection Reason',
                    inputPlaceholder: 'Enter reason for rejection...',
                    inputAttributes: { 'aria-label': 'Rejection reason' },
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#4b5563',
                    confirmButtonText: 'Reject',
                    preConfirm: (reason) => {
                        let rejectUrl = "{{ route('leave-applications.reject', ':id') }}".replace(':id', id);
                        return $.post(rejectUrl, { rejection_reason: reason }).then(res => {
                            if (res.status === 'success' || res.status === true) {
                                Toastify({
                                    text: res.message,
                                    duration: 3000,
                                    gravity: "bottom",
                                    position: "right",
                                    style: { background: "linear-gradient(135deg, #dc2626, #f87171)" },
                                }).showToast();
                                $('#leaveApplicationTable').DataTable().ajax.reload(null, false);
                            } else {
                                Swal.fire('Error', res.message, 'error');
                            }
                        }).fail(() => {
                            Swal.fire('Error', 'Server error.', 'error');
                        });
                    }
                });
            }
        </script>
    @endpush
</x-app-layout>