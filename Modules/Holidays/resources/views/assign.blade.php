<x-app-layout>
    <div class="p-4">
        {{-- REUSABLE DATA-TABLE COMPONENT --}}
        <x-data-table id="holidayAssignmentTable" title="Assign Holidays" icon="fa-solid fa-user-check" buttonId="btnAddAssignment"
            buttonText="Assign Holiday" :columns="['Holiday', 'Branch', 'Departments', 'Created At', 'Action']"
            :ajaxUrl="route('holidays.assign.dataTable')" :dtColumns="[
                ['data' => 'holiday_id'],
                ['data' => 'branch_id'],
                ['data' => 'department_ids'],
                ['data' => 'created_at'],
                ['data' => 'action', 'orderable' => false, 'searchable' => false],
            ]" />
    </div>

    {{-- DRAWER COMPONENT --}}
    <x-drawer id="assignment-drawer" overlayId="assignment-overlay" title="Assign Holiday" submitOnClick="saveForm()">
        <form id="assignmentForm">
            <input type="hidden" id="assignment_id" name="id">

            {{-- Holiday Select --}}
            <div class="mb-4 animate-fade" style="animation-delay: 50ms;">
                <x-form-select label="Holiday" name="holiday_id" placeholder="Select Holiday" required>
                    @foreach (\Modules\Holidays\Models\Holiday::all() as $holiday)
                        <option value="{{ $holiday->id }}">{{ $holiday->name }} ({{ $holiday->holiday_date->format('d M Y') }})</option>
                    @endforeach
                </x-form-select>
            </div>

            {{-- Branch Select --}}
            <div class="mb-4 animate-fade" style="animation-delay: 100ms;">
                <x-form-select label="Branch" name="branch_id" placeholder="All Branches">
                    @foreach (\Modules\Branch\Models\Branch::all() as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </x-form-select>
            </div>

            {{-- Multiple Department Selection --}}
            <div class="mb-4 animate-fade" style="animation-delay: 150ms;">
                <label class="font-semibold text-sm text-slate-700 block mb-1">Departments</label>
                <div class="border border-slate-300 rounded-md p-3 bg-white max-h-48 overflow-y-auto space-y-2">
                    <label class="flex items-center gap-2 cursor-pointer select-none border-b border-slate-200 pb-2 mb-1">
                        <input type="checkbox" id="selectAllDept"
                            class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500">
                        <span class="text-sm font-semibold text-indigo-600">Select All</span>
                    </label>
                    @php $departments = \Modules\Department\Models\Department::all(); @endphp
                    @forelse ($departments as $dept)
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" name="department_ids[]" value="{{ $dept->id }}"
                                class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500 dept-checkbox">
                            <span class="text-sm text-slate-700">{{ $dept->name }}</span>
                        </label>
                    @empty
                        <p class="text-sm text-slate-400">No departments found.</p>
                    @endforelse
                </div>
            </div>
        </form>
    </x-drawer>

    @push('scripts')
        <script>
            let isSaving = false;
            const csrfToken = '{{ csrf_token() }}';

            function openAssignmentDrawer(mode = 'add') {
                if (mode === 'edit') {
                    $('#drawerTitle').text('Update Assignment');
                    $('#drawerButtonText').text('Update Assignment');
                } else {
                    resetForm();
                    $('#drawerTitle').text('Assign Holiday');
                    $('#drawerButtonText').text('Assign Holiday');
                }
                openGlobalDrawer('assignment-drawer', 'assignment-overlay');
            }

            function resetForm() {
                $('#assignmentForm')[0].reset();
                $('#assignment_id').val('');
                $('.dept-checkbox').prop('checked', false);
                $('#selectAllDept').prop('checked', false);
            }

            function holidayAssignmentEdit(id) {
                Swal.fire({
                    title: 'Loading...',
                    text: 'Fetching assignment details',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                resetForm();

                let fetchUrl = "{{ route('holidays.assign.show', ':id') }}".replace(':id', id);

                $.get(fetchUrl, function(res) {
                    if (res.status) {
                        let d = res.data;
                        $('#assignment_id').val(d.id);
                        $('#holiday_id').val(d.holiday_id);
                        $('#branch_id').val(d.branch_id);

                        // Check all departments assigned to this holiday+branch combo
                        if (d.all_department_ids && d.all_department_ids.length > 0) {
                            d.all_department_ids.forEach(function(deptId) {
                                $('.dept-checkbox[value="' + deptId + '"]').prop('checked', true);
                            });
                        }

                        Swal.close();
                        openAssignmentDrawer('edit');
                    } else {
                        Swal.fire('Error', res.message || 'Failed to fetch data.', 'error');
                    }
                }).fail(function() {
                    Swal.fire('Error', 'Server communication error.', 'error');
                });
            }

            function saveForm() {
                if (isSaving) return;

                let id = $('#assignment_id').val();
                let url = id ? "{{ route('holidays.assign.update', ':id') }}".replace(':id', id) : "{{ route('holidays.assign.store') }}";
                let formData = $('#assignmentForm').serialize();

                if (id) formData += '&_method=PUT';
                formData += `&_token=${csrfToken}`;

                isSaving = true;
                $('#drawerButtonText').text('Saving...');
                $('#saveBtn').prop('disabled', true).addClass('opacity-70 cursor-not-allowed');

                Swal.fire({
                    title: 'Confirm',
                    text: id ? "Update assignment?" : "Assign holiday?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#16a34a',
                    cancelButtonColor: '#d33'
                }).then((result) => {
                    if (!result.isConfirmed) {
                        isSaving = false;
                        $('#drawerButtonText').text(id ? 'Update Assignment' : 'Assign Holiday');
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
                                    backgroundColor: "linear-gradient(135deg, #0f172a, #1e1b4b)",
                                }).showToast();
                                closeGlobalDrawer('assignment-drawer', 'assignment-overlay');
                                $('#holidayAssignmentTable').DataTable().ajax.reload(null, false);
                            } else {
                                Swal.fire('Error', res.message, 'error');
                                $('#drawerButtonText').text(id ? 'Update Assignment' : 'Assign Holiday');
                            }
                        },
                        error: function(xhr) {
                            isSaving = false;
                            $('#saveBtn').prop('disabled', false).removeClass(
                                'opacity-70 cursor-not-allowed');
                            $('#drawerButtonText').text(id ? 'Update Assignment' : 'Assign Holiday');
                            let errorMsg = xhr.responseJSON?.message || 'Server error occurred';
                            Swal.fire('Error', errorMsg, 'error');
                        }
                    });
                });
            }

            function holidayAssignmentDelete(id) {
                Swal.fire({
                    title: 'Delete?',
                    text: "This cannot be undone",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#4b5563'
                }).then((r) => {
                    if (r.isConfirmed) {
                        let deleteUrl = "{{ route('holidays.assign.destroy', ':id') }}".replace(':id', id);
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
                                $('#holidayAssignmentTable').DataTable().ajax.reload(null, false);
                            } else {
                                Swal.fire('Error', res.message, 'error');
                            }
                        }).fail(function() {
                            Swal.fire('Error', 'Failed to delete assignment.', 'error');
                        });
                    }
                });
            }

            $(document).ready(function() {
                $(document).on('click', '#btnAddAssignment', function(e) {
                    e.preventDefault();
                    openAssignmentDrawer('add');
                });

                // Select All departments
                $(document).on('change', '#selectAllDept', function() {
                    let isChecked = $(this).prop('checked');
                    $('.dept-checkbox').prop('checked', isChecked);
                });

                // If all individual checkboxes get checked, auto-check Select All
                // If any gets unchecked, uncheck Select All
                $(document).on('change', '.dept-checkbox', function() {
                    let total = $('.dept-checkbox').length;
                    let checked = $('.dept-checkbox:checked').length;
                    $('#selectAllDept').prop('checked', total === checked);
                });
            });
        </script>
    @endpush
</x-app-layout>