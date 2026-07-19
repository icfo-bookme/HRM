<x-app-layout>
    <div class="p-4 lg:p-6">

        <!-- Page Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-xl font-bold text-gray-800">My Loans</h1>
                <p class="text-sm text-gray-500 mt-0.5">View and manage your loan applications</p>
            </div>
            <a href="{{ route('loan.create') }}" class="inline-flex items-center px-3 py-2 text-xs font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 transition-colors">
                <i class="fas fa-plus mr-1.5"></i>Apply New Loan
            </a>
        </div>

        <!-- Loan Summary -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-5">
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Active Loans</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ $loanSummary['active_loans'] }}</p>
            </div>
            <div class="bg-white rounded-lg border border-blue-200 p-4">
                <p class="text-xs text-blue-600 font-medium uppercase tracking-wide">Monthly Deduction</p>
                <p class="text-2xl font-bold text-blue-700 mt-1">{{ number_format($loanSummary['monthly_deduction'], 2) }}</p>
            </div>
            <div class="bg-white rounded-lg border border-purple-200 p-4">
                <p class="text-xs text-purple-600 font-medium uppercase tracking-wide">Total Remaining</p>
                <p class="text-2xl font-bold text-purple-700 mt-1">{{ number_format($loanSummary['total_remaining'], 2) }}</p>
            </div>
        </div>

        <!-- DataTable using reusable component with PHP array syntax -->
        <x-data-table
            id="my-loans-table"
            title="My Loan Applications"
            icon="fa-solid fa-file-invoice"
            :buttonId="null"
            ajaxUrl="{{ route('loan.my.dataTable') }}"
            :columns="['#', 'Loan No', 'Type', 'Amount', 'Payable', 'Installment', 'Date', 'Status', 'Progress', 'Action']"
            :dtColumns="[
                ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'orderable' => false, 'searchable' => false, 'className' => 'px-3 py-2 w-10'],
                ['data' => 'loan_number', 'name' => 'loan_number', 'className' => 'px-3 py-2 font-mono text-xs'],
                ['data' => 'loan_type', 'name' => 'loan_type', 'className' => 'px-3 py-2'],
                ['data' => 'loan_amount', 'name' => 'loan_amount', 'className' => 'px-3 py-2 text-right font-medium'],
                ['data' => 'total_payable', 'name' => 'total_payable', 'className' => 'px-3 py-2 text-right'],
                ['data' => 'installment_amount', 'name' => 'installment_amount', 'className' => 'px-3 py-2 text-right'],
                ['data' => 'application_date', 'name' => 'application_date', 'className' => 'px-3 py-2'],
                ['data' => 'status', 'name' => 'status', 'className' => 'px-3 py-2'],
                ['data' => 'progress', 'name' => 'progress', 'orderable' => false, 'searchable' => false, 'className' => 'px-3 py-2'],
                ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false, 'className' => 'px-3 py-2 text-center'],
            ]"
            :exportButtons="false"
            orderColumn="6"
            orderDirection="desc"
        />

    </div>
</x-app-layout>