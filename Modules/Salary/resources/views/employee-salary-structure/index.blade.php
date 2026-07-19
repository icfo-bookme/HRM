<x-app-layout>

    <div class="p-4">
        <div
            class="flex flex-col md:flex-row md:items-end gap-4 mb-5 bg-white p-4 rounded-xl border border-gray-200 shadow-sm">

            {{-- Employee Filter --}}
            <div class="flex flex-col w-full md:w-1/3">
                <x-form-select label="Employee" id="filter_employee" class="dt-filter-employeeSalaryTable">
                    <option value="">All Employees</option>
                    @foreach ($employees as $employee)
                        <option value="{{ $employee->id }}">{{ $employee->employee_code }} - {{ $employee->personalInfo?->full_name ?? 'N/A' }}</option>
                    @endforeach
                </x-form-select>
            </div>

            {{-- Component Filter --}}
            <div class="flex flex-col w-full md:w-1/3">
                <x-form-select label="Salary Component" id="filter_component" class="dt-filter-employeeSalaryTable">
                    <option value="">All Components</option>
                    @foreach ($components as $component)
                        <option value="{{ $component->id }}">{{ $component->name }} ({{ $component->type }})</option>
                    @endforeach
                </x-form-select>
            </div>

            {{-- Reset Button --}}
            <div class="w-full md:w-auto flex items-end">
                <button id="resetFilters"
                    class="px-4 py-2 text-sm font-medium text-white bg-gray-700 hover:bg-gray-800
                   rounded-lg transition active:scale-95">
                    Reset
                </button>
            </div>

        </div>

        {{-- REUSABLE DATA-TABLE COMPONENT --}}
        <x-data-table id="employeeSalaryTable" title="Employee Salary Structure" icon="fa-solid fa-file-invoice-dollar"
            buttonId="btnAddEmployeeSalary" buttonText="Add Salary Structure" :columns="[ 'Employee', 'Component', 'Amount', 'Effective From', 'Effective To', 'Created At', 'Action']" :ajaxUrl="route('employee-salary-structure.dataTable')"
            :dtColumns="[
              
                ['data' => 'employee_id'],
                ['data' => 'component_id'],
                ['data' => 'amount'],
                ['data' => 'effective_from'],
                ['data' => 'effective_to'],
                ['data' => 'created_at'],
                ['data' => 'action', 'orderable' => false, 'searchable' => false],
            ]" :filters="[
                'employee_id' => '#filter_employee',
                'component_id' => '#filter_component',
            ]" :exportButtons="true" />
    </div>

    {{-- DRAWER COMPONENT --}}
    <x-drawer id="employee-salary-drawer" overlayId="employee-salary-overlay" title="Add Salary Structure"
        submitOnClick="saveForm()">
        <form id="employeeSalaryForm">
            <input type="hidden" name="id" id="structure_id">

            {{-- Employee Select --}}
            <div class="mb-4 animate-fade" style="animation-delay: 50ms;">
                <x-form-select label="Employee" name="employee_id" id="employee_id" placeholder="Select Employee" required>
                    <option value="">-- Select Employee --</option>
                    @foreach ($employees as $employee)
                        <option value="{{ $employee->id }}">{{ $employee->employee_code }} - {{ $employee->personalInfo?->full_name ?? 'N/A' }}</option>
                    @endforeach
                </x-form-select>
            </div>

            {{-- Component Select --}}
            <div class="mb-4 animate-fade" style="animation-delay: 100ms;">
                <x-form-select label="Salary Component" name="component_id" id="component_id" placeholder="Select Component" required>
                    <option value="">-- Select Component --</option>
                    @foreach ($components as $component)
                        <option value="{{ $component->id }}">{{ $component->name }} ({{ $component->type }})</option>
                    @endforeach
                </x-form-select>
            </div>

            {{-- Amount & Is Percentage --}}
            <div class="grid grid-cols-2 gap-3 mb-4 animate-fade" style="animation-delay: 150ms;">
                <div class="flex flex-col">
                    <x-form-input label="Amount" name="amount" id="amount" type="number" step="0.0001" placeholder="0.0000" required />
                </div>
                <div class="flex items-end pb-2">
                    <div class="space-y-2 bg-slate-50 p-3 rounded-md border border-slate-100 w-full">
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" id="is_percentage" name="is_percentage" value="1"
                                class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500">
                            <span class="text-sm font-semibold text-slate-700">Is Percentage</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Effective From & To --}}
            <div class="grid grid-cols-2 gap-3 mb-4 animate-fade" style="animation-delay: 200ms;">
                <x-form-input label="Effective From" name="effective_from" id="effective_from" type="date" required />
                <x-form-input label="Effective To" name="effective_to" id="effective_to" type="date" />
            </div>
        </form>
    </x-drawer>

    @push('scripts')
        <script>
            let isSaving = false;

            function openEmployeeSalaryDrawer(mode = 'add') {
                if (mode === 'edit') {
                    $('#drawerTitle').text('Update Salary Structure');
                    $('#drawerButtonText').text('Update Structure');
                } else {
                    resetForm();
                    $('#drawerTitle').text('Add Salary Structure');
                    $('#drawerButtonText').text('Save Structure');
                }
                openGlobalDrawer('employee-salary-drawer', 'employee-salary-overlay');
            }

            function resetForm() {
                $('#employeeSalaryForm')[0].reset();
                $('#structure_id').val('');
                $('#is_percentage').prop('checked', false);
            }

            function employeeSalaryStructureEdit(id) {
                Swal.fire({
                    title: 'Loading...',
                    text: 'Fetching salary structure details',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                resetForm();
                let fetchUrl = "{{ route('employee-salary-structure.show', ':id') }}".replace(':id', id);

                $.get(fetchUrl, function(res) {
                    Swal.close();
                    if (res.status) {
                        let s = res.structure;
                        $('#structure_id').val(s.id);
                        $('select[name="employee_id"]').val(s.employee_id);
                        $('select[name="component_id"]').val(s.component_id);
                        $('input[name="amount"]').val(s.amount);
                        $('input[name="effective_from"]').val(s.effective_from);
                        $('input[name="effective_to"]').val(s.effective_to || '');
                        $('#is_percentage').prop('checked', s.is_percentage == 1);

                        openEmployeeSalaryDrawer('edit');
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

                let id = $('#structure_id').val();
                let url = id ? "{{ route('employee-salary-structure.update', ':id') }}".replace(':id', id) :
                    "{{ route('employee-salary-structure.store') }}";

                let formData = $('#employeeSalaryForm').serialize();
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
                                style: {
                                    background: "linear-gradient(135deg, #16a34a, #4ade80)"
                                },
                            }).showToast();
                            closeGlobalDrawer('employee-salary-drawer', 'employee-salary-overlay');
                            $('#employeeSalaryTable').DataTable().ajax.reload(null, false);
                        } else {
                            Swal.fire('Error', res.message || 'Something went wrong', 'error');
                            $('#drawerButtonText').text(id ? 'Update Structure' : 'Save Structure');
                        }
                    },
                    error: function(xhr) {
                        isSaving = false;
                        $('#saveBtn').prop('disabled', false).removeClass('opacity-70 cursor-not-allowed');
                        $('#drawerButtonText').text(id ? 'Update Structure' : 'Save Structure');

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

            function employeeSalaryStructureDelete(id) {
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
                        let deleteUrl = "{{ route('employee-salary-structure.destroy', ':id') }}".replace(':id', id);

                        $.post(deleteUrl, {
                            _method: 'DELETE',
                        }, function(res) {
                            if (res.status === 'success' || res.status === true) {
                                Toastify({
                                    text: res.message || 'Deleted successfully',
                                    duration: 3000,
                                    gravity: "bottom",
                                    position: "right",
                                    style: {
                                        background: "linear-gradient(135deg, #dc2626, #f87171)"
                                    },
                                }).showToast();
                                $('#employeeSalaryTable').DataTable().ajax.reload(null, false);
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
                $('#filter_employee').val('');
                $('#filter_component').val('');
                $('.dt-filter-employeeSalaryTable').trigger('change');
            });

            $(document).ready(function() {
                $(document).on('click', '#btnAddEmployeeSalary', function(e) {
                    e.preventDefault();
                    openEmployeeSalaryDrawer('add');
                });
            });
        </script>
    @endpush
</x-app-layout>