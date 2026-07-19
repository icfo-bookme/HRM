<x-app-layout>

    <div class="p-4">
        {{-- FILTERS --}}
        <div
            class="flex flex-col md:flex-row md:items-end gap-4 mb-5 bg-white p-4 rounded-xl border border-gray-200 shadow-sm">

            {{-- Leave Type Filter --}}
            <div class="flex flex-col w-full md:w-1/4">
                <x-form-select label="Leave Type" id="filter_leave_type" class="dt-filter-leaveApplicationMyTable">
                    <option value="">All Leave Types</option>
                    @php $leaveTypes = \Modules\Leave\Models\LeaveType::where('is_active', true)->orderBy('name')->get(['id', 'name']); @endphp
                    @foreach ($leaveTypes as $leaveType)
                        <option value="{{ $leaveType->id }}">{{ $leaveType->name }}</option>
                    @endforeach
                </x-form-select>
            </div>

            {{-- Status Filter --}}
            <div class="flex flex-col w-full md:w-1/4">
                <x-form-select label="Status" id="filter_status" class="dt-filter-leaveApplicationMyTable">
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
            <div class="flex flex-col w-full md:w-1/4">
                <x-form-input type="date" label="From Date" name="filter_from_date" id="filter_from_date" class="dt-filter-leaveApplicationMyTable" />
            </div>

            {{-- Date To --}}
            <div class="flex flex-col w-full md:w-1/4">
                <x-form-input type="date" label="To Date" name="filter_to_date" id="filter_to_date" class="dt-filter-leaveApplicationMyTable" />
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
        <x-data-table id="leaveApplicationMyTable" title="My Leave Applications" icon="fa-solid fa-user-check"
            buttonId="btnApplyLeave" buttonText="Apply for Leave"
            :columns="['SL', 'App No', 'Leave Type', 'From', 'To', 'Days', 'Status', 'Applied At', 'Action']"
            :ajaxUrl="route('leave-applications.myDataTable')"
            :dtColumns="[
                ['data' => 'DT_RowIndex', 'name' => 'id', 'width' => '40px'],
                ['data' => 'application_no', 'width' => '120px'],
                ['data' => 'leave_type', 'width' => '130px'],
                ['data' => 'from_date', 'width' => '100px'],
                ['data' => 'to_date', 'width' => '100px'],
                ['data' => 'total_days', 'width' => '70px', 'className' => 'text-center'],
                ['data' => 'status', 'width' => '100px'],
                ['data' => 'applied_at', 'width' => '100px'],
                ['data' => 'action', 'orderable' => false, 'searchable' => false, 'width' => '80px'],
            ]"
            :filters="[
                'leave_type_id' => '#filter_leave_type',
                'status' => '#filter_status',
                'from_date' => '#filter_from_date',
                'to_date' => '#filter_to_date',
            ]"
            :exportButtons="true" />
    </div>

    @push('scripts')
        <script>
            // Apply Leave button
            $(document).ready(function() {
                $(document).on('click', '#btnApplyLeave', function(e) {
                    e.preventDefault();
                    window.location.href = "{{ route('leave-applications.create') }}";
                });
            });

            // Edit
            function leaveApplicationEdit(id) {
                window.location.href = "{{ route('leave-applications.edit', ':id') }}".replace(':id', id);
            }

            // Delete (only draft/cancelled)
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
                        $.post(deleteUrl, { _method: 'DELETE' }, function(res) {
                            if (res.status === 'success' || res.status === true) {
                                Toastify({
                                    text: res.message || 'Deleted successfully',
                                    duration: 3000,
                                    gravity: "bottom",
                                    position: "right",
                                    style: { background: "linear-gradient(135deg, #dc2626, #f87171)" },
                                }).showToast();
                                $('#leaveApplicationMyTable').DataTable().ajax.reload(null, false);
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
                $('#filter_leave_type').val('');
                $('#filter_status').val('');
                $('#filter_from_date').val('');
                $('#filter_to_date').val('');
                $('.dt-filter-leaveApplicationMyTable').trigger('change');
            });
        </script>
    @endpush
</x-app-layout>