<x-app-layout>
    <div class="p-4 lg:p-6">

        <!-- Page Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-xl font-bold text-gray-800">Loan Details</h1>
                <p class="text-sm text-gray-500 mt-0.5">View complete loan information and installment schedule</p>
            </div>
            <div class="flex gap-2">
                @if(in_array($loan->status, ['Pending', 'Rejected']))
                    <a href="{{ route('loan.edit', $loan->id) }}" class="inline-flex items-center px-3 py-2 text-xs font-medium rounded-lg text-yellow-700 bg-yellow-100 hover:bg-yellow-200 transition-colors">
                        <i class="fas fa-edit mr-1.5"></i>Edit
                    </a>
                @endif
                <a href="{{ route('loan.index') }}" class="inline-flex items-center px-3 py-2 text-xs font-medium rounded-lg text-gray-700 bg-gray-100 hover:bg-gray-200 transition-colors">
                    <i class="fas fa-arrow-left mr-1.5"></i>Back to All Loans
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Loan Information -->
            <div class="lg:col-span-2 space-y-5">

                <!-- Loan Details Card -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="p-5 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-indigo-50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-blue-600 flex items-center justify-center shadow-sm">
                                    <i class="fas fa-file-invoice text-white text-sm"></i>
                                </div>
                                <div>
                                    <h2 class="text-sm font-semibold text-gray-800">Loan Information</h2>
                                    <p class="text-xs text-gray-500">#{{ $loan->loan_number ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 text-xs font-medium rounded-full
                                @if($loan->status === 'Pending') bg-yellow-100 text-yellow-700
                                @elseif($loan->status === 'Approved') bg-green-100 text-green-700
                                @elseif($loan->status === 'Rejected') bg-red-100 text-red-700
                                @elseif($loan->status === 'Disbursed') bg-blue-100 text-blue-700
                                @elseif($loan->status === 'Completed') bg-gray-800 text-white
                                @else bg-gray-100 text-gray-500 @endif">
                                {{ $loan->status }}
                            </span>
                        </div>
                    </div>
                    <div class="p-5">
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            <div>
                                <x-input-label value="Employee" class="text-xs" />
                                <p class="text-sm font-medium text-gray-800 mt-1">{{ $loan->employee->employee_code ?? '' }} - {{ $loan->employee->personalInfo?->full_name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <x-input-label value="Department" class="text-xs" />
                                <p class="text-sm font-medium text-gray-800 mt-1">{{ $loan->employee->department?->name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <x-input-label value="Designation" class="text-xs" />
                                <p class="text-sm font-medium text-gray-800 mt-1">{{ $loan->employee->designation?->name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <x-input-label value="Loan Type" class="text-xs" />
                                <p class="text-sm font-medium text-gray-800 mt-1">{{ $loan->loan_type }}</p>
                            </div>
                            <div>
                                <x-input-label value="Application Date" class="text-xs" />
                                <p class="text-sm font-medium text-gray-800 mt-1">{{ $loan->application_date->format('d M Y') }}</p>
                            </div>
                            <div>
                                <x-input-label value="Approval Date" class="text-xs" />
                                <p class="text-sm font-medium text-gray-800 mt-1">{{ $loan->approval_date?->format('d M Y') ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <x-input-label value="Disbursement Date" class="text-xs" />
                                <p class="text-sm font-medium text-gray-800 mt-1">{{ $loan->disbursement_date?->format('d M Y') ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <x-input-label value="Approved By" class="text-xs" />
                                <p class="text-sm font-medium text-gray-800 mt-1">{{ $loan->approvedBy?->name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <x-input-label value="First Installment" class="text-xs" />
                                <p class="text-sm font-medium text-gray-800 mt-1">{{ $loan->first_installment_date?->format('d M Y') ?? 'N/A' }}</p>
                            </div>
                        </div>

                        @if($loan->rejection_reason)
                        <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-exclamation-circle text-red-500 text-xs"></i>
                                <x-input-label value="Rejection Reason" class="text-xs text-red-600 font-medium" />
                            </div>
                            <p class="text-sm text-red-700 mt-1">{{ $loan->rejection_reason }}</p>
                        </div>
                        @endif

                        @if($loan->purpose)
                        <div class="mt-4">
                            <x-input-label value="Purpose" class="text-xs" />
                            <p class="text-sm text-gray-700 mt-1">{{ $loan->purpose }}</p>
                        </div>
                        @endif

                        @if($loan->notes)
                        <div class="mt-3">
                            <x-input-label value="Notes" class="text-xs" />
                            <p class="text-sm text-gray-700 mt-1">{{ $loan->notes }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Installment Schedule -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="p-5 border-b border-gray-100 bg-gradient-to-r from-purple-50 to-pink-50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-purple-600 flex items-center justify-center shadow-sm">
                                    <i class="fas fa-calendar-check text-white text-sm"></i>
                                </div>
                                <div>
                                    <h2 class="text-sm font-semibold text-gray-800">Installment Schedule</h2>
                                    <p class="text-xs text-gray-500">{{ $summary['paid_installments'] }}/{{ $summary['total_installments'] }} Paid</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Due Date</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Paid</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payroll Run</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Paid At</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($loan->installments as $inst)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-3 text-xs text-gray-600 font-medium">{{ $inst->installment_no }}</td>
                                    <td class="px-4 py-3 text-xs text-right font-medium">{{ number_format($inst->amount, 2) }}</td>
                                    <td class="px-4 py-3 text-xs">{{ $inst->due_date->format('d M Y') }}</td>
                                    <td class="px-4 py-3 text-xs text-right">{{ number_format($inst->paid_amount, 2) }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-0.5 text-xs font-medium rounded-full
                                            @if($inst->status === 'Paid') bg-green-100 text-green-700
                                            @elseif($inst->status === 'Pending') bg-yellow-100 text-yellow-700
                                            @elseif($inst->status === 'Overdue') bg-red-100 text-red-700
                                            @elseif($inst->status === 'Waived') bg-gray-100 text-gray-500
                                            @else bg-blue-100 text-blue-700 @endif">
                                            {{ $inst->status }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-xs">{{ $inst->payrollRun?->run_label ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 text-xs">{{ $inst->paid_at?->format('d M Y H:i') ?? '-' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-xs text-gray-400">
                                        @if($loan->status === 'Pending')
                                            <div class="flex flex-col items-center gap-2">
                                                <i class="fas fa-clock text-gray-300 text-xl"></i>
                                                <p>Installments will be generated upon approval.</p>
                                            </div>
                                        @else
                                            <p>No installments found.</p>
                                        @endif
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Side Panel: Financial Summary -->
            <div class="lg:col-span-1 space-y-5">

                <!-- Financial Summary -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="p-4 border-b border-gray-100 bg-gradient-to-r from-emerald-50 to-teal-50">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-emerald-600 flex items-center justify-center shadow-sm">
                                <i class="fas fa-wallet text-white text-xs"></i>
                            </div>
                            <h3 class="text-xs font-bold text-gray-800">Financial Summary</h3>
                        </div>
                    </div>
                    <div class="p-4 space-y-3">
                        <div class="flex items-center justify-between py-2 px-3 bg-gray-50 rounded-lg">
                            <span class="text-xs text-gray-600">Loan Amount:</span>
                            <span class="text-xs font-bold text-gray-800">{{ number_format($loan->loan_amount, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2 px-3 bg-orange-50 rounded-lg">
                            <span class="text-xs text-gray-600">Interest:</span>
                            <span class="text-xs font-bold text-orange-600">{{ number_format($loan->total_interest, 2) }}</span>
                        </div>
                        <div class="border-t border-gray-100 pt-2">
                            <div class="flex items-center justify-between py-2 px-3 bg-blue-50 rounded-lg">
                                <span class="text-xs font-semibold text-gray-700">Total Payable:</span>
                                <span class="text-xs font-bold text-blue-700">{{ number_format($loan->total_payable, 2) }}</span>
                            </div>
                            <div class="flex items-center justify-between py-2 px-3 mt-1.5 bg-indigo-50 rounded-lg">
                                <span class="text-xs text-gray-600">Per Installment:</span>
                                <span class="text-xs font-bold text-indigo-700">{{ number_format($loan->installment_amount, 2) }}</span>
                            </div>
                            <div class="flex items-center justify-between py-2 px-3 mt-1.5 bg-gray-50 rounded-lg">
                                <span class="text-xs text-gray-600">Total Installments:</span>
                                <span class="text-xs font-bold text-gray-800">{{ $loan->total_installments }}</span>
                            </div>
                        </div>
                        <div class="border-t border-gray-100 pt-2">
                            <div class="flex items-center justify-between py-2 px-3 bg-emerald-50 rounded-lg">
                                <span class="text-xs text-gray-600">Paid:</span>
                                <span class="text-xs font-bold text-green-700">{{ number_format($summary['total_paid'], 2) }}</span>
                            </div>
                            <div class="flex items-center justify-between py-2 px-3 mt-1.5 bg-red-50 rounded-lg">
                                <span class="text-xs text-gray-600">Remaining:</span>
                                <span class="text-xs font-bold text-red-700">{{ number_format($summary['total_pending'], 2) }}</span>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        @php $percent = $loan->total_installments > 0 ? round(($summary['paid_installments'] / $loan->total_installments) * 100) : 0; @endphp
                        <div class="pt-2">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-xs text-gray-600 font-medium">Progress</span>
                                <span class="text-xs text-gray-600">{{ $summary['paid_installments'] }}/{{ $summary['total_installments'] }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="h-2.5 rounded-full transition-all duration-500 @if($percent >= 100) bg-green-500 @elseif($percent >= 50) bg-blue-500 @else bg-yellow-500 @endif" style="width: {{ $percent }}%"></div>
                            </div>
                            <p class="text-xs text-center text-gray-500 mt-1">{{ $percent }}% Complete</p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                @if($loan->status === 'Pending')
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="p-4 border-b border-gray-100 bg-gradient-to-r from-amber-50 to-orange-50">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-amber-600 flex items-center justify-center shadow-sm">
                                <i class="fas fa-tasks text-white text-xs"></i>
                            </div>
                            <h3 class="text-xs font-bold text-gray-800">Actions</h3>
                        </div>
                    </div>
                    <div class="p-4 space-y-2">
                        <button onclick="loanApprove({{ $loan->id }})"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 text-xs font-semibold rounded-lg text-white bg-green-600 hover:bg-green-700 focus:ring-2 focus:ring-green-500/30 transition-all shadow-sm">
                            <i class="fas fa-check"></i>Approve Loan
                        </button>
                        <button onclick="loanReject({{ $loan->id }})"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 text-xs font-medium rounded-lg text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 transition-colors">
                            <i class="fas fa-times"></i>Reject Loan
                        </button>
                    </div>
                </div>
                @endif

                @if($loan->status === 'Approved')
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="p-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-indigo-50">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center shadow-sm">
                                <i class="fas fa-hand-holding-usd text-white text-xs"></i>
                            </div>
                            <h3 class="text-xs font-bold text-gray-800">Disbursement</h3>
                        </div>
                    </div>
                    <div class="p-4">
                        <button onclick="loanDisburse({{ $loan->id }})"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 text-xs font-semibold rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:ring-2 focus:ring-blue-500/30 transition-all shadow-sm">
                            <i class="fas fa-hand-holding-usd"></i>Mark as Disbursed
                        </button>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
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
                                Swal.fire('Approved!', res.message, 'success').then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Error!', res.message, 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error!', 'Server error occurred.', 'error');
                        }
                    });
                }
            });
        }

        function loanReject(id) {
            Swal.fire({
                title: 'Reject Loan?',
                input: 'textarea',
                inputLabel: 'Rejection Reason (optional)',
                inputPlaceholder: 'Enter reason for rejection...',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                confirmButtonText: 'Yes, Reject',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('loans') }}/" + id + "/reject",
                        type: 'POST',
                        data: { rejection_reason: result.value || '' },
                        success: function(res) {
                            if (res.status === 'success') {
                                Swal.fire('Rejected!', res.message, 'success').then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Error!', res.message, 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error!', 'Server error occurred.', 'error');
                        }
                    });
                }
            });
        }

        function loanDisburse(id) {
            Swal.fire({
                title: 'Disburse Loan?',
                text: 'Mark this loan as disbursed.',
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
                                Swal.fire('Disbursed!', res.message, 'success').then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Error!', res.message, 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error!', 'Server error occurred.', 'error');
                        }
                    });
                }
            });
        }
    </script>
    @endpush
</x-app-layout>