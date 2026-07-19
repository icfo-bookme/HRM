<x-app-layout>
    <div class="p-4">
        {{-- REUSABLE DATA-TABLE COMPONENT --}}
        <x-data-table id="holidayTable" title="Holiday Management" icon="fa-solid fa-calendar-day" buttonId="btnAddHoliday"
            buttonText="Add New Holiday" :columns="['Holiday Name', 'Holiday Date', 'End Date', 'Total Days', 'Type', 'Recurring', 'Created At', 'Action']"
            :ajaxUrl="route('holidays.dataTable')" :dtColumns="[
                ['data' => 'name'],
                ['data' => 'holiday_date'],
                ['data' => 'end_date'],
                ['data' => 'total_days'],
                ['data' => 'holiday_type'],
                ['data' => 'is_recurring'],
                ['data' => 'created_at'],
                ['data' => 'action', 'orderable' => false, 'searchable' => false],
            ]" />
    </div>

    {{-- DRAWER COMPONENT --}}
    <x-drawer id="holiday-drawer" overlayId="holiday-overlay" title="Add New Holiday" submitOnClick="saveForm()">
        <form id="holidayForm">
            <input type="hidden" id="holiday_id" name="id">

            {{-- Holiday Name --}}
            <div class="mb-4 animate-fade" style="animation-delay: 50ms;">
                <x-form-input label="Holiday Name" name="name" placeholder="Enter holiday name" required />
            </div>

            {{-- Holiday Date & End Date --}}
            <div class="grid grid-cols-2 gap-3 mb-4 animate-fade" style="animation-delay: 100ms;">
                <x-form-input label="Holiday Date" name="holiday_date" type="date" required />
                <x-form-input label="End Date" name="end_date" type="date" />
            </div>

            {{-- Holiday Type & Applicable To --}}
            <div class="grid grid-cols-2 gap-3 mb-4 animate-fade" style="animation-delay: 150ms;">
                <x-form-select label="Holiday Type" name="holiday_type" placeholder="Select Type" required>
                    <option value="Public">Public</option>
                    <option value="Government">Government</option>
                    <option value="Company">Company</option>
                    <option value="Optional">Optional</option>
                    <option value="Religious">Religious</option>
                    <option value="Festival">Festival</option>
                </x-form-select>
                <x-form-select label="Applicable To" name="applicable_to" placeholder="Select Applicable To" required>
                    <option value="All">All</option>
                    <option value="Specific">Specific</option>
                    <option value="Branch">Branch</option>
                    <option value="Department">Department</option>
                </x-form-select>
            </div>

            {{-- Description Textarea --}}
            <div class="mb-4 animate-fade" style="animation-delay: 200ms;">
                <x-form-textarea label="Description" name="description" placeholder="Enter holiday description" rows="3" />
            </div>

            {{-- Recurring Checkboxes --}}
            <div class="space-y-2 bg-slate-50 p-3 rounded-md border border-slate-100 animate-fade"
                style="animation-delay: 250ms;">

                {{-- Is Recurring Checkbox --}}
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="hidden" name="is_recurring" value="0">
                    <input type="checkbox" id="is_recurring" name="is_recurring" value="1"
                        class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500">
                    <span class="text-sm font-semibold text-slate-700">Is Recurring</span>
                </label>

                {{-- Yearly Recurring Checkbox --}}
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="hidden" name="yearly_recurring" value="0">
                    <input type="checkbox" id="yearly_recurring" name="yearly_recurring" value="1"
                        class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500">
                    <span class="text-sm font-semibold text-slate-700">Yearly Recurring</span>
                </label>
            </div>
        </form>
    </x-drawer>

    @push('scripts')
        <script>
            let isSaving = false;
            const csrfToken = '{{ csrf_token() }}';

            function openHolidayDrawer(mode = 'add') {
                if (mode === 'edit') {
                    $('#drawerTitle').text('Update Holiday');
                    $('#drawerButtonText').text('Update Holiday');
                } else {
                    resetForm();
                    $('#drawerTitle').text('Add New Holiday');
                    $('#drawerButtonText').text('Save Holiday');
                }
                openGlobalDrawer('holiday-drawer', 'holiday-overlay');
            }

            function resetForm() {
                $('#holidayForm')[0].reset();
                $('#holiday_id').val('');
                $('#is_recurring').prop('checked', false);
                $('#yearly_recurring').prop('checked', false);
            }

            function holidayEdit(id) {
                Swal.fire({
                    title: 'Loading...',
                    text: 'Fetching holiday details',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                resetForm();

                let fetchUrl = "{{ route('holidays.show', ':id') }}".replace(':id', id);

                $.get(fetchUrl, function(res) {
                    if (res.status) {
                        let d = res.data;
                        $('#holiday_id').val(d.id);
                        $('#name').val(d.name);
                        $('#holiday_date').val(d.holiday_date);
                        $('#end_date').val(d.end_date);
                        $('#holiday_type').val(d.holiday_type);
                        $('#applicable_to').val(d.applicable_to);
                        $('#description').val(d.description);
                        $('#is_recurring').prop('checked', d.is_recurring == 1);
                        $('#yearly_recurring').prop('checked', d.yearly_recurring == 1);

                        Swal.close();
                        openHolidayDrawer('edit');
                    } else {
                        Swal.fire('Error', res.message || 'Failed to fetch data.', 'error');
                    }
                }).fail(function() {
                    Swal.fire('Error', 'Server communication error.', 'error');
                });
            }

            function saveForm() {
                if (isSaving) return;

                let id = $('#holiday_id').val();
                let url = id ? "{{ route('holidays.update', ':id') }}".replace(':id', id) : "{{ route('holidays.store') }}";
                let formData = $('#holidayForm').serialize();

                if (id) formData += '&_method=PUT';
                formData += `&_token=${csrfToken}`;

                isSaving = true;
                $('#drawerButtonText').text('Saving...');
                $('#saveBtn').prop('disabled', true).addClass('opacity-70 cursor-not-allowed');

                Swal.fire({
                    title: 'Confirm',
                    text: id ? "Update holiday?" : "Create holiday?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#16a34a',
                    cancelButtonColor: '#d33'
                }).then((result) => {
                    if (!result.isConfirmed) {
                        isSaving = false;
                        $('#drawerButtonText').text(id ? 'Update Holiday' : 'Save Holiday');
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
                                closeGlobalDrawer('holiday-drawer', 'holiday-overlay');
                                $('#holidayTable').DataTable().ajax.reload(null, false);
                            } else {
                                Swal.fire('Error', res.message, 'error');
                                $('#drawerButtonText').text(id ? 'Update Holiday' : 'Save Holiday');
                            }
                        },
                        error: function(xhr) {
                            isSaving = false;
                            $('#saveBtn').prop('disabled', false).removeClass(
                                'opacity-70 cursor-not-allowed');
                            $('#drawerButtonText').text(id ? 'Update Holiday' : 'Save Holiday');
                            let errorMsg = xhr.responseJSON?.message || 'Server error occurred';
                            Swal.fire('Error', errorMsg, 'error');
                        }
                    });
                });
            }

            function holidayDelete(id) {
                Swal.fire({
                    title: 'Delete?',
                    text: "This cannot be undone",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#4b5563'
                }).then((r) => {
                    if (r.isConfirmed) {
                        let deleteUrl = "{{ route('holidays.destroy', ':id') }}".replace(':id', id);
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
                                $('#holidayTable').DataTable().ajax.reload(null, false);
                            } else {
                                Swal.fire('Error', res.message, 'error');
                            }
                        }).fail(function() {
                            Swal.fire('Error', 'Failed to delete holiday.', 'error');
                        });
                    }
                });
            }

            $(document).ready(function() {
                $(document).on('click', '#btnAddHoliday', function(e) {
                    e.preventDefault();
                    openHolidayDrawer('add');
                });
            });
        </script>
    @endpush
</x-app-layout>