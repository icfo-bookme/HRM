<x-app-layout>

    <div class="p-4">
        <div
            class="flex flex-col md:flex-row md:items-end gap-4 mb-5 bg-white p-4 rounded-xl border border-gray-200 shadow-sm">

            {{-- Type Filter --}}
            <div class="flex flex-col w-full md:w-1/4">
                <x-form-select label="Component Type" id="filter_type" class="dt-filter-salaryComponentTable">
                    <option value="">All Types</option>
                    <option value="Earning">Earning</option>
                    <option value="Deduction">Deduction</option>
                    <option value="Reimbursement">Reimbursement</option>
                    <option value="Bonus">Bonus</option>
                </x-form-select>
            </div>

            {{-- Status Filter --}}
            <div class="flex flex-col w-full md:w-1/4">
                <x-form-select label="Status" id="filter_status" class="dt-filter-salaryComponentTable">
                    <option value="">All Status</option>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
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
        <x-data-table id="salaryComponentTable" title="Salary Components Management" icon="fa-solid fa-coins"
            buttonId="btnAddSalaryComponent" buttonText="Add New Component" :columns="[ 'Name', 'Type', 'Category', 'Calculation Type', 'Default Value', 'Taxable', 'PF Basis', 'Status', 'In Slip', 'Created At', 'Action']" :ajaxUrl="route('salary-components.dataTable')"
            :dtColumns="[
               
                ['data' => 'name'],
                ['data' => 'type'],
                ['data' => 'category'],
                ['data' => 'calculation_type'],
                ['data' => 'default_value'],
                ['data' => 'is_taxable'],
                ['data' => 'is_pf_basis'],
                ['data' => 'is_active'],
                ['data' => 'show_in_slip'],
                ['data' => 'created_at'],
                ['data' => 'action', 'orderable' => false, 'searchable' => false],
            ]" :filters="[
                'type' => '#filter_type',
                'is_active' => '#filter_status',
            ]" :exportButtons="true" />
    </div>

    {{-- DRAWER COMPONENT --}}
    <x-drawer id="salary-component-drawer" overlayId="salary-component-overlay" title="Add New Salary Component"
        submitOnClick="saveForm()">
        <form id="salaryComponentForm">
            <input type="hidden" name="id" id="component_id">

            {{-- Component Name --}}
            <div class="mb-4 animate-fade" style="animation-delay: 50ms;">
                <x-form-input label="Component Name" name="name" id="name" placeholder="Enter component name" required />
            </div>

            {{-- Type & Category --}}
            <div class="grid grid-cols-2 gap-3 mb-4 animate-fade" style="animation-delay: 100ms;">
                <div class="flex flex-col">
                    <x-form-select label="Type" name="type" id="type" placeholder="Select Type" required>
                        <option value="">-- Select Type --</option>
                        <option value="Earning">Earning</option>
                        <option value="Deduction">Deduction</option>
                        <option value="Reimbursement">Reimbursement</option>
                        <option value="Bonus">Bonus</option>
                    </x-form-select>
                </div>
                <div class="flex flex-col">
                    <x-form-select label="Category" name="category" id="category" placeholder="Select Category">
                        <option value="">-- Select Category --</option>
                        <option value="Basic">Basic</option>
                        <option value="Allowance">Allowance</option>
                        <option value="Bonus">Bonus</option>
                        <option value="PF">PF</option>
                        <option value="Tax">Tax</option>
                        <option value="Insurance">Insurance</option>
                        <option value="Loan">Loan</option>
                        <option value="Other">Other</option>
                    </x-form-select>
                </div>
            </div>

            {{-- Calculation Type & Default Value --}}
            <div class="grid grid-cols-2 gap-3 mb-4 animate-fade" style="animation-delay: 150ms;">
                <div class="flex flex-col">
                    <x-form-select label="Calculation Type" name="calculation_type" id="calculation_type" placeholder="Select Calculation Type">
                        <option value="">-- Select --</option>
                        <option value="Fixed">Fixed</option>
                        <option value="Percentage of Basic">Percentage of Basic</option>
                        <option value="Percentage of Gross">Percentage of Gross</option>
                        <option value="Formula">Formula</option>
                        <option value="Custom">Custom</option>
                    </x-form-select>
                </div>
                <div class="flex flex-col">
                    <x-form-input label="Default Value" name="default_value" id="default_value" type="number" step="0.0001" placeholder="0.0000" value="0" />
                </div>
            </div>

            {{-- Formula Expression --}}
            <div class="mb-4 animate-fade" style="animation-delay: 200ms;">
                <x-form-textarea label="Formula Expression" name="formula_expression" id="formula_expression"
                    placeholder="Enter formula for dynamic calculation (e.g., (basic * 0.5) + allowance)" rows="2" />
            </div>

            {{-- Display Order --}}
            <div class="mb-4 animate-fade" style="animation-delay: 250ms;">
                <x-form-input label="Display Order" name="display_order" id="display_order" type="number" placeholder="0" value="0" />
            </div>

            {{-- Checkboxes --}}
            <div class="grid grid-cols-2 gap-3 mb-4 animate-fade" style="animation-delay: 300ms;">
                {{-- Is Taxable --}}
                <div class="space-y-2 bg-slate-50 p-3 rounded-md border border-slate-100">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" id="is_taxable" name="is_taxable" value="1"
                            class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500">
                        <span class="text-sm font-semibold text-slate-700">Is Taxable</span>
                    </label>
                </div>

                {{-- Is PF Basis --}}
                <div class="space-y-2 bg-slate-50 p-3 rounded-md border border-slate-100">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" id="is_pf_basis" name="is_pf_basis" value="1"
                            class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500">
                        <span class="text-sm font-semibold text-slate-700">Is PF Basis</span>
                    </label>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 mb-4 animate-fade" style="animation-delay: 350ms;">
                {{-- Show in Slip --}}
                <div class="space-y-2 bg-slate-50 p-3 rounded-md border border-slate-100">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" id="show_in_slip" name="show_in_slip" value="1" checked
                            class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500">
                        <span class="text-sm font-semibold text-slate-700">Show in Payslip</span>
                    </label>
                </div>

                {{-- Active --}}
                <div class="space-y-2 bg-slate-50 p-3 rounded-md border border-slate-100">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" id="is_active" name="is_active" value="1" checked
                            class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500">
                        <span class="text-sm font-semibold text-slate-700">Active</span>
                    </label>
                </div>
            </div>
        </form>
    </x-drawer>

    @push('scripts')
        <script>
            let isSaving = false;

            function openSalaryComponentDrawer(mode = 'add') {
                if (mode === 'edit') {
                    $('#drawerTitle').text('Update Salary Component');
                    $('#drawerButtonText').text('Update Component');
                } else {
                    resetForm();
                    $('#drawerTitle').text('Add New Salary Component');
                    $('#drawerButtonText').text('Save Component');
                }
                openGlobalDrawer('salary-component-drawer', 'salary-component-overlay');
            }

            function resetForm() {
                $('#salaryComponentForm')[0].reset();
                $('#component_id').val('');
                $('#is_active').prop('checked', true);
                $('#show_in_slip').prop('checked', true);
                $('#default_value').val(0);
                $('#display_order').val(0);
            }

            function salaryComponentEdit(id) {
                Swal.fire({
                    title: 'Loading...',
                    text: 'Fetching salary component details',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                resetForm();
                let fetchUrl = "{{ route('salary-components.show', ':id') }}".replace(':id', id);

                $.get(fetchUrl, function(res) {
                    Swal.close();
                    if (res.status) {
                        let c = res.salary_component;
                        $('#component_id').val(c.id);
                        $('input[name="name"]').val(c.name);
                        $('select[name="type"]').val(c.type);
                        $('select[name="category"]').val(c.category || '');
                        $('select[name="calculation_type"]').val(c.calculation_type || '');
                        $('input[name="default_value"]').val(c.default_value);
                        $('textarea[name="formula_expression"]').val(c.formula_expression);
                        $('input[name="display_order"]').val(c.display_order || 0);

                        $('#is_taxable').prop('checked', c.is_taxable == 1);
                        $('#is_pf_basis').prop('checked', c.is_pf_basis == 1);
                        $('#is_active').prop('checked', c.is_active == 1);
                        $('#show_in_slip').prop('checked', c.show_in_slip == 1);

                        openSalaryComponentDrawer('edit');
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

                let id = $('#component_id').val();
                let url = id ? "{{ route('salary-components.update', ':id') }}".replace(':id', id) :
                    "{{ route('salary-components.store') }}";

                let formData = $('#salaryComponentForm').serialize();
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
                            closeGlobalDrawer('salary-component-drawer', 'salary-component-overlay');
                            $('#salaryComponentTable').DataTable().ajax.reload(null, false);
                        } else {
                            Swal.fire('Error', res.message || 'Something went wrong', 'error');
                            $('#drawerButtonText').text(id ? 'Update Component' : 'Save Component');
                        }
                    },
                    error: function(xhr) {
                        isSaving = false;
                        $('#saveBtn').prop('disabled', false).removeClass('opacity-70 cursor-not-allowed');
                        $('#drawerButtonText').text(id ? 'Update Component' : 'Save Component');

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

            function salaryComponentDelete(id) {
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
                        let deleteUrl = "{{ route('salary-components.destroy', ':id') }}".replace(':id', id);

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
                                $('#salaryComponentTable').DataTable().ajax.reload(null, false);
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
                $('#filter_type').val('');
                $('#filter_status').val('');
                $('.dt-filter-salaryComponentTable').trigger('change');
            });

            $(document).ready(function() {
                $(document).on('click', '#btnAddSalaryComponent', function(e) {
                    e.preventDefault();
                    openSalaryComponentDrawer('add');
                });
            });
        </script>
    @endpush
</x-app-layout>