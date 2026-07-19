<x-app-layout>
    <div class="p-4 lg:p-6">

        <!-- Page Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-xl font-bold text-gray-800">Loan Management</h1>
                <p class="text-sm text-gray-500 mt-0.5">Manage all employee loan applications</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('loan.my') }}" class="inline-flex items-center px-3 py-2 text-xs font-medium rounded-lg text-blue-700 bg-blue-100 hover:bg-blue-200 transition-colors">
                    <i class="fas fa-user mr-1.5"></i>My Loans
                </a>
                <a href="{{ route('loan.create') }}" class="inline-flex items-center px-3 py-2 text-xs font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 transition-colors">
                    <i class="fas fa-plus mr-1.5"></i>Apply Loan
                </a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-5">
            <div class="bg-white rounded-lg border border-gray-200 p-3">
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Total Loans</p>
                <p class="text-xl font-bold text-gray-800 mt-1">{{ $statistics['total_loans'] }}</p>
            </div>
            <div class="bg-white rounded-lg border border-yellow-200 p-3">
                <p class="text-xs text-yellow-600 font-medium uppercase tracking-wide">Pending</p>
                <p class="text-xl font-bold text-yellow-700 mt-1">{{ $statistics['pending_loans'] }}</p>
            </div>
            <div class="bg-white rounded-lg border border-green-200 p-3">
                <p class="text-xs text-green-600 font-medium uppercase tracking-wide">Active</p>
                <p class="text-xl font-bold text-green-700 mt-1">{{ $statistics['active_loans'] }}</p>
            </div>
            <div class="bg-white rounded-lg border border-gray-800 p-3">
                <p class="text-xs text-gray-600 font-medium uppercase tracking-wide">Completed</p>
                <p class="text-xl font-bold text-gray-800 mt-1">{{ $statistics['completed_loans'] }}</p>
            </div>
            <div class="bg-white rounded-lg border border-blue-200 p-3">
                <p class="text-xs text-blue-600 font-medium uppercase tracking-wide">Disbursed</p>
                <p class="text-xl font-bold text-blue-700 mt-1">{{ number_format($statistics['total_disbursed'], 0) }}</p>
            </div>
            <div class="bg-white rounded-lg border border-purple-200 p-3">
                <p class="text-xs text-purple-600 font-medium uppercase tracking-wide">Outstanding</p>
                <p class="text-xl font-bold text-purple-700 mt-1">{{ number_format($statistics['total_outstanding'], 0) }}</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg border border-gray-200 p-3 mb-4">
            <div class="flex flex-wrap items-center gap-3">
                <div class="flex items-center gap-2">
                    <label class="text-xs font-medium text-gray-600">Status:</label>
                    <select id="filter-status" class="dt-filter-loans-table text-xs border border-gray-300 rounded-lg px-2 py-1.5 focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Statuses</option>
                        <option value="Pending">Pending</option>
                        <option value="Approved">Approved</option>
                        <option value="Rejected">Rejected</option>
                        <option value="Disbursed">Disbursed</option>
                        <option value="Completed">Completed</option>
                        <option value="Cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <label class="text-xs font-medium text-gray-600">Type:</label>
                    <select id="filter-type" class="dt-filter-loans-table text-xs border border-gray-300 rounded-lg px-2 py-1.5 focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Types</option>
                        <option value="Personal">Personal</option>
                        <option value="Emergency">Emergency</option>
                        <option value="Education">Education</option>
                        <option value="Medical">Medical</option>
                        <option value="Vehicle">Vehicle</option>
                        <option value="Home">Home</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <label class="text-xs font-medium text-gray-600">Employee:</label>
                    <select id="filter-employee" class="dt-filter-loans-table text-xs border border-gray-300 rounded-lg px-2 py-1.5 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 min-w-[180px]">
                        <option value="">All Employees</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->employee_code }} - {{ $emp->personalInfo?->full_name ?? 'N/A' }}</option>
                        @endforeach
                    </select>
                </div>
                <button id="reset-filters" class="text-xs px-3 py-1.5 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition-colors">
                    <i class="fas fa-undo mr-1"></i>Reset
                </button>
            </div>
        </div>

        <!-- DataTable using reusable component with PHP array syntax -->
        <x-data-table
            id="loans-table"
            title="All Loan Applications"
            icon="fa-solid fa-hand-holding-usd"
            :buttonId="null"
            ajaxUrl="{{ route('loan.dataTable') }}"
            :columns="['#', 'Loan No', 'Employee', 'Type', 'Amount', 'Payable', 'Installment', 'Date', 'Status', 'Progress', 'Action']"
            :dtColumns="[
                ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'orderable' => false, 'searchable' => false, 'className' => 'px-3 py-2 w-10'],
                ['data' => 'loan_number', 'name' => 'loan_number', 'className' => 'px-3 py-2 font-mono text-xs'],
                ['data' => 'employee_id', 'name' => 'employee_id', 'className' => 'px-3 py-2'],
                ['data' => 'loan_type', 'name' => 'loan_type', 'className' => 'px-3 py-2'],
                ['data' => 'loan_amount', 'name' => 'loan_amount', 'className' => 'px-3 py-2 text-right font-medium'],
                ['data' => 'total_payable', 'name' => 'total_payable', 'className' => 'px-3 py-2 text-right'],
                ['data' => 'installment_amount', 'name' => 'installment_amount', 'className' => 'px-3 py-2 text-right'],
                ['data' => 'application_date', 'name' => 'application_date', 'className' => 'px-3 py-2'],
                ['data' => 'status', 'name' => 'status', 'className' => 'px-3 py-2'],
                ['data' => 'progress', 'name' => 'progress', 'orderable' => false, 'searchable' => false, 'className' => 'px-3 py-2'],
                ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false, 'className' => 'px-3 py-2 text-center'],
            ]"
            :filters="['status' => '#filter-status', 'loan_type' => '#filter-type', 'employee_id' => '#filter-employee']"
            :exportButtons="true"
            orderColumn="7"
            orderDirection="desc"
        />

    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            // Reset filters
            $('#reset-filters').on('click', function() {
                $('#filter-status, #filter-type, #filter-employee').val('');
                $('#loans-table').DataTable().ajax.reload();
            });
        });

        // Approve Loan
        function loanApprove(id) {
            Swal.fire({
                title: 'Approve Loan?',
                text: 'This will generate installment schedule and mark the loan as approved.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#16a34a',
                confirmButtonText: 'Yes, Approve',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('loans') }}/" + id + "/approve",
                        type: 'POST',
                        success: function(res) {
                            if (res.status === 'success') {
                                Swal.fire('Approved!', res.message, 'success');
                                $('#loans-table').DataTable().ajax.reload();
                            } else {
                                Swal.fire('Error!', res.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            Swal.fire('Error!', 'Server error occurred.', 'error');
                        }
                    });
                }
            });
        }

        // Reject Loan
        function loanReject(id) {
            Swal.fire({
                title: 'Reject Loan?',
                input: 'textarea',
                inputLabel: 'Rejection Reason (optional)',
                inputPlaceholder: 'Enter reason for rejection...',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                confirmButtonText: 'Yes, Reject',
                cancelButtonText: 'Cancel',
                inputValidator: (value) => { return null; }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('loans') }}/" + id + "/reject",
                        type: 'POST',
                        data: { rejection_reason: result.value || '' },
                        success: function(res) {
                            if (res.status === 'success') {
                                Swal.fire('Rejected!', res.message, 'success');
                                $('#loans-table').DataTable().ajax.reload();
                            } else {
                                Swal.fire('Error!', res.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            Swal.fire('Error!', 'Server error occurred.', 'error');
                        }
                    });
                }
            });
        }

        // Disburse Loan
        function loanDisburse(id) {
            Swal.fire({
                title: 'Disburse Loan?',
                text: 'Mark this loan as disbursed. The employee will receive the loan amount.',
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#2563eb',
                confirmButtonText: 'Yes, Disburse',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('loans') }}/" + id + "/disburse",
                        type: 'POST',
                        success: function(res) {
                            if (res.status === 'success') {
                                Swal.fire('Disbursed!', res.message, 'success');
                                $('#loans-table').DataTable().ajax.reload();
                            } else {
                                Swal.fire('Error!', res.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            Swal.fire('Error!', 'Server error occurred.', 'error');
                        }
                    });
                }
            });
        }

        // Delete Loan
        function loanDelete(id) {
            Swal.fire({
                title: 'Delete Loan?',
                text: 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                confirmButtonText: 'Yes, Delete',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('loans') }}/" + id,
                        type: 'DELETE',
                        success: function(res) {
                            if (res.status === 'success') {
                                Swal.fire('Deleted!', res.message, 'success');
                                $('#loans-table').DataTable().ajax.reload();
                            } else {
                                Swal.fire('Error!', res.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            Swal.fire('Error!', 'Server error occurred.', 'error');
                        }
                    });
                }
            });
        }
    </script>
    @endpush
</x-app-layout>