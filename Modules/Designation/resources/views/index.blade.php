<x-app-layout>
    <div class="p-4">
        <x-data-table id="designationTable" title="Designation Management" icon="fa-solid fa-briefcase"
            buttonId="btnAddDesignation" buttonText="Add New Designation" :columns="['Department', 'Grade', 'Title', 'Level', 'Status', 'Created At', 'Action']" :ajaxUrl="route('designation.dataTable')"
            :dtColumns="[
                ['data' => 'department.name'],
                ['data' => 'grade_id'],
               
                ['data' => 'title'],
                ['data' => 'level'],
                ['data' => 'is_active'],
                ['data' => 'created_at'],
                ['data' => 'action', 'orderable' => false, 'searchable' => false],
            ]" />
    </div>

    <x-drawer id="designation-drawer" overlayId="designation-overlay" title="Add New Designation"
        submitOnClick="saveForm()">
        <form id="designationForm">
            <input type="hidden" id="designation_id" name="id">

            <div class="mb-4 animate-fade" style="animation-delay: 100ms;">
                <x-form-select label="Department" name="department_id" placeholder="--Select Department--" required>
                    @foreach ($departments as $department)
                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                    @endforeach
                </x-form-select>
            </div>

            <div class="mb-4 animate-fade" style="animation-delay: 150ms;">
                <x-form-select label="Salary Grade" name="grade_id" placeholder="--Select Salary Grade--" required>
                    @foreach ($grades as $grade)
                        <option value="{{ $grade->id }}">{{ $grade->name }} ({{ $grade->code }})</option>
                    @endforeach
                </x-form-select>
            </div>

            <div class="grid grid-cols-2 gap-3 mb-4 animate-fade" style="animation-delay: 200ms;">
                <x-form-input label="Code" name="code" placeholder="Designation code" />
                <x-form-input label="Title" name="title" placeholder="Designation title" required />
            </div>

            <div class="grid grid-cols-2 gap-3 mb-4 animate-fade" style="animation-delay: 250ms;">
                <x-form-input label="Level" name="level" type="number" placeholder="1" />
                <div></div>
            </div>

            <div class="animate-fade" style="animation-delay: 300ms;">
                <x-form-tag-input label="Responsibilities" name="responsibilities" placeholder="Type responsibility and press Enter..." />
            </div>

            <div class="animate-fade" style="animation-delay: 350ms;">
                <x-form-tag-input label="Requirements" name="requirements" placeholder="Type requirement and press Enter..." />
            </div>

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

            function openDesignationDrawer(mode = 'add') {
                if (mode === 'edit') {
                    $('#drawerTitle').text('Update Designation');
                    $('#drawerButtonText').text('Update Designation');
                } else {
                    resetForm();
                    $('#drawerTitle').text('Add New Designation');
                    $('#drawerButtonText').text('Save Designation');
                }
                openGlobalDrawer('designation-drawer', 'designation-overlay');
            }

            function resetForm() {
                $('#designationForm')[0].reset();
                $('#designation_id').val('');
                $('#is_active').prop('checked', true);
                
                // Clear state values & run component updater
                $('#responsibilities').val('[]');
                $('#requirements').val('[]');
                window.renderTagComponent('responsibilities');
                window.renderTagComponent('requirements');
            }

            function designationEdit(id) {
                Swal.fire({
                    title: 'Loading...',
                    text: 'Fetching designation details',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                resetForm();
                let fetchUrl = "{{ route('designation.show', ':id') }}".replace(':id', id);

                $.get(fetchUrl, function(res) {
                    if (res.status === 'success') {
                        let d = res.designation;
                        $('#designation_id').val(d.id);
                        $('#company_id').val(d.company_id);
                        $('#department_id').val(d.department_id);
                        $('#grade_id').val(d.grade_id);
                        $('#code').val(d.code);
                        $('#title').val(d.title);
                        $('#level').val(d.level);
                        
                        // Parse JSON payloads to strings safely
                        let respVal = typeof d.responsibilities === 'string' ? d.responsibilities : JSON.stringify(d.responsibilities || []);
                        let reqVal = typeof d.requirements === 'string' ? d.requirements : JSON.stringify(d.requirements || []);
                        
                        $('#responsibilities').val(respVal);
                        $('#requirements').val(reqVal);
                        
                        // Sync UI component states
                        window.renderTagComponent('responsibilities');
                        window.renderTagComponent('requirements');

                        $('#is_active').prop('checked', d.is_active == 1);

                        Swal.close();
                        openDesignationDrawer('edit');
                    } else {
                        Swal.fire('Error', res.message || 'Failed to fetch data.', 'error');
                    }
                }).fail(function() {
                    Swal.fire('Error', 'Server communication error.', 'error');
                });
            }

            function saveForm() {
                if (isSaving) return;

                let id = $('#designation_id').val();
                let url = id ? "{{ route('designation.update', ':id') }}".replace(':id', id) : "{{ route('designation.store') }}";
                let formData = $('#designationForm').serialize();

                if (id) formData += '&_method=PUT';

                isSaving = true;
                $('#drawerButtonText').text('Saving...');
                $('#saveBtn').prop('disabled', true).addClass('opacity-70 cursor-not-allowed');

                Swal.fire({
                    title: 'Confirm',
                    text: id ? 'Update designation?' : 'Create designation?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#16a34a',
                    cancelButtonColor: '#d33'
                }).then((result) => {
                    if (!result.isConfirmed) {
                        isSaving = false;
                        $('#drawerButtonText').text(id ? 'Update Designation' : 'Save Designation');
                        $('#saveBtn').prop('disabled', false).removeClass('opacity-70 cursor-not-allowed');
                        return;
                    }

                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: formData,
                        success: function(res) {
                            isSaving = false;
                            $('#saveBtn').prop('disabled', false).removeClass('opacity-70 cursor-not-allowed');

                            if (res.status === 'success') {
                                Toastify({
                                    text: res.message,
                                    duration: 3000,
                                    gravity: 'bottom',
                                    position: 'right',
                                    backgroundColor: 'linear-gradient(135deg, #16a34a, #4ade80)',
                                }).showToast();
                                closeGlobalDrawer('designation-drawer', 'designation-overlay');
                                $('#designationTable').DataTable().ajax.reload(null, false);
                            } else {
                                Swal.fire('Error', res.message, 'error');
                                $('#drawerButtonText').text(id ? 'Update Designation' : 'Save Designation');
                            }
                        },
                        error: function(xhr) {
                            isSaving = false;
                            $('#saveBtn').prop('disabled', false).removeClass('opacity-70 cursor-not-allowed');
                            $('#drawerButtonText').text(id ? 'Update Designation' : 'Save Designation');
                            let errorMsg = 'Server error occurred';
                            if (xhr.responseJSON?.message) {
                                errorMsg = xhr.responseJSON.message;
                            } else if (xhr.responseJSON?.errors) {
                                errorMsg = Object.values(xhr.responseJSON.errors).flat().join(', ');
                            }
                            Swal.fire('Error', errorMsg, 'error');
                        }
                    });
                });
            }

            function designationDelete(id) {
                Swal.fire({
                    title: 'Delete?',
                    text: 'This cannot be undone',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#4b5563'
                }).then((r) => {
                    if (r.isConfirmed) {
                        let deleteUrl = "{{ route('designation.destroy', ':id') }}".replace(':id', id);
                        $.post(deleteUrl, { _method: 'DELETE' }, function(res) {
                            if (res.status === 'success') {
                                Toastify({
                                    text: res.message,
                                    duration: 3000,
                                    gravity: 'bottom',
                                    position: 'right',
                                    backgroundColor: 'linear-gradient(135deg, #dc2626, #f87171)',
                                }).showToast();
                                $('#designationTable').DataTable().ajax.reload(null, false);
                            } else {
                                Swal.fire('Error', res.message, 'error');
                            }
                        }).fail(function() {
                            Swal.fire('Error', 'Failed to delete designation.', 'error');
                        });
                    }
                });
            }

            $(document).ready(function() {
                $(document).on('click', '#btnAddDesignation', function(e) {
                    e.preventDefault();
                    openDesignationDrawer('add');
                });
            });
        </script>
    @endpush
</x-app-layout>