<x-app-layout>

    <div class="p-4">

        {{-- REUSABLE DATA-TABLE COMPONENT --}}
        <x-data-table id="roleTable" title="Roles & Permissions" icon="fa-solid fa-shield-alt"
            buttonId="btnAddRole" buttonText="Add New Role" :columns="[ 'ID', 'Name', 'Slug', 'Description', 'System', 'Permissions', 'Users', 'Created At', 'Action']" :ajaxUrl="route('roles.dataTable')"
            :dtColumns="[
                ['data' => 'id', 'width' => '60px'],
                ['data' => 'name'],
                ['data' => 'slug'],
                ['data' => 'description'],
                ['data' => 'is_system', 'name' => 'is_system'],
                ['data' => 'permissions_count', 'name' => 'permissions_count', 'width' => '80px'],
                ['data' => 'users_count', 'name' => 'users_count', 'width' => '60px'],
                ['data' => 'created_at'],
                ['data' => 'action', 'orderable' => false, 'searchable' => false],
            ]" :exportButtons="true" />
    </div>

    {{-- DRAWER COMPONENT --}}
    <x-drawer id="role-drawer" overlayId="role-overlay" title="Add New Role" maxWidth="max-w-2xl"
        submitOnClick="saveRoleForm()">
        <form id="roleForm">
            <input type="hidden" name="id" id="role_id">

            {{-- Role Name --}}
            <div class="mb-4 animate-fade" style="animation-delay: 50ms;">
                <x-form-input label="Role Name" name="name" id="role_name" placeholder="Enter role name (e.g. HR Manager)" required />
            </div>

            {{-- Description --}}
            <div class="mb-4 animate-fade" style="animation-delay: 100ms;">
                <x-form-textarea label="Description" name="description" id="role_description" placeholder="Describe what this role can do (optional)" rows="2"></x-form-textarea>
            </div>

            {{-- Permissions --}}
            <div class="animate-fade" style="animation-delay: 150ms;">
                <label class="font-semibold text-sm text-slate-700 block mb-3">
                    Permissions <span class="text-rose-500 font-bold">*</span>
                </label>
                <div class="max-h-96 overflow-y-auto border border-gray-200 rounded-lg p-3">
                    @foreach ($permissions as $group => $groupPermissions)
                        <div class="mb-3 border border-gray-100 rounded-lg overflow-hidden">
                            <div class="bg-gray-50 px-3 py-1.5 border-b border-gray-100 flex items-center justify-between">
                                <span class="text-xs font-semibold text-gray-700">{{ $group }}</span>
                                <label class="flex items-center gap-1 text-xs text-gray-500 cursor-pointer">
                                    <input type="checkbox" class="group-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" data-group="{{ $group }}">
                                    Select All
                                </label>
                            </div>
                            <div class="p-2 grid grid-cols-2 gap-1">
                                @foreach ($groupPermissions as $permission)
                                    <label class="flex items-center gap-1.5 text-xs text-gray-600 cursor-pointer hover:text-gray-800 py-0.5">
                                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                            class="perm-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                            data-group="{{ $group }}">
                                        {{ $permission->name }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </form>
    </x-drawer>

    @push('scripts')
        <script>
            let isSaving = false;

            function openRoleDrawer(mode = 'add') {
                if (mode === 'edit') {
                    $('#drawerTitle').text('Update Role');
                    $('#drawerButtonText').text('Update Role');
                } else {
                    resetRoleForm();
                    $('#drawerTitle').text('Add New Role');
                    $('#drawerButtonText').text('Save Role');
                }
                openGlobalDrawer('role-drawer', 'role-overlay');
            }

            function resetRoleForm() {
                $('#roleForm')[0].reset();
                $('#role_id').val('');
                $('.perm-checkbox').prop('checked', false);
                $('.group-checkbox').prop('checked', false);
            }

            function roleEdit(id) {
                Swal.fire({
                    title: 'Loading...',
                    text: 'Fetching role details',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                resetRoleForm();
                let fetchUrl = "{{ route('roles.show', ':id') }}".replace(':id', id);

                $.get(fetchUrl, function(res) {
                    Swal.close();
                    if (res.status) {
                        let r = res.role;
                        $('#role_id').val(r.id);
                        $('input[name="name"]').val(r.name);
                        $('textarea[name="description"]').val(r.description);

                        // Check the permissions that belong to this role
                        let permIds = r.permissions.map(p => p.id);
                        $('.perm-checkbox').each(function() {
                            $(this).prop('checked', permIds.includes(parseInt($(this).val())));
                        });

                        openRoleDrawer('edit');
                    } else {
                        Swal.fire('Error', res.message || 'Failed to fetch data.', 'error');
                    }
                }).fail(function() {
                    Swal.close();
                    Swal.fire('Error', 'Server communication error.', 'error');
                });
            }

            function saveRoleForm() {
                if (isSaving) return;

                let id = $('#role_id').val();
                let url = id ? "{{ route('roles.update', ':id') }}".replace(':id', id) :
                    "{{ route('roles.store') }}";

                let formData = $('#roleForm').serialize();
                if (id) formData += '&_method=PATCH';

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
                        $('#drawerButtonText').text(id ? 'Update Role' : 'Save Role');

                        if (res.status === 'success' || res.status === true) {
                            Toastify({
                                text: res.message || 'Saved successfully',
                                duration: 3000,
                                gravity: "bottom",
                                position: "right",
                                style: { background: "linear-gradient(135deg, #16a34a, #4ade80)" },
                            }).showToast();
                            closeGlobalDrawer('role-drawer', 'role-overlay');
                            $('#roleTable').DataTable().ajax.reload(null, false);
                        } else {
                            Swal.fire('Error', res.message || 'Something went wrong', 'error');
                        }
                    },
                    error: function(xhr) {
                        isSaving = false;
                        $('#saveBtn').prop('disabled', false).removeClass('opacity-70 cursor-not-allowed');
                        $('#drawerButtonText').text(id ? 'Update Role' : 'Save Role');

                        let errorMsg = 'Server error occurred';
                        if (xhr.responseJSON?.errors) {
                            errorMsg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                        } else if (xhr.responseJSON?.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        Swal.fire({ icon: 'error', title: 'Validation Error', html: errorMsg });
                    }
                });
            }

            function roleDelete(id) {
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
                        let deleteUrl = "{{ route('roles.destroy', ':id') }}".replace(':id', id);
                        $.post(deleteUrl, { _method: 'DELETE' }, function(res) {
                            if (res.status === 'success' || res.status === true) {
                                Toastify({
                                    text: res.message || 'Deleted successfully',
                                    duration: 3000,
                                    gravity: "bottom",
                                    position: "right",
                                    style: { background: "linear-gradient(135deg, #dc2626, #f87171)" },
                                }).showToast();
                                $('#roleTable').DataTable().ajax.reload(null, false);
                            } else {
                                Swal.fire('Error', res.message || 'Deletion failed.', 'error');
                            }
                        }).fail(function() {
                            Swal.fire('Error', 'Failed to communicate with server.', 'error');
                        });
                    }
                });
            }

            // Group checkbox toggle
            $(document).on('change', '.group-checkbox', function() {
                const group = this.dataset.group;
                $(`.perm-checkbox[data-group="${group}"]`).prop('checked', this.checked);
            });

            $(document).ready(function() {
                $(document).on('click', '#btnAddRole', function(e) {
                    e.preventDefault();
                    openRoleDrawer('add');
                });
            });
        </script>
    @endpush
</x-app-layout>