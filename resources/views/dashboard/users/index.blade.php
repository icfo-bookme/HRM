<x-app-layout>

    <div class="p-4">

        {{-- REUSABLE DATA-TABLE COMPONENT --}}
        <x-data-table id="userTable" title="User Management" icon="fa-solid fa-users"
            buttonId="btnAddUser" buttonText="Add New User" :columns="[ 'ID', 'Name', 'Email', 'Role', 'Created At', 'Action']" :ajaxUrl="route('users.dataTable')"
            :dtColumns="[
                ['data' => 'id', 'width' => '60px'],
                ['data' => 'name'],
                ['data' => 'email'],
                ['data' => 'role_id', 'name' => 'role_id'],
                ['data' => 'created_at'],
                ['data' => 'action', 'orderable' => false, 'searchable' => false],
            ]" :exportButtons="true" />
    </div>

    {{-- DRAWER COMPONENT --}}
    <x-drawer id="user-drawer" overlayId="user-overlay" title="Add New User"
        submitOnClick="saveUserForm()">
        <form id="userForm">
            <input type="hidden" name="id" id="user_id">

            {{-- Name --}}
            <div class="mb-4 animate-fade" style="animation-delay: 50ms;">
                <x-form-input label="Name" name="name" id="name" placeholder="Enter full name" required />
            </div>

            {{-- Email --}}
            <div class="mb-4 animate-fade" style="animation-delay: 100ms;">
                <x-form-input label="Email" name="email" id="email" type="email" placeholder="Enter email address" required />
            </div>

            {{-- Password --}}
            <div class="mb-4 animate-fade" style="animation-delay: 150ms;">
                <x-form-input label="Password" name="password" id="password" type="password" placeholder="Enter password" required />
                <small id="passwordHelp" class="text-xs text-gray-400 hidden">Leave empty to keep current password</small>
            </div>

            {{-- Role --}}
            <div class="mb-4 animate-fade" style="animation-delay: 200ms;">
                <x-form-select label="Assign Role" name="role_id" id="role_id" placeholder="-- Select Role --">
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                    @endforeach
                </x-form-select>
            </div>

            {{-- Employee --}}
            <div class="mb-4 animate-fade" style="animation-delay: 250ms;">
                <x-form-select label="Link Employee" name="employee_id" id="employee_id" placeholder="-- Select Employee (Optional) --">
                    @foreach ($employees as $employee)
                        <option value="{{ $employee->id }}">
                            {{ $employee->employee_code }} - {{ $employee->personalInfo?->full_name ?? 'N/A' }}
                        </option>
                    @endforeach
                </x-form-select>
            </div>
        </form>
    </x-drawer>

    @push('scripts')
        <script>
            let isSaving = false;

            function openUserDrawer(mode = 'add') {
                if (mode === 'edit') {
                    $('#drawerTitle').text('Update User');
                    $('#drawerButtonText').text('Update User');
                    $('#password').prop('required', false);
                    $('#passwordHelp').removeClass('hidden');
                } else {
                    resetUserForm();
                    $('#drawerTitle').text('Add New User');
                    $('#drawerButtonText').text('Save User');
                    $('#password').prop('required', true);
                    $('#passwordHelp').addClass('hidden');
                }
                openGlobalDrawer('user-drawer', 'user-overlay');
            }

            function resetUserForm() {
                $('#userForm')[0].reset();
                $('#user_id').val('');
            }

            function userEdit(id) {
                Swal.fire({
                    title: 'Loading...',
                    text: 'Fetching user details',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                resetUserForm();
                let fetchUrl = "{{ route('users.show', ':id') }}".replace(':id', id);

                $.get(fetchUrl, function(res) {
                    Swal.close();
                    if (res.status) {
                        let u = res.user;
                        $('#user_id').val(u.id);
                        $('input[name="name"]').val(u.name);
                        $('input[name="email"]').val(u.email);
                        $('select[name="role_id"]').val(u.role_id || '');
                        $('select[name="employee_id"]').val(u.employee_id || '');
                        openUserDrawer('edit');
                    } else {
                        Swal.fire('Error', res.message || 'Failed to fetch data.', 'error');
                    }
                }).fail(function() {
                    Swal.close();
                    Swal.fire('Error', 'Server communication error.', 'error');
                });
            }

            function saveUserForm() {
                if (isSaving) return;

                let id = $('#user_id').val();
                let url = id ? "{{ route('users.update', ':id') }}".replace(':id', id) :
                    "{{ route('users.store') }}";

                let formData = $('#userForm').serialize();
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
                        $('#drawerButtonText').text(id ? 'Update User' : 'Save User');

                        if (res.status === 'success' || res.status === true) {
                            Toastify({
                                text: res.message || 'Saved successfully',
                                duration: 3000,
                                gravity: "bottom",
                                position: "right",
                                style: { background: "linear-gradient(135deg, #16a34a, #4ade80)" },
                            }).showToast();
                            closeGlobalDrawer('user-drawer', 'user-overlay');
                            $('#userTable').DataTable().ajax.reload(null, false);
                        } else {
                            Swal.fire('Error', res.message || 'Something went wrong', 'error');
                        }
                    },
                    error: function(xhr) {
                        isSaving = false;
                        $('#saveBtn').prop('disabled', false).removeClass('opacity-70 cursor-not-allowed');
                        $('#drawerButtonText').text(id ? 'Update User' : 'Save User');

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

            function userDelete(id) {
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
                        let deleteUrl = "{{ route('users.destroy', ':id') }}".replace(':id', id);
                        $.post(deleteUrl, { _method: 'DELETE' }, function(res) {
                            if (res.status === 'success' || res.status === true) {
                                Toastify({
                                    text: res.message || 'Deleted successfully',
                                    duration: 3000,
                                    gravity: "bottom",
                                    position: "right",
                                    style: { background: "linear-gradient(135deg, #dc2626, #f87171)" },
                                }).showToast();
                                $('#userTable').DataTable().ajax.reload(null, false);
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
                $(document).on('click', '#btnAddUser', function(e) {
                    e.preventDefault();
                    openUserDrawer('add');
                });
            });
        </script>
    @endpush
</x-app-layout>