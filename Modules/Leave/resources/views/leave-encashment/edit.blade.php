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
            .badge-status {
                display: inline-flex; align-items: center; gap: 0.4rem;
                padding: 0.25rem 0.75rem; border-radius: 9999px;
                font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;
            }
            .badge-pending { background: #fef3c7; color: #92400e; }
            .badge-approved { background: #d1fae5; color: #065f46; }
            .badge-paid { background: #dbeafe; color: #1e40af; }
        </style>
    @endpush

    <div class="p-4 max-w-4xl mx-auto space-y-6">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Edit Encashment Request</h1>
                <p class="text-sm text-gray-500 mt-1">
                    Status: <span class="badge-status badge-{{ strtolower($encashment->status) }}">{{ $encashment->status }}</span>
                </p>
            </div>
            <a href="{{ route('leave-encashment.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 transition text-sm font-medium">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        <form id="encashmentForm" method="POST" action="{{ route('leave-encashment.update', $encashment->id) }}" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Section 1: Employee & Leave --}}
            <div class="encash-section">
                <div class="encash-section-header"><i class="fas fa-user"></i><span>Employee & Leave Details</span></div>
                <div class="encash-section-body">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Employee <span class="text-red-500">*</span></label>
                            <select name="employee_id" id="employee_id" required disabled
                                class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm bg-slate-50 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ $encashment->employee_id == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->employee_code }} - {{ $employee->full_name }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="employee_id" value="{{ $encashment->employee_id }}">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Leave Type <span class="text-red-500">*</span></label>
                            <select name="leave_type_id" id="leave_type_id" required disabled
                                class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm bg-slate-50 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @foreach ($leaveTypes as $leaveType)
                                    <option value="{{ $leaveType->id }}" {{ $encashment->leave_type_id == $leaveType->id ? 'selected' : '' }}>
                                        {{ $leaveType->name }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="leave_type_id" value="{{ $encashment->leave_type_id }}">
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
                                value="{{ $encashment->encashment_date?->format('Y-m-d') }}"
                                class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Days Encashed <span class="text-red-500">*</span></label>
                            <input type="number" name="days_encashed" id="days_encashed" step="0.5" min="0.5" required
                                value="{{ $encashment->days_encashed }}"
                                class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-bold" />
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Rate per Day (BDT)</label>
                            <input type="number" name="amount_per_day" id="amount_per_day" step="0.01" min="0"
                                value="{{ $encashment->amount_per_day }}"
                                class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                        </div>
                    </div>

                    <div class="mt-4 p-4 bg-slate-50 rounded-lg border border-slate-200">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-semibold text-slate-600">Total Amount:</span>
                            <span class="text-2xl font-bold text-green-700">
                                <span id="totalAmountDisplay">{{ number_format($encashment->total_amount ?? ($encashment->amount_per_day * $encashment->days_encashed), 2) }}</span>
                                <span class="text-sm font-semibold">BDT</span>
                            </span>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Reason / Notes</label>
                        <textarea name="reason" id="reason" rows="3" placeholder="Reason for encashment request..."
                            class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ $encashment->reason }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Section 3: Status & Submit --}}
            <div class="encash-section">
                <div class="encash-section-header"><i class="fas fa-check-circle"></i><span>Update</span></div>
                <div class="encash-section-body">
                    @if ($encashment->approved_by)
                        <div class="mb-4 p-3 bg-slate-50 rounded-lg text-sm text-slate-600">
                            <i class="fas fa-check-circle text-green-500 mr-1"></i>
                            Approved by: <strong>{{ $encashment->approvedBy?->full_name ?? 'Unknown' }}</strong>
                            @if ($encashment->approved_at) on {{ $encashment->approved_at->format('d M Y H:i') }} @endif
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Status</label>
                            <select name="status" id="status"
                                class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="Pending" {{ $encashment->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="Approved" {{ $encashment->status == 'Approved' ? 'selected' : '' }}>Approved</option>
                                <option value="Paid" {{ $encashment->status == 'Paid' ? 'selected' : '' }}>Paid</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Payroll Run ID</label>
                            <input type="number" name="payroll_run_id" id="payroll_run_id" placeholder="Optional"
                                value="{{ $encashment->payroll_run_id }}"
                                class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                        </div>
                    </div>

                    <div class="flex items-center gap-3 pt-2 border-t border-slate-100">
                        <button type="submit" class="btn-encash-primary">
                            <i class="fas fa-save mr-2"></i> Update
                        </button>
                        <a href="{{ route('leave-encashment.index') }}" class="btn-encash-secondary">Cancel</a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            function calculateTotal() {
                const days = parseFloat($('#days_encashed').val()) || 0;
                const rate = parseFloat($('#amount_per_day').val()) || 0;
                $('#totalAmountDisplay').text((days * rate).toFixed(2));
            }
            $('#days_encashed, #amount_per_day').on('input', calculateTotal);
        </script>
    @endpush
</x-app-layout>