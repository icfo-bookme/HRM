<x-app-layout>

    <div class="p-4">
        <div
            class="flex flex-col md:flex-row md:items-end gap-4 mb-5 bg-white p-4 rounded-xl border border-gray-200 shadow-sm">

            {{-- Company Filter --}}
            {{-- <div class="flex flex-col w-full md:w-1/3">
                <x-form-select label="Company" id="filter_company" class="dt-filter-departmentTable">
                    <option value="">All Company</option>
                    @foreach ($companies as $company)
                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                    @endforeach
                </x-form-select>
            </div> --}}

            {{-- Status Filter --}}
            <div class="flex flex-col w-full md:w-1/3">
                <x-form-select label="Status" id="filter_status" class="dt-filter-departmentTable">
                    <option value="">All Status</option>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </x-form-select>
            </div>

            {{-- Optional: Reset Button () --}}
            <div class="w-full md:w-auto flex items-end">
                <button id="resetFilters"
                    class="px-4 py-2 text-sm font-medium text-white bg-gray-700 hover:bg-gray-800
                   rounded-lg transition active:scale-95">
                    Reset
                </button>
            </div>

        </div>
        {{-- REUSABLE DATA-TABLE COMPONENT --}}
        <x-data-table id="departmentTable" title="Department Management" icon="fa-solid fa-building"
            buttonId="btnAddDepartment" buttonText="Add New Department" :columns="[ 'Code', 'Department Name', 'Email', 'Phone', 'Status', 'Created At', 'Action']" :ajaxUrl="route('departments.dataTable')"
            :dtColumns="[
                {{-- ['data' => 'company.name', 'width' => '80px'], --}}
                ['data' => 'code'],
                ['data' => 'name'],
                ['data' => 'email','width' => '80px'],
                ['data' => 'phone'],
                ['data' => 'is_active'],
                ['data' => 'created_at'],
                ['data' => 'action', 'orderable' => false, 'searchable' => false],
            ]" :filters="[
                'company_id' => '#filter_company',
                'is_active' => '#filter_status',
            ]" :exportButtons="true" />
    </div>

    {{-- DRAWER COMPONENT --}}
    <x-drawer id="department-drawer" overlayId="department-overlay" title="Add New Department"
        submitOnClick="saveForm()">
        <form id="departmentForm">
            <input type="hidden" name="id" id="department_id">

            {{-- Company Select --}}
            {{-- <div class="mb-4 animate-fade" style="animation-delay: 50ms;">
                <x-form-select label="Company" name="company_id" id="company_id" placeholder="Select Company">
                    @foreach ($companies as $company)
                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                    @endforeach
                </x-form-select>
            </div> --}}

            {{-- Branch Select --}}
            <div class="mb-4 animate-fade" style="animation-delay: 100ms;">
                <x-form-select label="Branch" name="branch_id" id="branch_id" placeholder="Select Branch (Optional)">
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </x-form-select>
            </div>

            {{-- Code & Name --}}
            <div class="grid grid-cols-2 gap-3 mb-4 animate-fade" style="animation-delay: 150ms;">
                <x-form-input label="Code" name="code" id="code" placeholder="Code" />
                <x-form-input label="Name" name="name" id="name" placeholder="Department Name" required />
            </div>

            {{-- Email & Phone --}}
            <div class="grid grid-cols-2 gap-3 mb-4 animate-fade" style="animation-delay: 200ms;">
                <x-form-input label="Email" name="email" id="email" type="email" placeholder="Email" />
                <x-form-input label="Phone" name="phone" id="phone" placeholder="Phone" />
            </div>

            {{-- Parent Department --}}
            <div class="mb-4 animate-fade" style="animation-delay: 250ms;">
                <x-form-select label="Parent Department" name="parent_id" id="parent_id"
                    placeholder="Select Parent Department (Optional)">
                    <option value="">-- None --</option>
                    @foreach ($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </x-form-select>
            </div>

            {{-- Description Textarea --}}
            <div class="mb-4 animate-fade" style="animation-delay: 300ms;">
                <x-form-textarea label="Description" name="description" id="description"
                    placeholder="Department Description" rows="3" />
            </div>

            {{-- Sort Order --}}
            <div class="grid grid-cols-2 gap-3 mb-4 animate-fade" style="animation-delay: 350ms;">
                <x-form-input label="Sort Order" name="sort_order" id="sort_order" type="number" placeholder="0"
                    value="0" />
            </div>

            {{-- Active Checkbox --}}
            <div class="space-y-2 bg-slate-50 p-3 rounded-md border border-slate-100 animate-fade"
                style="animation-delay: 400ms;">
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox" id="is_active" name="is_active" value="1" checked
                        class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500">
                    <span class="text-sm font-semibold text-slate-700">Active</span>
                </label>
            </div>
        </form>
    </x-drawer>

    @push('scripts')
        <script>
            let isSaving = false;

            function openDepartmentDrawer(mode = 'add') {
                if (mode === 'edit') {
                    $('#drawerTitle').text('Update Department');
                    $('#drawerButtonText').text('Update Department');
                } else {
                    resetForm();
                    $('#drawerTitle').text('Add New Department');
                    $('#drawerButtonText').text('Save Department');
                }
                openGlobalDrawer('department-drawer', 'department-overlay');
            }

            function resetForm() {
                $('#departmentForm')[0].reset();
                $('#department_id').val('');
                $('#is_active').prop('checked', true);
                $('#sort_order').val(0);
            }

            function departmentEdit(id) {
                Swal.fire({
                    title: 'Loading...',
                    text: `Fetching department details`,
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                resetForm();
                let fetchUrl = "{{ route('departments.show', ':id') }}".replace(':id', id);

                $.get(fetchUrl, function(res) {
                    Swal.close();
                    if (res.status) {
                        let d = res.department;
                        $('#department_id').val(d.id);
                        $('select[name="company_id"]').val(d.company_id);
                        $('select[name="branch_id"]').val(d.branch_id || '');
                        $('input[name="code"]').val(d.code);
                        $('input[name="name"]').val(d.name);
                        $('input[name="email"]').val(d.email);
                        $('input[name="phone"]').val(d.phone);
                        $('select[name="parent_id"]').val(d.parent_id || '');
                        $('textarea[name="description"]').val(d.description);
                        $('input[name="sort_order"]').val(d.sort_order || 0);
                     
                        $('#is_active').prop('checked', d.is_active == 1);

                        openDepartmentDrawer('edit');
                    } else {
                        Swal.fire('Error', res.message || 'Failed to fetch data.', 'error');
                    }
                }).fail(function() {
                    Swal.close();
                    Swal.fire('Error', 'Server communication error.', 'error');
                });
            }

            function saveForm() {
                if (isSaving) return;

                let id = $('#department_id').val();
                let url = id ? "{{ route('departments.update', ':id') }}".replace(':id', id) :
                    "{{ route('departments.store') }}";

                let formData = $('#departmentForm').serialize();
                if (id) formData += '&_method=PUT';
               

                isSaving = true;
                $('#drawerButtonText').text('Saving...');
                $('#saveBtn').prop('disabled', true).addClass('opacity-70 cursor-not-allowed');

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    success: function(res) {
                        isSaving = false;
                        $('#saveBtn').prop('disabled', false).removeClass('opacity-70 cursor-not-allowed');

                        if (res.status === 'success' || res.status === true) {
                            Toastify({
                                text: res.message || 'Saved successfully',
                                duration: 3000,
                                gravity: "bottom",
                                position: "right",
                                style: {
                                    background: "linear-gradient(135deg, #16a34a, #4ade80)"
                                },
                            }).showToast();
                            closeGlobalDrawer('department-drawer', 'department-overlay');
                            $('#departmentTable').DataTable().ajax.reload(null, false);
                        } else {
                            Swal.fire('Error', res.message || 'Something went wrong', 'error');
                            $('#drawerButtonText').text(id ? 'Update Department' : 'Save Department');
                        }
                    },
                    error: function(xhr) {
                        isSaving = false;
                        $('#saveBtn').prop('disabled', false).removeClass('opacity-70 cursor-not-allowed');
                        $('#drawerButtonText').text(id ? 'Update Department' : 'Save Department');

                        let errorMsg = 'Server error occurred';
                        if (xhr.responseJSON?.errors) {
                            errorMsg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                        } else if (xhr.responseJSON?.message) {
                            errorMsg = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            html: errorMsg
                        });
                    }
                });
            }

            function departmentDelete(id) {
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
                        let deleteUrl = "{{ route('departments.destroy', ':id') }}".replace(':id', id);

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
                                $('#departmentTable').DataTable().ajax.reload(null, false);
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
                $('#filter_company').val('');
                $('#filter_status').val('');

                $('.dt-filter-departmentTable').trigger('change');
            });

            $(document).ready(function() {
                $(document).on('click', '#btnAddDepartment', function(e) {
                    e.preventDefault();
                    openDepartmentDrawer('add');
                });
            });
        </script>
    @endpush
</x-app-layout>
