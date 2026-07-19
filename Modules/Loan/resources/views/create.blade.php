<x-app-layout>
    <div class="p-4 lg:p-6">

        <!-- Page Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-xl font-bold text-gray-800">Apply for Loan</h1>
                <p class="text-sm text-gray-500 mt-0.5">Submit a new loan application</p>
            </div>
            <a href="{{ route('loan.my') }}"
                class="inline-flex items-center px-3 py-2 text-xs font-medium rounded-lg text-gray-700 bg-gray-100 hover:bg-gray-200 transition-colors">
                <i class="fas fa-arrow-left mr-1.5"></i>Back to My Loans
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Loan Application Form -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="p-5 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-indigo-50">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-blue-900 flex items-center justify-center shadow-sm">
                                <i class="fas fa-file-invoice text-white text-sm"></i>
                            </div>
                            <div>
                                <h2 class="text-sm font-semibold text-gray-800">Loan Application Form</h2>
                                <p class="text-xs text-gray-500">Fill in the details below to submit your loan request
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <form id="loanForm" action="{{ route('loan.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="employee_id" value="{{ $employee->id }}">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <!-- Employee Info (Read-only) -->
                                <div>
                                    <x-input-label value="Employee" class="text-xs font-semibold" />
                                    <div
                                        class="flex items-center gap-2.5 w-full border border-gray-200 rounded-lg px-3.5 py-2.5 bg-gray-50 mt-1.5">
                                        <i class="fas fa-user text-gray-400 text-xs"></i>
                                        <span class="text-xs text-gray-600 font-medium">{{ $employee->employee_code }} -
                                            {{ $employee->personalInfo?->full_name ?? 'N/A' }}</span>
                                    </div>
                                </div>
                                <div>
                                    <x-input-label value="Department" class="text-xs font-semibold" />
                                    <div
                                        class="flex items-center gap-2.5 w-full border border-gray-200 rounded-lg px-3.5 py-2.5 bg-gray-50 mt-1.5">
                                        <i class="fas fa-building text-gray-400 text-xs"></i>
                                        <span
                                            class="text-xs text-gray-600 font-medium">{{ $employee->department?->name ?? 'N/A' }}</span>
                                    </div>
                                </div>

                                <!-- Loan Type (Custom Select with chevron icon) -->
                                <div>
                                    <x-input-label for="loan_type" value="Loan Type" class="text-xs font-semibold"
                                        :required="true" />
                                    <div class="relative mt-1.5">
                                        <select name="loan_type" id="loan_type" required
                                            class="w-full text-xs border border-gray-300 rounded-lg px-3.5 py-2.5 pr-8 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white transition-colors appearance-none">
                                            <option value="">Select Loan Type</option>
                                            <option value="Personal">Personal Loan</option>
                                            <option value="Emergency">Emergency Loan</option>
                                            <option value="Education">Education Loan</option>
                                            <option value="Medical">Medical Loan</option>
                                            <option value="Vehicle">Vehicle Loan</option>
                                            <option value="Home">Home Loan</option>
                                            <option value="Other">Other</option>
                                        </select>
                                        <i
                                            class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                                    </div>
                                    <x-input-error :messages="$errors->get('loan_type')" class="mt-1" />
                                </div>

                                <!-- Loan Amount with prefix -->
                                <div>
                                    <x-input-label for="loan_amount" value="Loan Amount" class="text-xs font-semibold"
                                        :required="true" />
                                    <div class="relative mt-1.5">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                            <span class="text-gray-400 text-xs font-medium">৳</span>
                                        </div>
                                        <input type="number" name="loan_amount" id="loan_amount" step="0.01"
                                            min="1" required
                                            class="w-full text-xs border border-gray-300 rounded-lg pl-7 pr-3.5 py-2.5 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors"
                                            placeholder="Enter loan amount">
                                    </div>
                                    <x-input-error :messages="$errors->get('loan_amount')" class="mt-1" />
                                </div>

                                <!-- Interest Rate with suffix -->
                                <div>
                                    <x-input-label for="interest_rate" value="Interest Rate (%)"
                                        class="text-xs font-semibold" />
                                    <div class="relative mt-1.5">
                                        <input type="number" name="interest_rate" id="interest_rate" step="0.01"
                                            min="0" max="100" value="0"
                                            class="w-full text-xs border border-gray-300 rounded-lg px-3.5 py-2.5 pr-8 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors"
                                            placeholder="0 for no interest">
                                        <span
                                            class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs font-medium">%</span>
                                    </div>
                                    <x-input-error :messages="$errors->get('interest_rate')" class="mt-1" />
                                </div>

                                <!-- Installments (Custom Select) -->
                                <div>
                                    <x-input-label for="total_installments" value="Installments (Months)"
                                        class="text-xs font-semibold" :required="true" />
                                    <div class="relative mt-1.5">
                                        <select name="total_installments" id="total_installments" required
                                            class="w-full text-xs border border-gray-300 rounded-lg px-3.5 py-2.5 pr-8 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors appearance-none bg-white">
                                            @for ($i = 1; $i <= 60; $i++)
                                                <option value="{{ $i }}" {{ $i == 12 ? 'selected' : '' }}>
                                                    {{ $i }} {{ $i === 1 ? 'Month' : 'Months' }}</option>
                                            @endfor
                                        </select>
                                        <i
                                            class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                                    </div>
                                    <x-input-error :messages="$errors->get('total_installments')" class="mt-1" />
                                </div>
                            </div>

                            <!-- Purpose -->
                            <div class="mt-5">
                                <x-input-label for="purpose" value="Purpose of Loan" class="text-xs font-semibold" />
                                <textarea name="purpose" rows="3" id="purpose"
                                    class="w-full text-xs border border-gray-300 rounded-lg px-3.5 py-2.5 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors resize-none mt-1.5"
                                    placeholder="Describe the purpose of this loan..."></textarea>
                                <x-input-error :messages="$errors->get('purpose')" class="mt-1" />
                            </div>

                            <!-- Notes -->
                            <div class="mt-4">
                                <x-input-label for="notes" value="Additional Notes"
                                    class="text-xs font-semibold" />
                                <textarea name="notes" rows="2" id="notes"
                                    class="w-full text-xs border border-gray-300 rounded-lg px-3.5 py-2.5 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors resize-none mt-1.5"
                                    placeholder="Any additional notes..."></textarea>
                                <x-input-error :messages="$errors->get('notes')" class="mt-1" />
                            </div>

                            <!-- Submit -->
                            <div class="mt-6 pt-5 border-t border-gray-100 flex items-center gap-3">
                                <button type="submit"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 text-xs font-semibold rounded-lg text-white bg-blue-800 hover:bg-blue-700 focus:ring-2 focus:ring-blue-500/30 transition-all shadow-sm">
                                    <i class="fas fa-paper-plane"></i>Submit Application
                                </button>
                                <button type="reset"
                                    class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-medium rounded-lg text-gray-600 bg-gray-50 hover:bg-gray-100 border border-gray-200 transition-colors">
                                    <i class="fas fa-undo"></i>Reset
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Side Panel: Loan Summary & Calculator Preview -->
            <div class="lg:col-span-1 space-y-5">
                <!-- Active Loan Summary -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="p-4 border-b border-gray-100 bg-gradient-to-r from-purple-50 to-pink-50">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-purple-600 flex items-center justify-center shadow-sm">
                                <i class="fas fa-chart-pie text-white text-xs"></i>
                            </div>
                            <h3 class="text-xs font-bold text-gray-800">Your Active Loans</h3>
                        </div>
                    </div>
                    <div class="p-4 space-y-3">
                        <div class="flex items-center justify-between py-2 px-3 bg-blue-50 rounded-lg">
                            <span class="text-xs text-gray-600 font-medium">Active Loans:</span>
                            <span class="text-xs font-bold text-gray-800">{{ $loanSummary['active_loans'] }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2 px-3 bg-indigo-50 rounded-lg">
                            <span class="text-xs text-gray-600 font-medium">Monthly Deduction:</span>
                            <span
                                class="text-xs font-bold text-blue-700">{{ number_format($loanSummary['monthly_deduction'], 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2 px-3 bg-violet-50 rounded-lg">
                            <span class="text-xs text-gray-600 font-medium">Total Remaining:</span>
                            <span
                                class="text-xs font-bold text-purple-700">{{ number_format($loanSummary['total_remaining'], 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2 px-3 bg-emerald-50 rounded-lg">
                            <span class="text-xs text-gray-600 font-medium">Total Paid:</span>
                            <span
                                class="text-xs font-bold text-green-700">{{ number_format($loanSummary['total_paid'], 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Loan Calculator Preview -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="p-4 border-b border-gray-100 bg-gradient-to-r from-emerald-50 to-teal-50">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-emerald-600 flex items-center justify-center shadow-sm">
                                <i class="fas fa-calculator text-white text-xs"></i>
                            </div>
                            <h3 class="text-xs font-bold text-gray-800">Loan Preview</h3>
                        </div>
                    </div>
                    <div class="p-4" id="loan-preview">
                        <div class="text-center text-xs text-gray-400 py-8" id="preview-placeholder">
                            <div
                                class="w-12 h-12 rounded-full bg-gray-50 flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-calculator text-gray-300 text-xl"></i>
                            </div>
                            <p class="text-gray-400 font-medium">Fill in the loan details</p>
                            <p class="text-gray-300 text-[11px] mt-1">to see the preview</p>
                        </div>
                        <div id="preview-details" class="hidden space-y-3">
                            <div class="flex items-center justify-between py-2 px-3 bg-gray-50 rounded-lg">
                                <span class="text-xs text-gray-600">Loan Amount:</span>
                                <span class="text-xs font-bold text-gray-800" id="preview-amount">0.00</span>
                            </div>
                            <div class="flex items-center justify-between py-2 px-3 bg-orange-50 rounded-lg">
                                <span class="text-xs text-gray-600">Interest:</span>
                                <span class="text-xs font-bold text-orange-600" id="preview-interest">0.00</span>
                            </div>
                            <div class="border-t border-gray-100 pt-2">
                                <div class="flex items-center justify-between py-2 px-3 bg-blue-50 rounded-lg">
                                    <span class="text-xs font-semibold text-gray-700">Total Payable:</span>
                                    <span class="text-xs font-bold text-blue-700" id="preview-total">0.00</span>
                                </div>
                                <div
                                    class="flex items-center justify-between py-2 px-3 mt-1.5 bg-emerald-50 rounded-lg">
                                    <span class="text-xs font-semibold text-gray-700">Per Installment:</span>
                                    <span class="text-xs font-bold text-emerald-700"
                                        id="preview-installment">0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                function calculateLoan() {
                    let amount = parseFloat($('#loan_amount').val()) || 0;
                    let interestRate = parseFloat($('#interest_rate').val()) || 0;
                    let installments = parseInt($('#total_installments').val()) || 1;

                    if (amount <= 0) {
                        $('#preview-details').addClass('hidden');
                        $('#preview-placeholder').removeClass('hidden');
                        return;
                    }

                    $('#preview-placeholder').addClass('hidden');

                    $.ajax({
                        url: "{{ route('loan.calculate') }}",
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            amount: amount,
                            interest_rate: interestRate,
                            installments: installments
                        },
                        success: function(res) {
                            $('#preview-amount').text(Number(res.loan_amount).toFixed(2));
                            $('#preview-interest').text(Number(res.total_interest).toFixed(2));
                            $('#preview-total').text(Number(res.total_payable).toFixed(2));
                            $('#preview-installment').text(Number(res.installment_amount).toFixed(2));
                            $('#preview-details').removeClass('hidden');
                        },
                        error: function() {
                            let totalInterest = amount * (interestRate / 100);
                            let totalPayable = amount + totalInterest;
                            let perInstallment = installments > 0 ? totalPayable / installments :
                                totalPayable;

                            $('#preview-amount').text(amount.toFixed(2));
                            $('#preview-interest').text(totalInterest.toFixed(2));
                            $('#preview-total').text(totalPayable.toFixed(2));
                            $('#preview-installment').text(perInstallment.toFixed(2));
                            $('#preview-details').removeClass('hidden');
                        }
                    });
                }

                let calcTimeout;
                $('#loan_amount, #interest_rate, #total_installments').on('input change', function() {
                    clearTimeout(calcTimeout);
                    calcTimeout = setTimeout(calculateLoan, 300);
                });

                $('#loanForm').on('submit', function(e) {
                    let amount = parseFloat($('#loan_amount').val()) || 0;
                    if (amount <= 0) {
                        e.preventDefault();
                        Swal.fire('Error!', 'Please enter a valid loan amount.', 'error');
                        return false;
                    }
                    return true;
                });
            });
        </script>
    @endpush
</x-app-layout>
