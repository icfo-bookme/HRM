<x-app-layout>
    <div class="p-4">
        <div class="flex flex-col md:flex-row md:items-end gap-4 mb-5 bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
           
            <div class="flex flex-col w-full md:w-1/3">
                <x-form-select label="Status" id="filter_status" class="dt-filter-shiftTable">
                    <option value="">All Status</option>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </x-form-select>
            </div>

            <div class="w-full md:w-auto flex items-end">
                <button id="resetFilters"
                    class="px-4 py-2 text-sm font-medium text-white bg-gray-700 hover:bg-gray-800 rounded-lg transition active:scale-95">
                    Reset
                </button>
            </div>
        </div>

        <x-data-table id="shiftTable" title="Shift Management" icon="fa-solid fa-clock"
            buttonId="btnAddShift" buttonText="Add New Shift"
            :columns="[ 'Shift Name', 'Start', 'End', 'Break', 'Work Hours', 'Night', 'Flexible', 'Status', 'Created At', 'Action']"
            :ajaxUrl="route('shifts.dataTable')"
            :dtColumns="[
           
                ['data' => 'name'],
                ['data' => 'start_time'],
                ['data' => 'end_time'],
                ['data' => 'break_minutes'],
                ['data' => 'work_hours'],
                ['data' => 'is_night_shift'],
                ['data' => 'is_flexible'],
                ['data' => 'is_active'],
                ['data' => 'created_at'],
                ['data' => 'action', 'orderable' => false, 'searchable' => false],
            ]"
            :filters="[
                'company_id' => '#filter_company',
                'is_active' => '#filter_status',
            ]"
            :exportButtons="true" />
    </div>

    <x-drawer id="shift-drawer" overlayId="shift-overlay" title="Add New Shift" submitOnClick="saveForm()">
        <form id="shiftForm">
            <input type="hidden" name="id" id="shift_id">

            <div class="grid grid-cols-1 gap-3 mb-4 animate-fade" style="animation-delay: 100ms;">
                <x-form-input label="Name" name="name" id="name" placeholder="Shift Name" />
            </div>

            <div class="grid grid-cols-2 gap-3 mb-4 animate-fade" style="animation-delay: 150ms;">
                <x-form-input label="Start Time" name="start_time" id="start_time" type="time" />
                <x-form-input label="End Time" name="end_time" id="end_time" type="time" />
            </div>

            <div class="grid grid-cols-2 gap-3 mb-4 animate-fade" style="animation-delay: 200ms;">
                <x-form-input label="Break Minutes" name="break_minutes" id="break_minutes" type="number" placeholder="60" value="60" />
                <x-form-input label="Work Hours" name="work_hours" id="work_hours" type="number" step="0.1" placeholder="8.0" value="8.0" />
            </div>

            <div class="grid grid-cols-2 gap-3 mb-4 animate-fade" style="animation-delay: 250ms;">
                <x-form-input label="Grace In Minutes" name="grace_in_min" id="grace_in_min" type="number" placeholder="10" value="10" />
                <x-form-input label="Grace Out Minutes" name="grace_out_min" id="grace_out_min" type="number" placeholder="10" value="10" />
            </div>

            <div class="mb-4 animate-fade" style="animation-delay: 300ms;">
                <x-form-textarea label="Metadata" name="metadata" id="metadata" placeholder='Optional JSON, e.g. {"color":"blue"}' rows="3" />
            </div>

            <div class="space-y-2 bg-slate-50 p-3 rounded-md border border-slate-100 animate-fade" style="animation-delay: 350ms;">
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox" id="is_night_shift" name="is_night_shift" value="1"
                        class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500">
                    <span class="text-sm font-semibold text-slate-700">Night Shift</span>
                </label>

                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox" id="is_flexible" name="is_flexible" value="1"
                        class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500">
                    <span class="text-sm font-semibold text-slate-700">Flexible</span>
                </label>

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

            function normalizeTime(value) {
                return value ? value.substring(0, 5) : '';
            }

            function openShiftDrawer(mode = 'add') {
                if (mode === 'edit') {
                    $('#drawerTitle').text('Update Shift');
                    $('#drawerButtonText').text('Update Shift');
                } else {
                    resetForm();
                    $('#drawerTitle').text('Add New Shift');
                    $('#drawerButtonText').text('Save Shift');
                }

                openGlobalDrawer('shift-drawer', 'shift-overlay');
            }

            function resetForm() {
                $('#shiftForm')[0].reset();
                $('#shift_id').val('');
                $('#break_minutes').val(60);
                $('#grace_in_min').val(10);
                $('#grace_out_min').val(10);
                $('#work_hours').val('8.0');
                $('#is_night_shift').prop('checked', false);
                $('#is_flexible').prop('checked', false);
                $('#is_active').prop('checked', true);
            }

            function shiftEdit(id) {
                Swal.fire({
                    title: 'Loading...',
                    text: 'Fetching shift details',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                resetForm();

                let fetchUrl = "{{ route('shifts.show', ':id') }}".replace(':id', id);

                $.get(fetchUrl, function(res) {
                    Swal.close();

                    if (res.status === 'success') {
                        let d = res.shift;

                        $('#shift_id').val(d.id);
                        $('#company_id').val(d.company_id);
                        $('#code').val(d.code);
                        $('#name').val(d.name);
                        $('#start_time').val(normalizeTime(d.start_time));
                        $('#end_time').val(normalizeTime(d.end_time));
                        $('#break_minutes').val(d.break_minutes);
                        $('#grace_in_min').val(d.grace_in_min);
                        $('#grace_out_min').val(d.grace_out_min);
                        $('#work_hours').val(d.work_hours);
                        $('#metadata').val(d.metadata ? JSON.stringify(d.metadata) : '');
                        $('#is_night_shift').prop('checked', parseInt(d.is_night_shift) === 1);
                        $('#is_flexible').prop('checked', parseInt(d.is_flexible) === 1);
                        $('#is_active').prop('checked', parseInt(d.is_active) === 1);

                        openShiftDrawer('edit');
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

                let id = $('#shift_id').val();
                let url = id ? "{{ route('shifts.update', ':id') }}".replace(':id', id) : "{{ route('shifts.store') }}";
                let formData = $('#shiftForm').serialize();

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
                                gravity: 'bottom',
                                position: 'right',
                                style: {
                                    background: 'linear-gradient(135deg, #16a34a, #4ade80)'
                                },
                            }).showToast();

                            closeGlobalDrawer('shift-drawer', 'shift-overlay');
                            $('#shiftTable').DataTable().ajax.reload(null, false);
                        } else {
                            Swal.fire('Error', res.message || 'Something went wrong', 'error');
                            $('#drawerButtonText').text(id ? 'Update Shift' : 'Save Shift');
                        }
                    },
                    error: function(xhr) {
                        isSaving = false;
                        $('#saveBtn').prop('disabled', false).removeClass('opacity-70 cursor-not-allowed');
                        $('#drawerButtonText').text(id ? 'Update Shift' : 'Save Shift');

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

            function shiftDelete(id) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'This action cannot be undone!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#4b5563',
                    confirmButtonText: 'Yes, delete it!'
                }).then((r) => {
                    if (r.isConfirmed) {
                        let deleteUrl = "{{ route('shifts.destroy', ':id') }}".replace(':id', id);

                        $.post(deleteUrl, {
                            _method: 'DELETE'
                        }, function(res) {
                            if (res.status === 'success' || res.status === true) {
                                Swal.fire('Deleted!', res.message || 'Shift has been deleted.', 'success');
                                $('#shiftTable').DataTable().ajax.reload(null, false);
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
                $('.dt-filter-shiftTable').trigger('change');
            });

            $(document).ready(function() {
                $(document).on('click', '#btnAddShift', function(e) {
                    e.preventDefault();
                    openShiftDrawer('add');
                });
            });
        </script>
    @endpush
</x-app-layout>
