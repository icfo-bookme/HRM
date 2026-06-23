<x-app-layout>

    @push('head')
        <style>
            .encash-section { background: #fff; border-radius: 0.75rem; border: 1px solid #e2e8f0; overflow: hidden; }
            .encash-section-header {
                background: linear-gradient(135deg, #0f4c81, #1565c0);
                color: white; padding: 1rem 1.5rem; font-weight: 600;
                display: flex; align-items: center; gap: 0.75rem;
            }
            .encash-section-body { padding: 1.5rem; }
            .btn-encash-primary {
                background: linear-gradient(135deg, #0f4c81, #1565c0);
                color: #fff; padding: 0.6rem 2rem; border-radius: 0.5rem;
                font-weight: 600; transition: all 0.2s; border: none;
            }
            .btn-encash-primary:hover { opacity: 0.9; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(15, 76, 129, 0.3); }
            .btn-encash-secondary { background: #e2e8f0; color: #475569; padding: 0.6rem 2rem; border-radius: 0.5rem; font-weight: 600; transition: all 0.2s; border: none; }
            .btn-encash-secondary:hover { background: #cbd5e1; }
            .balance-info {
                background: linear-gradient(135deg, #e0f2fe, #dbeafe);
                border: 1px solid #93c5fd; border-radius: 0.75rem;
                padding: 1rem 1.5rem; display: flex; align-items: center; gap: 1rem;
            }
            .balance-info .amount { font-size: 1.75rem; font-weight: 800; color: #1e40af; }
        </style>
    @endpush

    <div class="p-4 max-w-4xl mx-auto space-y-6">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Leave Encashment Request</h1>
                <p class="text-sm text-gray-500 mt-1">Convert your unused leave days into cash</p>
            </div>
            <a href="{{ route('leave-encashment.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 transition text-sm font-medium">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        <form id="encashmentForm" method="POST" action="{{ route('leave-encashment.store') }}" class="space-y-6">
            @csrf

            {{-- Section 1: Employee & Leave --}}
            <div class="encash-section">
                <div class="encash-section-header"><i class="fas fa-user"></i><span>Employee & Leave Details</span></div>
                <div class="encash-section-body">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Employee <span class="text-red-500">*</span></label>
                            <select name="employee_id" id="employee_id" required
                                class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">-- Select Employee --</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->employee_code }} - {{ $employee->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Leave Type <span class="text-red-500">*</span></label>
                            <select name="leave_type_id" id="leave_type_id" required
                                class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">-- Select Leave Type --</option>
                                @foreach ($leaveTypes as $leaveType)
                                    <option value="{{ $leaveType->id }}">{{ $leaveType->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Balance Info (shown after selection) --}}
                    <div id="balanceContainer" class="mt-4 hidden">
                        <div class="balance-info">
                            <div class="p-2 bg-blue-100 rounded-full">
                                <i class="fas fa-coins text-blue-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-blue-600 uppercase tracking-wider">Available Balance</p>
                                <p class="amount"><span id="balanceDisplay">0.0</span> <span class="text-base font-semibold text-blue-500">days</span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section 2: Encashment Details --}}
            <div class="encash-section">
                <div class="encash-section-header"><i class="fas fa-calculator"></i><span>Encashment Calculation</span></div>
                <div class="encash-section-body">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Encashment Date <span class="text-red-500">*</span></label>
                            <input type="date" name="encashment_date" id="encashment_date" required
                                class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Days to Encash <span class="text-red-500">*</span></label>
                            <input type="number" name="days_encashed" id="days_encashed" step="0.5" min="0.5" max="999.9" required
                                class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-bold" />
                            <p id="daysWarning" class="text-xs text-red-500 mt-1 hidden">
                                <i class="fas fa-exclamation-circle"></i> Cannot exceed available balance!
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Rate per Day (BDT)</label>
                            <input type="number" name="amount_per_day" id="amount_per_day" step="0.01" min="0"
                                class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                        </div>
                    </div>

                    {{-- Auto-calculated total --}}
                    <div class="mt-4 p-4 bg-slate-50 rounded-lg border border-slate-200">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-semibold text-slate-600">Total Amount:</span>
                            <span class="text-2xl font-bold text-green-700">
                                <span id="totalAmountDisplay">0.00</span> <span class="text-sm font-semibold">BDT</span>
                            </span>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Reason / Notes</label>
                        <textarea name="reason" id="reason" rows="3" placeholder="Reason for encashment request..."
                            class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>
                </div>
            </div>

            {{-- Section 3: Status & Submit --}}
            <div class="encash-section">
                <div class="encash-section-header"><i class="fas fa-check-circle"></i><span>Submission</span></div>
                <div class="encash-section-body">
                    <div class="mb-5">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Status</label>
                        <select name="status" id="status"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="Pending">Pending (Submit for Approval)</option>
                            <option value="Approved">Approved (Directly Approve)</option>
                        </select>
                        <p class="text-xs text-slate-400 mt-1">
                            <i class="fas fa-info-circle"></i> Selecting "Approved" will immediately update the employee's leave balance.
                        </p>
                    </div>
                    <div class="flex items-center gap-3 pt-2 border-t border-slate-100">
                        <button type="submit" class="btn-encash-primary">
                            <i class="fas fa-paper-plane mr-2"></i> Submit Request
                        </button>
                        <a href="{{ route('leave-encashment.index') }}" class="btn-encash-secondary">Cancel</a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            let currentBalance = 0;

            // ===== FETCH BALANCE when employee + leave type selected =====
            function fetchBalance() {
                const empId = $('#employee_id').val();
                const ltId = $('#leave_type_id').val();

                if (empId && ltId) {
                    $.get("{{ route('leave-encashment.balance') }}", {
                        employee_id: empId,
                        leave_type_id: ltId
                    }, function(res) {
                        currentBalance = parseFloat(res.remaining_days) || 0;
                        $('#balanceDisplay').text(currentBalance.toFixed(1));
                        $('#balanceContainer').removeClass('hidden');
                        validateDays();
                    });
                } else {
                    $('#balanceContainer').addClass('hidden');
                }
            }

            $('#employee_id, #leave_type_id').on('change', fetchBalance);

            // ===== VALIDATE DAYS against balance =====
            function validateDays() {
                const days = parseFloat($('#days_encashed').val()) || 0;
                if (days > currentBalance && currentBalance > 0) {
                    $('#daysWarning').removeClass('hidden');
                    $('#days_encashed').addClass('border-red-500');
                } else {
                    $('#daysWarning').addClass('hidden');
                    $('#days_encashed').removeClass('border-red-500');
                }
            }
            $('#days_encashed').on('input', function() {
                validateDays();
                calculateTotal();
            });

            // ===== CALCULATE TOTAL =====
            function calculateTotal() {
                const days = parseFloat($('#days_encashed').val()) || 0;
                const rate = parseFloat($('#amount_per_day').val()) || 0;
                const total = days * rate;
                $('#totalAmountDisplay').text(total.toFixed(2));
            }
            $('#days_encashed, #amount_per_day').on('input', calculateTotal);
        </script>
    @endpush
</x-app-layout>