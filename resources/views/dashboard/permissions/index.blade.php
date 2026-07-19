<x-app-layout>

    <div class="p-4">

        {{-- REUSABLE DATA-TABLE COMPONENT --}}
        <x-data-table id="permissionTable" title="Permission Management" icon="fa-solid fa-key"
            buttonId="btnAddPermission" buttonText="Add New Permission" :columns="[ 'ID', 'Name', 'Slug', 'Group', 'Description', 'Created At', 'Action']" :ajaxUrl="route('permissions.dataTable')"
            :dtColumns="[
                ['data' => 'id', 'width' => '60px'],
                ['data' => 'name'],
                ['data' => 'slug'],
                ['data' => 'group', 'width' => '100px'],
                ['data' => 'description'],
                ['data' => 'created_at'],
                ['data' => 'action', 'orderable' => false, 'searchable' => false],
            ]" :exportButtons="true" />
    </div>

    {{-- DRAWER COMPONENT --}}
    <x-drawer id="permission-drawer" overlayId="permission-overlay" title="Add New Permission"
        submitOnClick="savePermissionForm()">
        <form id="permissionForm">
            <input type="hidden" name="id" id="permission_id">

            {{-- Permission Name --}}
            <div class="mb-4 animate-fade" style="animation-delay: 50ms;">
                <x-form-input label="Permission Name" name="name" id="perm_name" placeholder="e.g. Manage Products" required />
                <p class="mt-1 text-xs text-gray-400">Slug will be auto-generated: e.g. <span id="slugPreview">manage-products</span></p>
            </div>

            {{-- Group --}}
            <div class="mb-4 animate-fade" style="animation-delay: 100ms;">
                <x-form-select label="Group" name="group" id="perm_group" placeholder="-- Select or Type Group --">
                    @foreach ($groups as $group)
                        <option value="{{ $group }}">{{ $group }}</option>
                    @endforeach
                    <option value="__new__" disabled>─── Custom ───</option>
                </x-form-select>
                <div id="customGroupWrap" class="mt-2 hidden animate-fade">
                    <x-form-input label="Custom Group Name" name="group_custom" id="perm_group_custom" placeholder="Enter new group name" />
                </div>
            </div>

            {{-- Description --}}
            <div class="mb-4 animate-fade" style="animation-delay: 150ms;">
                <x-form-textarea label="Description" name="description" id="perm_description" placeholder="What does this permission allow?" rows="2"></x-form-textarea>
            </div>
        </form>
    </x-drawer>

    @push('scripts')
        <script>
            let isSaving = false;

            // Auto-generate slug preview
            $('#perm_name').on('input', function() {
                const slug = $(this).val()
                    .toLowerCase()
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/^-|-$/g, '');
                $('#slugPreview').text(slug || 'manage-products');
            });

            // Toggle custom group input
            $('#perm_group').on('change', function() {
                if ($(this).val() === '__new__') {
                    $('#customGroupWrap').removeClass('hidden');
                } else {
                    $('#customGroupWrap').addClass('hidden');
                }
            });

            function openPermissionDrawer(mode = 'add') {
                if (mode === 'edit') {
                    $('#drawerTitle').text('Update Permission');
                    $('#drawerButtonText').text('Update Permission');
                } else {
                    resetPermissionForm();
                    $('#drawerTitle').text('Add New Permission');
                    $('#drawerButtonText').text('Save Permission');
                }
                openGlobalDrawer('permission-drawer', 'permission-overlay');
            }

            function resetPermissionForm() {
                $('#permissionForm')[0].reset();
                $('#permission_id').val('');
                $('#customGroupWrap').addClass('hidden');
                $('#slugPreview').text('manage-products');
            }

            function permissionEdit(id) {
                Swal.fire({
                    title: 'Loading...',
                    text: 'Fetching permission details',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                resetPermissionForm();
                let fetchUrl = "{{ route('permissions.show', ':id') }}".replace(':id', id);

                $.get(fetchUrl, function(res) {
                    Swal.close();
                    if (res.status) {
                        let p = res.permission;
                        $('#permission_id').val(p.id);
                        $('input[name="name"]').val(p.name);
                        $('select[name="group"]').val(p.group);
                        $('textarea[name="description"]').val(p.description);
                        $('#slugPreview').text(p.slug);
                        openPermissionDrawer('edit');
                    } else {
                        Swal.fire('Error', res.message || 'Failed to fetch data.', 'error');
                    }
                }).fail(function() {
                    Swal.close();
                    Swal.fire('Error', 'Server communication error.', 'error');
                });
            }

            function savePermissionForm() {
                if (isSaving) return;

                let id = $('#permission_id').val();
                let url = id ? "{{ route('permissions.update', ':id') }}".replace(':id', id) :
                    "{{ route('permissions.store') }}";

                // Handle custom group
                let groupVal = $('#perm_group').val();
                if (groupVal === '__new__') {
                    groupVal = $('#perm_group_custom').val().trim();
                    if (!groupVal) {
                        Swal.fire('Validation Error', 'Please enter a custom group name.', 'error');
                        return;
                    }
                }

                let formData = $('#permissionForm').serialize() + '&group=' + encodeURIComponent(groupVal);
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
                        $('#drawerButtonText').text(id ? 'Update Permission' : 'Save Permission');

                        if (res.status === 'success' || res.status === true) {
                            Toastify({
                                text: res.message || 'Saved successfully',
                                duration: 3000,
                                gravity: "bottom",
                                position: "right",
                                style: { background: "linear-gradient(135deg, #16a34a, #4ade80)" },
                            }).showToast();
                            closeGlobalDrawer('permission-drawer', 'permission-overlay');
                            $('#permissionTable').DataTable().ajax.reload(null, false);
                        } else {
                            Swal.fire('Error', res.message || 'Something went wrong', 'error');
                        }
                    },
                    error: function(xhr) {
                        isSaving = false;
                        $('#saveBtn').prop('disabled', false).removeClass('opacity-70 cursor-not-allowed');
                        $('#drawerButtonText').text(id ? 'Update Permission' : 'Save Permission');

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

            function permissionDelete(id) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This will remove this permission from all roles!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#4b5563',
                    confirmButtonText: 'Yes, delete it!'
                }).then((r) => {
                    if (r.isConfirmed) {
                        let deleteUrl = "{{ route('permissions.destroy', ':id') }}".replace(':id', id);
                        $.post(deleteUrl, { _method: 'DELETE' }, function(res) {
                            if (res.status === 'success' || res.status === true) {
                                Toastify({
                                    text: res.message || 'Deleted successfully',
                                    duration: 3000,
                                    gravity: "bottom",
                                    position: "right",
                                    style: { background: "linear-gradient(135deg, #dc2626, #f87171)" },
                                }).showToast();
                                $('#permissionTable').DataTable().ajax.reload(null, false);
                            } else {
                                Swal.fire('Error', res.message || 'Deletion failed.', 'error');
                            }
                        }).fail(function() {
                            Swal.fire('Error', 'Failed to communicate with server.', 'error');
                        });
                    }
                });
            }

            $(document).ready(function() {
                $(document).on('click', '#btnAddPermission', function(e) {
                    e.preventDefault();
                    openPermissionDrawer('add');
                });
            });
        </script>
    @endpush
</x-app-layout>