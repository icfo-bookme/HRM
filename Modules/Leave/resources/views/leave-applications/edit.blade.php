<x-app-layout>

    @push('head')
        {{-- CKEditor 5 Classic CDN --}}
        <script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>
        <style>
            .ck-editor__editable_inline {
                min-height: 250px;
            }
            .leave-section {
                background: #fff;
                border-radius: 0.75rem;
                border: 1px solid #e2e8f0;
                overflow: hidden;
            }
            .leave-section-header {
                background: linear-gradient(135deg, #1e3a8a, #1e40af);
                color: white;
                padding: 1rem 1.5rem;
                font-weight: 600;
                display: flex;
                align-items: center;
                gap: 0.75rem;
            }
            .leave-section-body {
                padding: 1.5rem;
            }
            .btn-leave-primary {
                background: linear-gradient(135deg, #1e3a8a, #1e40af);
                color: #fff;
                padding: 0.6rem 2rem;
                border-radius: 0.5rem;
                font-weight: 600;
                transition: all 0.2s;
                border: none;
            }
            .btn-leave-primary:hover {
                opacity: 0.9;
                transform: translateY(-1px);
                box-shadow: 0 4px 12px rgba(30, 58, 138, 0.3);
            }
            .btn-leave-secondary {
                background: #e2e8f0;
                color: #475569;
                padding: 0.6rem 2rem;
                border-radius: 0.5rem;
                font-weight: 600;
                transition: all 0.2s;
                border: none;
            }
            .btn-leave-secondary:hover {
                background: #cbd5e1;
            }
            .badge-status {
                display: inline-flex;
                align-items: center;
                gap: 0.4rem;
                padding: 0.25rem 0.75rem;
                border-radius: 9999px;
                font-size: 0.75rem;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.05em;
            }
            .badge-pending { background: #fef3c7; color: #92400e; }
            .badge-approved { background: #d1fae5; color: #065f46; }
            .badge-rejected { background: #fee2e2; color: #991b1b; }
            .badge-draft { background: #f3f4f6; color: #374151; }
            .badge-cancelled { background: #f3f4f6; color: #6b7280; }
            .badge-withdrawn { background: #ffedd5; color: #9a3412; }
        </style>
    @endpush

    <div class="p-4 max-w-5xl mx-auto space-y-6">

        {{-- Page Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Edit Leave Application</h1>
                <p class="text-sm text-gray-500 mt-1">
                    Application No: <span class="font-mono font-semibold text-indigo-600">{{ $application->application_no ?? 'N/A' }}</span>
                    <span class="mx-2">•</span>
                    Status: <span class="badge-status badge-{{ strtolower($application->status) }}">{{ $application->status }}</span>
                </p>
            </div>
            <a href="{{ route('leave-applications.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 transition text-sm font-medium">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>

        <form id="leaveApplicationForm" method="POST" action="{{ route('leave-applications.update', $application->id) }}" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- SECTION 1: Employee & Leave Type --}}
            <div class="leave-section">
                <div class="leave-section-header">
                    <i class="fas fa-user"></i>
                    <span>Employee & Leave Information</span>
                </div>
                <div class="leave-section-body">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Employee <span class="text-red-500">*</span></label>
                            <select name="employee_id" id="employee_id" required
                                class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">-- Select Employee --</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ $application->employee_id == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->employee_code }} - {{ $employee->full_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Leave Type <span class="text-red-500">*</span></label>
                            <select name="leave_type_id" id="leave_type_id" required
                                class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">-- Select Leave Type --</option>
                                @foreach ($leaveTypes as $leaveType)
                                    <option value="{{ $leaveType->id }}" {{ $application->leave_type_id == $leaveType->id ? 'selected' : '' }}>
                                        {{ $leaveType->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Application No.</label>
                            <input type="text" name="application_no" id="application_no"
                                value="{{ $application->application_no }}"
                                class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm bg-slate-50 text-slate-500"
                                readonly />
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECTION 2: Date Range & Days --}}
            <div class="leave-section">
                <div class="leave-section-header">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Leave Duration</span>
                </div>
                <div class="leave-section-body">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">From Date <span class="text-red-500">*</span></label>
                            <input type="date" name="from_date" id="from_date" required
                                value="{{ $application->from_date?->format('Y-m-d') }}"
                                class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">To Date <span class="text-red-500">*</span></label>
                            <input type="date" name="to_date" id="to_date" required
                                value="{{ $application->to_date?->format('Y-m-d') }}"
                                class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Total Days <span class="text-red-500">*</span></label>
                            <input type="number" name="total_days" id="total_days" step="0.5" min="0.5" required
                                value="{{ $application->total_days }}"
                                class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-bold" />
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Half Day</label>
                            <div class="flex items-center gap-3 h-[42px]">
                                <label class="inline-flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="is_half_day" id="is_half_day" value="1"
                                        {{ $application->is_half_day ? 'checked' : '' }}
                                        class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500" />
                                    <span class="text-sm text-slate-600">Half Day</span>
                                </label>
                                <select name="half_day_period" id="half_day_period"
                                    {{ !$application->is_half_day ? 'disabled' : '' }}
                                    class="border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                                    <option value="">-- Period --</option>
                                    <option value="First Half" {{ $application->half_day_period == 'First Half' ? 'selected' : '' }}>First Half</option>
                                    <option value="Second Half" {{ $application->half_day_period == 'Second Half' ? 'selected' : '' }}>Second Half</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECTION 3: Reason --}}
            <div class="leave-section">
                <div class="leave-section-header">
                    <i class="fas fa-pen-alt"></i>
                    <span>Reason for Leave</span>
                </div>
                <div class="leave-section-body">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Reason</label>
                        <textarea name="reason" id="reason" rows="4"
                            placeholder="Describe the reason for your leave..."
                            class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ $application->reason }}</textarea>
                    </div>
                </div>
            </div>

            {{-- SECTION 4: Professional Email (CKEditor) --}}
            <div class="leave-section">
                <div class="leave-section-header">
                    <i class="fas fa-envelope-open-text"></i>
                    <span>Professional Email</span>
                </div>
                <div class="leave-section-body">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Compose a professional email to your manager
                        </label>
                        <textarea name="professional_email" id="professional_email" rows="10">{{ $application->professional_email }}</textarea>
                        <p class="text-xs text-slate-400 mt-2">
                            <i class="fas fa-info-circle"></i> 
                            Use the toolbar above to format your email (bold, italic, lists, colors, etc.)
                        </p>
                    </div>
                </div>
            </div>

            {{-- SECTION 5: Contact & Substitute --}}
            <div class="leave-section">
                <div class="leave-section-header">
                    <i class="fas fa-phone"></i>
                    <span>Contact & Substitute</span>
                </div>
                <div class="leave-section-body">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Contact During Leave</label>
                            <input type="text" name="contact_during_leave" id="contact_during_leave" placeholder="Phone number"
                                value="{{ $application->contact_during_leave }}"
                                class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Substitute Employee</label>
                            <select name="substitute_employee_id" id="substitute_employee_id"
                                class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">-- None --</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ $application->substitute_employee_id == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->full_name }} ({{ $employee->employee_code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECTION 6: Status & Submit --}}
            <div class="leave-section">
                <div class="leave-section-header">
                    <i class="fas fa-check-circle"></i>
                    <span>Update Application</span>
                </div>
                <div class="leave-section-body">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Status</label>
                            <select name="status" id="status"
                                class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="Draft" {{ $application->status == 'Draft' ? 'selected' : '' }}>Draft</option>
                                <option value="Pending" {{ $application->status == 'Pending' ? 'selected' : '' }}>Pending (Awaiting Approval)</option>
                                <option value="Approved" {{ $application->status == 'Approved' ? 'selected' : '' }}>Approved</option>
                                <option value="Rejected" {{ $application->status == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                                <option value="Cancelled" {{ $application->status == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                                <option value="Withdrawn" {{ $application->status == 'Withdrawn' ? 'selected' : '' }}>Withdrawn</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Rejection Reason</label>
                            <textarea name="rejection_reason" id="rejection_reason" rows="2"
                                placeholder="If rejected, provide reason..."
                                class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ $application->rejection_reason }}</textarea>
                        </div>
                    </div>

                    @if ($application->approved_by)
                        <div class="mb-4 p-3 bg-slate-50 rounded-lg text-sm text-slate-600">
                            <i class="fas fa-check-circle text-green-500 mr-1"></i>
                            Approved by: <strong>{{ $application->approvedBy?->full_name ?? 'Unknown' }}</strong>
                            @if ($application->approved_at)
                                on {{ $application->approved_at->format('d M Y H:i') }}
                            @endif
                        </div>
                    @endif

                    <div class="flex items-center gap-3 pt-2 border-t border-slate-100">
                        <button type="submit" class="btn-leave-primary">
                            <i class="fas fa-save mr-2"></i> Update Application
                        </button>
                        <a href="{{ route('leave-applications.index') }}" class="btn-leave-secondary">
                            Cancel
                        </a>
                    </div>
                </div>
            </div>

        </form>
    </div>

    @push('scripts')
        <script>
            let ckeditorInstance = null;

            // ===== AUTO-CALCULATE DAYS =====
            function calculateDays() {
                const from = $('#from_date').val();
                const to = $('#to_date').val();
                if (from && to) {
                    const f = new Date(from);
                    const t = new Date(to);
                    const diff = Math.floor((t - f) / (1000 * 60 * 60 * 24)) + 1;
                    if (diff > 0) {
                        $('#total_days').val(diff);
                    }
                }
            }
            $('#from_date, #to_date').on('change', calculateDays);

            // ===== HALF DAY TOGGLE =====
            $('#is_half_day').on('change', function() {
                $('#half_day_period').prop('disabled', !this.checked);
            });

            // ===== CKEDITOR INIT =====
            ClassicEditor
                .create(document.querySelector('#professional_email'), {
                    toolbar: [
                        'heading', '|',
                        'bold', 'italic', 'underline', 'strikethrough', '|',
                        'bulletedList', 'numberedList', '|',
                        'alignment', '|',
                        'indent', 'outdent', '|',
                        'fontColor', 'fontBackgroundColor', '|',
                        'blockQuote', 'link', '|',
                        'undo', 'redo'
                    ],
                    placeholder: 'Dear Manager, please accept my leave application...',
                })
                .then(editor => {
                    ckeditorInstance = editor;
                })
                .catch(error => {
                    console.error('CKEditor initialization error:', error);
                });

            // ===== FORM SUBMIT - Update CKEditor content into textarea =====
            $('#leaveApplicationForm').on('submit', function() {
                if (ckeditorInstance) {
                    $('#professional_email').val(ckeditorInstance.getData());
                }
            });
        </script>
    @endpush
</x-app-layout>