<x-app-layout>
    <div class="p-4">
        {{-- FILTERS --}}
        <div class="flex flex-col md:flex-row md:items-end gap-4 mb-5 bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            {{-- Notice Type Filter --}}
            <div class="flex flex-col w-full md:w-1/4">
                <x-table-filter id="filter_notice_type" label="Notice Type" tableId="noticeTable" :options="$noticeTypes" />
            </div>

            {{-- Priority Filter --}}
            <div class="flex flex-col w-full md:w-1/4">
                <x-table-filter id="filter_priority" label="Priority" tableId="noticeTable" :options="$priorities" />
            </div>

            {{-- Pinned Filter --}}
            <div class="flex flex-col w-full md:w-1/4">
                <x-table-filter id="filter_pinned" label="Pinned Status" tableId="noticeTable" :options="$pinnedStatus" />
            </div>

            {{-- Status Filter --}}
            <div class="flex flex-col w-full md:w-1/4">
                <x-table-filter id="filter_status" label="Status" tableId="noticeTable" :options="$statuses" />
            </div>

            {{-- Reset Button --}}
            <div class="w-full md:w-auto flex items-end">
                <button id="resetFilters"
                    class="px-4 py-2 text-sm font-medium text-white bg-gray-700 hover:bg-gray-800
                   rounded-lg transition active:scale-95">
                    <i class="fas fa-undo mr-1"></i> Reset
                </button>
            </div>
        </div>

        {{-- REUSABLE DATA-TABLE COMPONENT --}}
        <x-data-table id="noticeTable" title="Notice Board Management" icon="fa-solid fa-bullhorn" buttonId="btnAddNotice"
            buttonText="Add New Notice" :columns="[
                'Title',
                'Notice Type',
                'Priority',
                'Publish Date',
                'Expiry Date',
                'Pinned',
                'Status',
                'Action',
            ]" :ajaxUrl="route('notice.dataTable')" :dtColumns="[
                ['data' => 'title'],
                ['data' => 'notice_type'],
                ['data' => 'priority'],
                ['data' => 'publish_date'],
                ['data' => 'expiry_date'],
                ['data' => 'is_pinned'],
                ['data' => 'is_active'],
                ['data' => 'action', 'orderable' => false, 'searchable' => false],
            ]"
            :filters="[
                'notice_type' => '#filter_notice_type',
                'priority' => '#filter_priority',
                'is_pinned' => '#filter_pinned',
                'is_active' => '#filter_status',
            ]" />
    </div>

    {{-- DRAWER COMPONENT --}}
    <x-drawer id="notice-drawer" overlayId="notice-overlay" title="Add New Notice" submitOnClick="saveForm()">
        <form id="noticeForm" enctype="multipart/form-data">
            <input type="hidden" id="notice_id" name="id">

            {{-- Title --}}
            <div class="mb-4 animate-fade" style="animation-delay: 50ms;">
                <x-form-input label="Notice Title" name="title" placeholder="Enter notice title" required />
            </div>

            {{-- Notice Type & Priority --}}
            <div class="grid grid-cols-2 gap-3 mb-4 animate-fade" style="animation-delay: 100ms;">
                <x-form-select label="Notice Type" name="notice_type" placeholder="Select Type" required>
                    <option value="General">General</option>
                    <option value="HR">HR</option>
                    <option value="Holiday">Holiday</option>
                    <option value="Attendance">Attendance</option>
                    <option value="Payroll">Payroll</option>
                    <option value="Policy">Policy</option>
                    <option value="Training">Training</option>
                    <option value="Event">Event</option>
                    <option value="Emergency">Emergency</option>
                </x-form-select>
                <x-form-select label="Priority" name="priority" placeholder="Select Priority" required>
                    <option value="Low">Low</option>
                    <option value="Medium">Medium</option>
                    <option value="High">High</option>
                    <option value="Urgent">Urgent</option>
                </x-form-select>
            </div>

            {{-- Publish Date & Expiry Date --}}
            <div class="grid grid-cols-2 gap-3 mb-4 animate-fade" style="animation-delay: 150ms;">
                <x-form-input label="Publish Date" name="publish_date" type="datetime-local" required />
                <x-form-input label="Expiry Date" name="expiry_date" type="datetime-local" />
            </div>

            {{-- Target Type & Branch --}}
            <div class="grid grid-cols-2 gap-3 mb-4 animate-fade" style="animation-delay: 200ms;">
                <x-form-select label="Target Type" name="target_type" placeholder="Select Target" required>
                    <option value="All">All</option>
                    <option value="Department">Department</option>
                    <option value="Designation">Designation</option>
                    <option value="Branch">Branch</option>
                    <option value="Employee">Employee</option>
                </x-form-select>
                <x-form-input label="Branch ID (optional)" name="branch_id" type="number" placeholder="Branch ID" />
            </div>

            {{-- Description Textarea --}}
            <div class="mb-4 animate-fade" style="animation-delay: 250ms;">
                <x-form-textarea label="Description" name="description" placeholder="Enter notice description"
                    rows="4" required />
            </div>

            {{-- Attachment File Upload --}}
            <div class="mb-4 animate-fade" style="animation-delay: 300ms;">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Attachment File</label>
                    <input type="file" id="attachment" name="attachment"
                        class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
                    <p class="text-xs text-slate-400 mt-1">Accepted: jpg, png, pdf, doc, docx (Max: 5MB)</p>
                    <div id="existing-attachment" class="hidden mt-2 text-xs text-slate-600">
                        Current file: <a href="#" id="attachment-link" target="_blank"
                            class="text-indigo-600 underline"></a>
                        <button type="button" id="remove-attachment-btn"
                            class="ml-2 text-red-500 hover:text-red-700 text-xs underline">Remove</button>
                        <input type="hidden" id="remove_attachment" name="remove_attachment" value="0">
                    </div>
                </div>
            </div>

            {{-- Checkboxes --}}
            <div class="space-y-2 bg-slate-50 p-3 rounded-md border border-slate-100 animate-fade"
                style="animation-delay: 350ms;">

                {{-- Is Popup Checkbox --}}
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="hidden" name="is_popup" value="0">
                    <input type="checkbox" id="is_popup" name="is_popup" value="1"
                        class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500">
                    <span class="text-sm font-semibold text-slate-700">Show as Popup</span>
                </label>

                {{-- Is Pinned Checkbox --}}
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="hidden" name="is_pinned" value="0">
                    <input type="checkbox" id="is_pinned" name="is_pinned" value="1"
                        class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500">
                    <span class="text-sm font-semibold text-slate-700">Pin to Top</span>
                </label>

                {{-- Is Active Checkbox --}}
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

            function openNoticeDrawer(mode = 'add') {
                if (mode === 'edit') {
                    $('#drawerTitle').text('Update Notice');
                    $('#drawerButtonText').text('Update Notice');
                } else {
                    resetForm();
                    $('#drawerTitle').text('Add New Notice');
                    $('#drawerButtonText').text('Save Notice');
                }
                openGlobalDrawer('notice-drawer', 'notice-overlay');
            }

            function resetForm() {
                $('#noticeForm')[0].reset();
                $('#notice_id').val('');
                $('#is_popup').prop('checked', false);
                $('#is_pinned').prop('checked', false);
                $('#is_active').prop('checked', true);
                $('#existing-attachment').addClass('hidden');
                $('#remove_attachment').val('0');
            }

            function noticeEdit(id) {
                Swal.fire({
                    title: 'Loading...',
                    text: 'Fetching notice details',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                resetForm();

                let fetchUrl = "{{ route('notice.show', ':id') }}".replace(':id', id);

                $.get(fetchUrl, function(res) {
                    if (res.status) {
                        let d = res.data;
                        $('#notice_id').val(d.id);
                        $('#title').val(d.title);
                        $('#description').val(d.description);
                        $('#notice_type').val(d.notice_type);
                        $('#priority').val(d.priority);
                        $('#publish_date').val(d.publish_date ? d.publish_date.substring(0, 16) : '');
                        $('#expiry_date').val(d.expiry_date ? d.expiry_date.substring(0, 16) : '');
                        $('#target_type').val(d.target_type);
                        $('#branch_id').val(d.branch_id);
                        $('#is_popup').prop('checked', d.is_popup == 1);
                        $('#is_pinned').prop('checked', d.is_pinned == 1);
                        $('#is_active').prop('checked', d.is_active == 1);

                        // Show existing attachment link if present
                        if (d.attachment_path) {
                            $('#existing-attachment').removeClass('hidden');
                            $('#attachment-link').text(d.attachment_path.split('/').pop());
                            $('#attachment-link').attr('href', '/storage/' + d.attachment_path);
                        }

                        Swal.close();
                        openNoticeDrawer('edit');
                    } else {
                        Swal.fire('Error', res.message || 'Failed to fetch data.', 'error');
                    }
                }).fail(function() {
                    Swal.fire('Error', 'Server communication error.', 'error');
                });
            }

            $('#remove-attachment-btn').on('click', function() {
                $('#remove_attachment').val('1');
                $('#existing-attachment').addClass('hidden');
                $('#attachment').val('');
            });

            function saveForm() {
                if (isSaving) return;

                let id = $('#notice_id').val();
                let url = id ? "{{ route('notice.update', ':id') }}".replace(':id', id) : "{{ route('notice.store') }}";

                // Use FormData for file upload support
                let formData = new FormData($('#noticeForm')[0]);
                if (id) formData.append('_method', 'PUT');

                isSaving = true;
                $('#drawerButtonText').text('Saving...');
                $('#saveBtn').prop('disabled', true).addClass('opacity-70 cursor-not-allowed');

                Swal.fire({
                    title: 'Confirm',
                    text: id ? "Update notice?" : "Create notice?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#16a34a',
                    cancelButtonColor: '#d33'
                }).then((result) => {
                    if (!result.isConfirmed) {
                        isSaving = false;
                        $('#drawerButtonText').text(id ? 'Update Notice' : 'Save Notice');
                        $('#saveBtn').prop('disabled', false).removeClass('opacity-70 cursor-not-allowed');
                        return;
                    }

                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
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
                                    backgroundColor: "linear-gradient(135deg, #0f172a, #1e1b4b)",
                                }).showToast();
                                closeGlobalDrawer('notice-drawer', 'notice-overlay');
                                $('#noticeTable').DataTable().ajax.reload(null, false);
                            } else {
                                Swal.fire('Error', res.message, 'error');
                                $('#drawerButtonText').text(id ? 'Update Notice' : 'Save Notice');
                            }
                        },
                        error: function(xhr) {
                            isSaving = false;
                            $('#saveBtn').prop('disabled', false).removeClass(
                                'opacity-70 cursor-not-allowed');
                            $('#drawerButtonText').text(id ? 'Update Notice' : 'Save Notice');
                            let errorMsg = xhr.responseJSON?.message || 'Server error occurred';
                            Swal.fire('Error', errorMsg, 'error');
                        }
                    });
                });
            }

            function noticeDelete(id) {
                Swal.fire({
                    title: 'Delete?',
                    text: "This cannot be undone",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#4b5563'
                }).then((r) => {
                    if (r.isConfirmed) {
                        let deleteUrl = "{{ route('notice.destroy', ':id') }}".replace(':id', id);
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
                                    backgroundColor: "red",
                                }).showToast();
                                $('#noticeTable').DataTable().ajax.reload(null, false);
                            } else {
                                Swal.fire('Error', res.message, 'error');
                            }
                        }).fail(function() {
                            Swal.fire('Error', 'Failed to delete notice.', 'error');
                        });
                    }
                });
            }

            $(document).ready(function() {
                $(document).on('click', '#btnAddNotice', function(e) {
                    e.preventDefault();
                    openNoticeDrawer('add');
                });

                // Reset filters
                $('#resetFilters').on('click', function() {
                    $('#filter_notice_type, #filter_priority, #filter_pinned, #filter_status').val('').trigger('change');
                });
            });
        </script>
    @endpush
</x-app-layout>
