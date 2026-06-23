<x-app-layout>

    <div class="p-4">
        {{-- FILTERS --}}
        <div
            class="flex flex-col md:flex-row md:items-end gap-4 mb-5 bg-white p-4 rounded-xl border border-gray-200 shadow-sm">

            {{-- Employee Filter --}}
            <div class="flex flex-col w-full md:w-1/4">
                <x-form-select label="Employee" id="filter_employee" class="dt-filter-employeeLeaveBalanceTable">
                    <option value="">All Employees</option>
                    @foreach ($employees ?? [] as $employee)
                        <option value="{{ $employee->id }}">{{ $employee->employee_code }} - {{ $employee->full_name }}</option>
                    @endforeach
                </x-form-select>
            </div>

            {{-- Leave Type Filter --}}
            <div class="flex flex-col w-full md:w-1/4">
                <x-form-select label="Leave Type" id="filter_leave_type" class="dt-filter-employeeLeaveBalanceTable">
                    <option value="">All Leave Types</option>
                    @foreach ($leaveTypes ?? [] as $leaveType)
                        <option value="{{ $leaveType->id }}">{{ $leaveType->name }}</option>
                    @endforeach
                </x-form-select>
            </div>

            {{-- Fiscal Year Filter --}}
            <div class="flex flex-col w-full md:w-1/4">
                <x-form-select label="Fiscal Year" id="filter_fiscal_year" class="dt-filter-employeeLeaveBalanceTable">
                    <option value="">All Fiscal Years</option>
                    @foreach ($fiscalYears ?? [] as $year)
                        <option value="{{ $year->id }}">{{ $year->name ?? $year->id }}</option>
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

        {{-- DATA-TABLE COMPONENT --}}
        <x-data-table id="employeeLeaveBalanceTable" title="Employee Leave Balance Management" icon="fa-solid fa-scale-balanced"
            buttonId="btnAddLeaveBalance" buttonText="Add Leave Balance"
            :columns="['SL', 'Employee', 'Leave Type', 'Fiscal Year', 'Opening', 'Earned', 'Used', 'Encashed', 'Lapsed', 'Pending', 'Remaining', 'Updated At', 'Action']"
            :ajaxUrl="route('employee-leave-balances.dataTable')"
            :dtColumns="[
                ['data' => 'DT_RowIndex', 'name' => 'id', 'width' => '40px'],
                ['data' => 'employee', 'width' => '180px'],
                ['data' => 'leave_type', 'width' => '120px'],
                ['data' => 'fiscal_year', 'width' => '100px'],
                ['data' => 'opening_balance', 'width' => '80px', 'className' => 'text-right'],
                ['data' => 'earned_days', 'width' => '80px', 'className' => 'text-right'],
                ['data' => 'used_days', 'width' => '70px', 'className' => 'text-right'],
                ['data' => 'encashed_days', 'width' => '80px', 'className' => 'text-right'],
                ['data' => 'lapsed_days', 'width' => '70px', 'className' => 'text-right'],
                ['data' => 'pending_days', 'width' => '80px', 'className' => 'text-right'],
                ['data' => 'remaining_days', 'width' => '90px', 'className' => 'text-right font-bold'],
                ['data' => 'updated_at', 'width' => '100px'],
                ['data' => 'action', 'orderable' => false, 'searchable' => false, 'width' => '80px'],
            ]"
            :filters="[
                'employee_id' => '#filter_employee',
                'leave_type_id' => '#filter_leave_type',
                'fiscal_year_id' => '#filter_fiscal_year',
            ]"
            :exportButtons="true" />
    </div>

    {{-- DRAWER COMPONENT --}}
    <x-drawer id="leave-balance-drawer" overlayId="leave-balance-overlay" title="Add Leave Balance" submitOnClick="saveForm()">
        <form id="leaveBalanceForm">
            <input type="hidden" name="id" id="balance_id">

            {{-- Employee Select --}}
            <div class="mb-4 animate-fade" style="animation-delay: 50ms;">
                <x-form-select label="Employee" name="employee_id" id="employee_id" placeholder="Select Employee" required>
                    <option value="">-- Select Employee --</option>
                    @foreach ($employees ?? [] as $employee)
                        <option value="{{ $employee->id }}">{{ $employee->employee_code }} - {{ $employee->full_name }}</option>
                    @endforeach
                </x-form-select>
            </div>

            {{-- Leave Type & Fiscal Year --}}
            <div class="grid grid-cols-2 gap-3 mb-4 animate-fade" style="animation-delay: 100ms;">
                <x-form-select label="Leave Type" name="leave_type_id" id="leave_type_id" placeholder="Select Leave Type" required>
                    <option value="">-- Select Leave Type --</option>
                    @foreach ($leaveTypes ?? [] as $leaveType)
                        <option value="{{ $leaveType->id }}">{{ $leaveType->name }}</option>
                    @endforeach
                </x-form-select>
                <x-form-select label="Fiscal Year" name="fiscal_year_id" id="fiscal_year_id" placeholder="Select Fiscal Year" required>
                    <option value="">-- Select Fiscal Year --</option>
                    @foreach ($fiscalYears ?? [] as $year)
                        <option value="{{ $year->id }}">{{ $year->label ?? $year->id }}</option>
                    @endforeach
                </x-form-select>
            </div>

            {{-- Balance Fields --}}
            <div class="grid grid-cols-3 gap-3 mb-4 animate-fade" style="animation-delay: 150ms;">
                <x-form-input label="Opening Balance" name="opening_balance" id="opening_balance" type="number" step="0.5" placeholder="0.0" value="0" />
                <x-form-input label="Earned Days" name="earned_days" id="earned_days" type="number" step="0.5" placeholder="0.0" value="0" />
                <x-form-input label="Used Days" name="used_days" id="used_days" type="number" step="0.5" placeholder="0.0" value="0" />
            </div>

            <div class="grid grid-cols-3 gap-3 mb-4 animate-fade" style="animation-delay: 200ms;">
                <x-form-input label="Encashed Days" name="encashed_days" id="encashed_days" type="number" step="0.5" placeholder="0.0" value="0" />
                <x-form-input label="Lapsed Days" name="lapsed_days" id="lapsed_days" type="number" step="0.5" placeholder="0.0" value="0" />
                <x-form-input label="Pending Days" name="pending_days" id="pending_days" type="number" step="0.5" placeholder="0.0" value="0" />
            </div>

            {{-- Remaining (read-only, auto-calculated by DB) --}}
            <div class="mb-4 animate-fade" style="animation-delay: 250ms;">
                <x-form-input label="Remaining Days (Auto-calculated)" name="remaining_days_display" id="remaining_days_display"
                    type="text" placeholder="Auto-calculated by system" disabled />
            </div>
        </form>
    </x-drawer>

    @push('scripts')
        <script>
            let isSaving = false;

            // ===== DRAWER FUNCTIONS =====
            function openLeaveBalanceDrawer(mode = 'add') {
                if (mode === 'edit') {
                    $('#drawerTitle').text('Update Leave Balance');
                    $('#drawerButtonText').text('Update Balance');
                } else {
                    resetForm();
                    $('#drawerTitle').text('Add Leave Balance');
                    $('#drawerButtonText').text('Save Balance');
                }
                openGlobalDrawer('leave-balance-drawer', 'leave-balance-overlay');
            }

            function resetForm() {
                $('#leaveBalanceForm')[0].reset();
                $('#balance_id').val('');
                $('#opening_balance').val(0);
                $('#earned_days').val(0);
                $('#used_days').val(0);
                $('#encashed_days').val(0);
                $('#lapsed_days').val(0);
                $('#pending_days').val(0);
                $('#remaining_days_display').val('');
            }

            // ===== CALCULATE REMAINING DAYS (Live preview in drawer) =====
            function calculateRemainingPreview() {
                let opening = parseFloat($('#opening_balance').val()) || 0;
                let earned = parseFloat($('#earned_days').val()) || 0;
                let used = parseFloat($('#used_days').val()) || 0;
                let encashed = parseFloat($('#encashed_days').val()) || 0;
                let lapsed = parseFloat($('#lapsed_days').val()) || 0;
                let pending = parseFloat($('#pending_days').val()) || 0;

                let remaining = opening + earned - used - encashed - lapsed - pending;
                $('#remaining_days_display').val(remaining.toFixed(1));
            }

            $(document).on('input', '#opening_balance, #earned_days, #used_days, #encashed_days, #lapsed_days, #pending_days', function() {
                calculateRemainingPreview();
            });

            // ===== EDIT - Fetch and populate =====
            function employeeLeaveBalanceEdit(id) {
                Swal.fire({
                    title: 'Loading...',
                    text: 'Fetching leave balance details',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                resetForm();
                let fetchUrl = "{{ route('employee-leave-balances.show', ':id') }}".replace(':id', id);

                $.get(fetchUrl, function(res) {
                    Swal.close();
                    if (res.status === 'success' || res.status === true) {
                        let d = res.data;
                        $('#balance_id').val(d.id);
                        $('#employee_id').val(d.employee_id);
                        $('#leave_type_id').val(d.leave_type_id);
                        $('#fiscal_year_id').val(d.fiscal_year_id);
                        $('#opening_balance').val(d.opening_balance);
                        $('#earned_days').val(d.earned_days);
                        $('#used_days').val(d.used_days);
                        $('#encashed_days').val(d.encashed_days);
                        $('#lapsed_days').val(d.lapsed_days);
                        $('#pending_days').val(d.pending_days);
                        $('#remaining_days_display').val(d.remaining_days);

                        openLeaveBalanceDrawer('edit');
                    } else {
                        Swal.fire('Error', res.message || 'Failed to fetch data.', 'error');
                    }
                }).fail(function() {
                    Swal.close();
                    Swal.fire('Error', 'Server communication error.', 'error');
                });
            }

            // ===== SAVE (Create / Update) =====
            function saveForm() {
                if (isSaving) return;

                let id = $('#balance_id').val();
                let url = id ? "{{ route('employee-leave-balances.update', ':id') }}".replace(':id', id) :
                    "{{ route('employee-leave-balances.store') }}";

                let formData = $('#leaveBalanceForm').serialize();
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
                            closeGlobalDrawer('leave-balance-drawer', 'leave-balance-overlay');
                            $('#employeeLeaveBalanceTable').DataTable().ajax.reload(null, false);
                        } else {
                            Swal.fire('Error', res.message || 'Something went wrong', 'error');
                            $('#drawerButtonText').text(id ? 'Update Balance' : 'Save Balance');
                        }
                    },
                    error: function(xhr) {
                        isSaving = false;
                        $('#saveBtn').prop('disabled', false).removeClass('opacity-70 cursor-not-allowed');
                        $('#drawerButtonText').text(id ? 'Update Balance' : 'Save Balance');

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

            // ===== DELETE =====
            function employeeLeaveBalanceDelete(id) {
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
                        let deleteUrl = "{{ route('employee-leave-balances.destroy', ':id') }}".replace(':id', id);

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
                                $('#employeeLeaveBalanceTable').DataTable().ajax.reload(null, false);
                            } else {
                                Swal.fire('Error', res.message || 'Deletion failed.', 'error');
                            }
                        }).fail(function() {
                            Swal.fire('Error', 'Failed to communicate with server.', 'error');
                        });
                    }
                });
            }

            // ===== RESET FILTERS =====
            $('#resetFilters').on('click', function() {
                $('#filter_employee').val('');
                $('#filter_leave_type').val('');
                $('#filter_fiscal_year').val('');
                $('.dt-filter-employeeLeaveBalanceTable').trigger('change');
            });

            // ===== ADD BUTTON =====
            $(document).ready(function() {
                $(document).on('click', '#btnAddLeaveBalance', function(e) {
                    e.preventDefault();
                    openLeaveBalanceDrawer('add');
                });
            });
        </script>
    @endpush
</x-app-layout>