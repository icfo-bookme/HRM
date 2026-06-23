<x-app-layout>

    <div class="p-4">
        {{-- FILTERS --}}
        <div
            class="flex flex-col md:flex-row md:items-end gap-4 mb-5 bg-white p-4 rounded-xl border border-gray-200 shadow-sm">

            <div class="flex flex-col w-full md:w-1/3">
                <x-form-select label="Company" id="filter_company" class="dt-filter-fiscalYearTable">
                    <option value="">All Companies</option>
                    @foreach ($companies as $company)
                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                    @endforeach
                </x-form-select>
            </div>

            <div class="flex flex-col w-full md:w-1/3">
                <x-form-select label="Current" id="filter_current" class="dt-filter-fiscalYearTable">
                    <option value="">All</option>
                    <option value="1">Current</option>
                    <option value="0">Not Current</option>
                </x-form-select>
            </div>

            <div class="w-full md:w-auto flex items-end">
                <button id="resetFilters"
                    class="px-4 py-2 text-sm font-medium text-white bg-gray-700 hover:bg-gray-800 rounded-lg transition active:scale-95">
                    Reset
                </button>
            </div>

        </div>

        {{-- DATA-TABLE --}}
        <x-data-table id="fiscalYearTable" title="Fiscal Years Management" icon="fa-solid fa-calendar-alt"
            buttonId="btnAddFiscalYear" buttonText="Add New Fiscal Year"
            :columns="['Company', 'Label', 'Start Date', 'End Date', 'Current', 'Locked', 'Action']"
            :ajaxUrl="route('fiscal-years.dataTable')"
            :orderColumn="1"
            :orderDirection="'desc'"
            :dtColumns="[
                ['data' => 'company', 'width' => '150px', 'orderable' => false],
                ['data' => 'label'],
                ['data' => 'start_date', 'width' => '110px'],
                ['data' => 'end_date', 'width' => '110px'],
                ['data' => 'is_current', 'width' => '80px'],
                ['data' => 'locked', 'width' => '80px'],
                ['data' => 'action', 'orderable' => false, 'searchable' => false, 'width' => '80px'],
            ]"
            :filters="[
                'company_id' => '#filter_company',
                'is_current' => '#filter_current',
            ]"
            :exportButtons="true" />
    </div>

    {{-- DRAWER --}}
    <x-drawer id="fiscal-year-drawer" overlayId="fiscal-year-overlay" title="Add New Fiscal Year" submitOnClick="saveForm()">
        <form id="fiscalYearForm">
            <input type="hidden" name="id" id="fy_id">

            {{-- Company --}}
            <div class="mb-4 animate-fade" style="animation-delay: 50ms;">
                <x-form-select label="Company" name="company_id" id="company_id" placeholder="Select Company" required>
                    <option value="">-- Select Company --</option>
                    @foreach ($companies as $company)
                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                    @endforeach
                </x-form-select>
            </div>

            {{-- Label --}}
            <div class="mb-4 animate-fade" style="animation-delay: 100ms;">
                <x-form-input label="Fiscal Year Label" name="label" id="label" placeholder="e.g. FY 2025-26" required />
            </div>

            {{-- Start & End Date --}}
            <div class="grid grid-cols-2 gap-3 mb-4 animate-fade" style="animation-delay: 150ms;">
                <x-form-input label="Start Date" name="start_date" id="start_date" type="date" required />
                <x-form-input label="End Date" name="end_date" id="end_date" type="date" required />
            </div>

            {{-- Checkboxes --}}
            <div class="space-y-2 bg-slate-50 p-3 rounded-md border border-slate-100 animate-fade"
                style="animation-delay: 200ms;">
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox" id="is_current" name="is_current" value="1"
                        class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500">
                    <span class="text-sm font-semibold text-slate-700">Set as Current Fiscal Year</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox" id="locked" name="locked" value="1"
                        class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500">
                    <span class="text-sm font-semibold text-slate-700">Locked (No further changes)</span>
                </label>
            </div>
        </form>
    </x-drawer>

    @push('scripts')
        <script>
            let isSaving = false;

            function openFiscalYearDrawer(mode = 'add') {
                if (mode === 'edit') {
                    $('#drawerTitle').text('Update Fiscal Year');
                    $('#drawerButtonText').text('Update Fiscal Year');
                } else {
                    resetForm();
                    $('#drawerTitle').text('Add New Fiscal Year');
                    $('#drawerButtonText').text('Save Fiscal Year');
                }
                openGlobalDrawer('fiscal-year-drawer', 'fiscal-year-overlay');
            }

            function resetForm() {
                $('#fiscalYearForm')[0].reset();
                $('#fy_id').val('');
                $('#is_current').prop('checked', false);
                $('#locked').prop('checked', false);
            }

            function fiscalYearEdit(id) {
                Swal.fire({
                    title: 'Loading...',
                    text: 'Fetching fiscal year details',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                resetForm();
                let fetchUrl = "{{ route('fiscal-years.show', ':id') }}".replace(':id', id);

                $.get(fetchUrl, function(res) {
                    Swal.close();
                    if (res.status === 'success') {
                        let d = res.data;
                        $('#fy_id').val(d.id);
                        $('#company_id').val(d.company_id);
                        $('#label').val(d.label);
                        $('#start_date').val(d.start_date ? d.start_date.substring(0, 10) : '');
                        $('#end_date').val(d.end_date ? d.end_date.substring(0, 10) : '');
                        $('#is_current').prop('checked', d.is_current == 1);
                        $('#locked').prop('checked', d.locked == 1);
                        openFiscalYearDrawer('edit');
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

                let id = $('#fy_id').val();
                let url = id ? "{{ route('fiscal-years.update', ':id') }}".replace(':id', id) :
                    "{{ route('fiscal-years.store') }}";

                let formData = $('#fiscalYearForm').serialize();
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
                                style: { background: "linear-gradient(135deg, #16a34a, #4ade80)" },
                            }).showToast();
                            closeGlobalDrawer('fiscal-year-drawer', 'fiscal-year-overlay');
                            $('#fiscalYearTable').DataTable().ajax.reload(null, false);
                        } else {
                            Swal.fire('Error', res.message || 'Something went wrong', 'error');
                            $('#drawerButtonText').text(id ? 'Update Fiscal Year' : 'Save Fiscal Year');
                        }
                    },
                    error: function(xhr) {
                        isSaving = false;
                        $('#saveBtn').prop('disabled', false).removeClass('opacity-70 cursor-not-allowed');
                        $('#drawerButtonText').text(id ? 'Update Fiscal Year' : 'Save Fiscal Year');

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

            function fiscalYearDelete(id) {
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
                        let deleteUrl = "{{ route('fiscal-years.destroy', ':id') }}".replace(':id', id);
                        $.post(deleteUrl, { _method: 'DELETE' }, function(res) {
                            if (res.status === 'success' || res.status === true) {
                                Toastify({
                                    text: res.message || 'Deleted successfully',
                                    duration: 3000,
                                    gravity: "bottom",
                                    position: "right",
                                    style: { background: "linear-gradient(135deg, #dc2626, #f87171)" },
                                }).showToast();
                                $('#fiscalYearTable').DataTable().ajax.reload(null, false);
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
                $('#filter_current').val('');
                $('.dt-filter-fiscalYearTable').trigger('change');
            });

            $(document).ready(function() {
                $(document).on('click', '#btnAddFiscalYear', function(e) {
                    e.preventDefault();
                    openFiscalYearDrawer('add');
                });
            });
        </script>
    @endpush
</x-app-layout>