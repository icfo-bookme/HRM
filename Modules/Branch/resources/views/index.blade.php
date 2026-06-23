<x-app-layout>
    <div class="p-4">
        {{-- REUSABLE DATA-TABLE COMPONENT --}}
        <x-data-table id="branchTable" title="Branch Management" icon="fa-solid fa-code-branch" buttonId="btnAddBranch"
            buttonText="Add New Branch" :columns="[ 'Code', 'Branch Name', 'Type', 'Status', 'Created At', 'Action']" :ajaxUrl="route('branches.dataTable')" :dtColumns="[
                {{-- ['data' => 'company.name'], --}}
                ['data' => 'code'],
                ['data' => 'name'],
                ['data' => 'is_head_office'],
                ['data' => 'is_active'],
                ['data' => 'created_at'],
                ['data' => 'action', 'orderable' => false, 'searchable' => false],
            ]" />
    </div>

    {{-- DRAWER COMPONENT --}}
    <x-drawer id="branch-drawer" overlayId="branch-overlay" title="Add New Branch" submitOnClick="saveForm()">
        <form id="branchForm">
            <input type="hidden" id="branch_id" name="id">

            {{-- Company Select --}}
            {{-- <div class="mb-4 animate-fade" style="animation-delay: 50ms;">
                <x-form-select label="Company" name="company_id" placeholder="Select Company">
                    @foreach (\Modules\Company\Models\Company::all() as $company)
                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                    @endforeach
                </x-form-select>
            </div> --}}

            {{-- Code & Name --}}
            <div class="grid grid-cols-2 gap-3 mb-4 animate-fade" style="animation-delay: 100ms;">
                <x-form-input label="Code" name="code" placeholder="Code" required />
                <x-form-input label="Name" name="name" placeholder="Name" required />
            </div>

            {{-- Phone & Email --}}
            <div class="grid grid-cols-2 gap-3 mb-4 animate-fade" style="animation-delay: 150ms;">
                <x-form-input label="Phone" name="phone" placeholder="Phone" />
                <x-form-input label="Email" name="email" type="email" placeholder="Email" />
            </div>

            {{-- Address Textarea --}}
            <div class="mb-4 animate-fade" style="animation-delay: 200ms;">
                <x-form-textarea label="Address" name="address" placeholder="Address" rows="3" />
            </div>

            {{-- City & Country --}}
            <div class="grid grid-cols-2 gap-3 mb-4 animate-fade" style="animation-delay: 250ms;">
                <x-form-input label="City" name="city" placeholder="City" />
                <x-form-input label="Country" name="country" value="Bangladesh" placeholder="Country" />
            </div>

            {{-- Head Office & Active Checkboxes --}}
            <div class="space-y-2 bg-slate-50 p-3 rounded-md border border-slate-100 animate-fade"
                style="animation-delay: 300ms;">

                {{-- Head Office Checkbox --}}
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="hidden" name="is_head_office" value="0">
                    <input type="checkbox" id="is_head_office" name="is_head_office" value="1"
                        class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500">
                    <span class="text-sm font-semibold text-slate-700">Head Office</span>
                </label>

                {{-- Active Checkbox --}}
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="hidden" name="is_active" value="0">
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
            const csrfToken = '{{ csrf_token() }}';

            function openBranchDrawer(mode = 'add') {
                if (mode === 'edit') {
                    $('#drawerTitle').text('Update Branch');
                    $('#drawerButtonText').text('Update Branch');
                } else {
                    resetForm();
                    $('#drawerTitle').text('Add New Branch');
                    $('#drawerButtonText').text('Save Branch');
                }
                openGlobalDrawer('branch-drawer', 'branch-overlay');
            }

            function resetForm() {
                $('#branchForm')[0].reset();
                $('#branch_id').val('');
                $('#is_active').prop('checked', true);
            }

            function branchEdit(id) {

                Swal.fire({
                    title: 'Loading...',
                    text: `Fetching branch details`,
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                resetForm();

                let fetchUrl = "{{ route('branches.show', ':id') }}".replace(':id', id);

                $.get(fetchUrl, function(res) {
                    if (res.status) {
                        let d = res.data;
                        $('#branch_id').val(d.id);
                        $('#company_id').val(d.company_id);
                        $('#code').val(d.code);
                        $('#name').val(d.name);
                        $('#phone').val(d.phone);
                        $('#email').val(d.email);
                        $('#address').val(d.address);
                        $('#city').val(d.city);
                        $('#country').val(d.country);
                        $('#is_head_office').prop('checked', d.is_head_office == 1);
                        $('#is_active').prop('checked', d.is_active == 1);

                        Swal.close();
                        openBranchDrawer('edit');
                    } else {
                        Swal.fire('Error', res.message || 'Failed to fetch data.', 'error');
                    }
                }).fail(function() {
                    Swal.fire('Error', 'Server communication error.', 'error');
                });
            }

            function saveForm() {
                if (isSaving) return;

                let id = $('#branch_id').val();
                let url = id ? "{{ route('branches.update', ':id') }}".replace(':id', id) : "{{ route('branches.store') }}";
                let formData = $('#branchForm').serialize();

                if (id) formData += '&_method=PUT';
                formData += `&_token=${csrfToken}`;

                isSaving = true;
                $('#drawerButtonText').text('Saving...');
                $('#saveBtn').prop('disabled', true).addClass('opacity-70 cursor-not-allowed');

                Swal.fire({
                    title: 'Confirm',
                    text: id ? "Update branch?" : "Create branch?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#16a34a',
                    cancelButtonColor: '#d33'
                }).then((result) => {
                    if (!result.isConfirmed) {
                        isSaving = false;
                        $('#drawerButtonText').text(id ? 'Update Branch' : 'Save Branch');
                        $('#saveBtn').prop('disabled', false).removeClass('opacity-70 cursor-not-allowed');
                        return;
                    }

                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: formData,
                        success: function(res) {
                            isSaving = false;
                            $('#saveBtn').prop('disabled', false).removeClass(
                                'opacity-70 cursor-not-allowed');

                            if (res.status === true) {
                                Toastify({
                                    text: res.message,
                                    duration: 3000,
                                    gravity: "bottom",
                                    position: "right",
                                    backgroundColor: status == 1 ? "green" :
                                        "linear-gradient(135deg, #0f172a, #1e1b4b)",
                                }).showToast();
                                closeGlobalDrawer('branch-drawer', 'branch-overlay');
                                $('#branchTable').DataTable().ajax.reload(null, false);
                            } else {
                                Swal.fire('Error', res.message, 'error');
                                $('#drawerButtonText').text(id ? 'Update Branch' : 'Save Branch');
                            }
                        },
                        error: function(xhr) {
                            isSaving = false;
                            $('#saveBtn').prop('disabled', false).removeClass(
                                'opacity-70 cursor-not-allowed');
                            $('#drawerButtonText').text(id ? 'Update Branch' : 'Save Branch');
                            let errorMsg = xhr.responseJSON?.message || 'Server error occurred';
                            Swal.fire('Error', errorMsg, 'error');
                        }
                    });
                });
            }

            function branchDelete(id) {
                Swal.fire({
                    title: 'Delete?',
                    text: "This cannot be undone",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#4b5563'
                }).then((r) => {
                    if (r.isConfirmed) {
                        let deleteUrl = "{{ route('branches.destroy', ':id') }}".replace(':id', id);
                        $.post(deleteUrl, {
                            _method: 'DELETE',
                            _token: csrfToken
                        }, function(res) {
                            if (res.status) {
                                Toastify({
                                    text: res.message,
                                    duration: 3000,
                                    gravity: "bottom",
                                    position: "right",
                                    backgroundColor: status == 1 ? "red" : "red",
                                }).showToast();
                                $('#branchTable').DataTable().ajax.reload(null, false);
                            } else {
                                Swal.fire('Error', res.message, 'error');
                            }
                        }).fail(function() {
                            Swal.fire('Error', 'Failed to delete branch.', 'error');
                        });
                    }
                });
            }

            $(document).ready(function() {
                $(document).on('click', '#btnAddBranch', function(e) {
                    e.preventDefault();
                    openBranchDrawer('add');
                });
            });
        </script>
    @endpush
</x-app-layout>
