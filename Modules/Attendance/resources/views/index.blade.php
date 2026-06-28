<x-app-layout>

    <div class="p-4">
        <div
            class="flex flex-col md:flex-row md:items-end gap-4 mb-5 bg-white p-4 rounded-xl border border-gray-200 shadow-sm">

            {{-- Employee Filter --}}
            <div class="flex flex-col w-full md:w-1/4">
                <x-form-select label="Employee" id="filter_employee" class="dt-filter-attendanceTable">
                    <option value="">All Employees</option>
                    @foreach ($employees as $employee)
                        <option value="{{ $employee->id }}">{{ $employee->employee_code }} - {{ $employee->full_name }}</option>
                    @endforeach
                </x-form-select>
            </div>

            {{-- Attendance Status Filter --}}
            <div class="flex flex-col w-full md:w-1/4">
                <x-form-select label="Status" id="filter_status" class="dt-filter-attendanceTable">
                    <option value="">All Status</option>
                    <option value="Present">Present</option>
                    <option value="Absent">Absent</option>
                    <option value="Half Day">Half Day</option>
                    <option value="On Leave">On Leave</option>
                    <option value="Holiday">Holiday</option>
                    <option value="Weekend">Weekend</option>
                </x-form-select>
            </div>

            {{-- Late/Early Filter --}}
            <div class="flex flex-col w-full md:w-1/4">
                <x-form-select label="Late/Early" id="filter_late_early" class="dt-filter-attendanceTable">
                    <option value="">All</option>
                    <option value="late">Late</option>
                    <option value="early">Early Out</option>
                    <option value="on_time">On Time</option>
                </x-form-select>
            </div>

            {{-- Date From --}}
            <div class="flex flex-col w-full md:w-1/4">
                <x-form-input type="date" label="Date From" name="filter_date_from" id="filter_date_from" class="dt-filter-attendanceTable" />
            </div>

            {{-- Date To --}}
            <div class="flex flex-col w-full md:w-1/4">
                <x-form-input type="date" label="Date To" name="filter_date_to" id="filter_date_to" class="dt-filter-attendanceTable" />
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
        <x-data-table id="attendanceTable" title="Attendance Management" icon="fa-solid fa-calendar-check"
            buttonId="btnAddAttandance" buttonText="Add Attendance" :columns="[ 'SL', 'Employee', 'Date', 'Check In/Out', 'Total Hours', 'Late/Early', 'Overtime', 'Status', 'Approval', 'Action']"
            :ajaxUrl="route('attendance.dataTable')"
            :dtColumns="[
                ['data' => 'DT_RowIndex', 'name' => 'id', 'width' => '5px'],
                ['data' => 'employee'],
                ['data' => 'attendance_date'],
                ['data' => 'attendance_time', 'width' => '100px'],
                ['data' => 'working_hours', 'width' => '80px'],
                ['data' => 'late_early', 'width' => '80px'],
                ['data' => 'overtime', 'width' => '80px'],
                ['data' => 'attendance_status'],
                ['data' => 'approval_status'],
                ['data' => 'action', 'orderable' => false, 'searchable' => false],
            ]" :filters="[
                'employee_id' => '#filter_employee',
                'attendance_status' => '#filter_status',
                'late_early' => '#filter_late_early',
                'attendance_date_from' => '#filter_date_from',
                'attendance_date_to' => '#filter_date_to',
            ]" :exportButtons="true" />
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Set default date filters: Date From = 1st of current month, Date To = today
                var today = new Date();
                var firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
                
                var formatDate = function(date) {
                    var y = date.getFullYear();
                    var m = String(date.getMonth() + 1).padStart(2, '0');
                    var d = String(date.getDate()).padStart(2, '0');
                    return y + '-' + m + '-' + d;
                };

                if (!$('#filter_date_from').val()) {
                    $('#filter_date_from').val(formatDate(firstDay));
                }
                if (!$('#filter_date_to').val()) {
                    $('#filter_date_to').val(formatDate(today));
                }

                // Trigger reload so DataTable picks up the default filter values
                setTimeout(function() {
                    $('.dt-filter-attendanceTable').trigger('change');
                }, 100);

                // Open add new attendance - redirect to step 1 of the creation wizard
                $(document).on('click', '#btnAddAttandance', function(e) {
                    e.preventDefault();
                    window.location.href = "{{ route('attendance.create') }}";
                });
            });
            $('#resetFilters').on('click', function() {
                $('#filter_employee').val('');
                $('#filter_status').val('');
                $('#filter_late_early').val('');
                $('#filter_date_from').val('');
                $('#filter_date_to').val('');

                $('.dt-filter-attendanceTable').trigger('change');
            });

            function attendanceApprove(id) {
                const csrfToken = '{{ csrf_token() }}';
                Swal.fire({
                    title: 'Approve Attendance?',
                    text: "This will mark the attendance as approved.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#16a34a',
                    cancelButtonColor: '#4b5563',
                    confirmButtonText: 'Yes, approve it!'
                }).then((r) => {
                    if (r.isConfirmed) {
                        $.ajax({
                            url: "{{ url('attendances') }}/" + id + "/approve",
                            type: 'POST',
                            data: { _token: csrfToken },
                            success: function(res) {
                                if (res.status === 'success') {
                                    Toastify({
                                        text: res.message || 'Approved successfully',
                                        duration: 3000,
                                        gravity: "bottom",
                                        position: "right",
                                        style: { background: "linear-gradient(135deg, #16a34a, #22c55e)" },
                                    }).showToast();
                                    $('#attendanceTable').DataTable().ajax.reload(null, false);
                                } else {
                                    Swal.fire('Error', res.message || 'Approval failed.', 'error');
                                }
                            },
                            error: function(xhr) {
                                Swal.fire('Error', xhr.responseJSON?.message || 'Server error occurred', 'error');
                            }
                        });
                    }
                });
            }

            function attendanceDisapprove(id) {
                const csrfToken = '{{ csrf_token() }}';
                Swal.fire({
                    title: 'Revert Approval?',
                    text: "This will set the attendance back to Pending.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#f59e0b',
                    cancelButtonColor: '#4b5563',
                    confirmButtonText: 'Yes, revert to Pending'
                }).then((r) => {
                    if (r.isConfirmed) {
                        $.ajax({
                            url: "{{ url('attendances') }}/" + id + "/disapprove",
                            type: 'POST',
                            data: { _token: csrfToken },
                            success: function(res) {
                                if (res.status === 'success') {
                                    Toastify({
                                        text: res.message || 'Reverted to Pending',
                                        duration: 3000,
                                        gravity: "bottom",
                                        position: "right",
                                        style: { background: "linear-gradient(135deg, #f59e0b, #d97706)" },
                                    }).showToast();
                                    $('#attendanceTable').DataTable().ajax.reload(null, false);
                                } else {
                                    Swal.fire('Error', res.message || 'Revert failed.', 'error');
                                }
                            },
                            error: function(xhr) {
                                Swal.fire('Error', xhr.responseJSON?.message || 'Server error occurred', 'error');
                            }
                        });
                    }
                });
            }

            function attendanceDelete(id) {
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
                        $.ajax({
                            url: "{{ url('attendances') }}/" + id,
                            type: 'POST',
                            data: { _method: 'DELETE', _token: '{{ csrf_token() }}' },
                            success: function(res) {
                                if (res.status === 'success') {
                                    Toastify({
                                        text: res.message || 'Deleted successfully',
                                        duration: 3000,
                                        gravity: "bottom",
                                        position: "right",
                                        style: {
                                            background: "linear-gradient(135deg, #dc2626, #f87171)"
                                        },
                                    }).showToast();
                                    $('#attendanceTable').DataTable().ajax.reload(null, false);
                                } else {
                                    Swal.fire('Error', res.message || 'Deletion failed.', 'error');
                                }
                            },
                            error: function(xhr) {
                                let errorMsg = 'Server error occurred';
                                if (xhr.responseJSON?.message) {
                                    errorMsg = xhr.responseJSON.message;
                                }
                                Swal.fire('Error', errorMsg, 'error');
                            }
                        });
                    }
                });
            }

            function attendanceEdit(id) {
                // Redirect to edit page or open modal
                window.location.href = "{{ url('attendances') }}/" + id + "/edit";
            }
        </script>
    @endpush
</x-app-layout>