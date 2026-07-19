<x-app-layout>

    <div class="p-4">
        {{-- Filters --}}
        <div
            class="flex flex-col md:flex-row md:items-end gap-4 mb-5 bg-white p-4 rounded-xl border border-gray-200 shadow-sm">

            {{-- Status Filter --}}
            <div class="flex flex-col w-full md:w-1/3">
                <x-form-select label="Status" id="filter_status" class="dt-filter-leaveTypeTable">
                    <option value="">All Status</option>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </x-form-select>
            </div>

            {{-- Gender Filter --}}
            <div class="flex flex-col w-full md:w-1/3">
                <x-form-select label="Gender" id="filter_gender" class="dt-filter-leaveTypeTable">
                    <option value="">All Genders</option>
                    <option value="All">All</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
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

        {{-- DATA-TABLE COMPONENT --}}
        <x-data-table id="leaveTypeTable" title="Leave Types Management" icon="fa-solid fa-calendar-alt"
            buttonId="btnAddLeaveType" buttonText="Add Leave Type" :columns="[ 'SL', 'Name', 'Days/Year', 'Gender', 'Settings', 'Action']" :ajaxUrl="route('leave-types.dataTable')"
            :dtColumns="[
                ['data' => 'DT_RowIndex', 'name' => 'id', 'width' => '50px'],
                ['data' => 'name'],
                ['data' => 'days_per_year'],
                ['data' => 'applicable_gender'],
                ['data' => 'is_paid'],
                ['data' => 'action', 'orderable' => false, 'searchable' => false],
            ]" :filters="[
                'is_active' => '#filter_status',
                'applicable_gender' => '#filter_gender',
            ]" :exportButtons="true" />
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Open add new leave type
                $(document).on('click', '#btnAddLeaveType', function(e) {
                    e.preventDefault();
                    window.location.href = "{{ route('leave-types.create') }}";
                });
            });

            function leaveTypeDelete(id) {
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
                        let deleteUrl = "{{ route('leave-types.destroy', ':id') }}".replace(':id', id);

                        $.ajax({
                            url: deleteUrl,
                            type: 'POST',
                            data: { _method: 'DELETE' },
                            success: function(res) {
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
                                    $('#leaveTypeTable').DataTable().ajax.reload(null, false);
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

            $('#resetFilters').on('click', function() {
                $('#filter_status').val('');
                $('#filter_gender').val('');
                $('.dt-filter-leaveTypeTable').trigger('change');
            });
        </script>
    @endpush
</x-app-layout>