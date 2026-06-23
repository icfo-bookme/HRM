<x-app-layout>

    <div class="p-4">
        {{-- FILTER SECTION --}}
        <div class="bg-white min-w-full shadow-md rounded-xl border border-gray-200 overflow-hidden mb-4">
            <div class="flex flex-wrap items-center gap-4 px-6 py-4">
                <x-table-filter id="filter_department" label="Department"
                    :options="$departments" tableId="employeeTable" />

                <x-table-filter id="filter_designation" label="Designation"
                    :options="$designations" tableId="employeeTable" />

                <x-table-filter id="filter_status" label="Status"
                    :options="['Active' => 'Active', 'Inactive' => 'Inactive', 'On Leave' => 'On Leave', 'Suspended' => 'Suspended', 'Terminated' => 'Terminated', 'Resigned' => 'Resigned', 'Retired' => 'Retired']"
                    tableId="employeeTable" />

                <button onclick="resetFilters()"
                    class="px-4 py-2 text-xs font-semibold uppercase tracking-wider text-gray-500 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                    <i class="fas fa-undo mr-1"></i> Reset
                </button>
            </div>
        </div>

        {{-- DATA-TABLE COMPONENT --}}
        <x-data-table id="employeeTable" title="Employee Management" icon="fa-solid fa-users"
            buttonId="btnAddEmployee" buttonText="Add New Employee" :columns="[ 'SL', 'Employee Code', 'Employee Name', 'Department', 'Designation', 'Email', 'Status', 'Action']"
            :ajaxUrl="route('employee.dataTable')"
            :dtColumns="[
                ['data' => 'DT_RowIndex', 'name' => 'id', 'width' => '50px'],
                ['data' => 'employee_code'],
                ['data' => 'employee'],
                ['data' => 'department'],
                ['data' => 'designation'],
                ['data' => 'email'],
                ['data' => 'status'],
                ['data' => 'action', 'orderable' => false, 'searchable' => false],
            ]" :filters="[
                'department_id' => '#filter_department',
                'designation_id' => '#filter_designation',
                'status' => '#filter_status',
            ]" :exportButtons="true" />
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Open add new employee - redirect to step 1 of the creation wizard
                $(document).on('click', '#btnAddEmployee', function(e) {
                    e.preventDefault();
                    window.location.href = "{{ route('employee.create.step1') }}";
                });
            });

            function resetFilters() {
                $('#filter_department, #filter_designation, #filter_status').val('').trigger('change');
            }

            function employeeDelete(id) {
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
                        let deleteUrl = "{{ route('employee.destroy', ':id') }}".replace(':id', id);

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
                                    $('#employeeTable').DataTable().ajax.reload(null, false);
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
        </script>
    @endpush
</x-app-layout>