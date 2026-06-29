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
                background: linear-gradient(135deg, #006172, #1e40af);
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
        </style>
    @endpush

    <div class="p-4 max-w-5xl mx-auto space-y-6">

        {{-- Page Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 tracking-tight">New Leave Application</h1>
                <p class="text-sm text-gray-500 mt-1">Submit a professional leave request</p>
            </div>
            <a href="{{ route('leave-applications.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 transition text-sm font-medium">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
                <div class="flex items-start gap-3">
                    <i class="fas fa-exclamation-circle text-red-500 mt-0.5"></i>
                    <div>
                        <p class="text-sm font-semibold text-red-800">Validation Error</p>
                        <ul class="mt-2 list-disc list-inside text-sm text-red-700 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form id="leaveApplicationForm" method="POST" action="{{ route('leave-applications.store') }}"
            enctype="multipart/form-data" class="space-y-6">
            @csrf

            {{-- SECTION 1: Employee & Leave Type --}}
            <div class="leave-section">
                <div class="leave-section-header">
                    <i class="fas fa-user"></i>
                    <span>Employee & Leave Information</span>
                </div>
                <div class="leave-section-body">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <x-form-select label="Employee" name="employee_id" id="employee_id" required>
                            <option value="">-- Select Employee --</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}"
                                    {{ ($loggedInEmployeeId ?? '') == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->employee_code }} - {{ $employee->full_name }}
                                </option>
                            @endforeach
                        </x-form-select>

                        <x-form-select label="Leave Type" name="leave_type_id" id="leave_type_id" required>
                            <option value="">-- Select Leave Type --</option>
                            @foreach ($leaveTypes as $leaveType)
                                <option value="{{ $leaveType->id }}">{{ $leaveType->name }}</option>
                            @endforeach
                        </x-form-select>

                        <x-form-input label="Application No." name="application_no" id="application_no"
                            placeholder="Auto-generated" value="{{ old('application_no') }}" readonly />
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
                        <x-form-input label="From Date" name="from_date" id="from_date" type="date" required
                            value="{{ old('from_date') }}" />

                        <x-form-input label="To Date" name="to_date" id="to_date" type="date" required
                            value="{{ old('to_date') }}" />

                        <x-form-input label="Total Days" name="total_days" id="total_days" type="number" step="0.5"
                            min="0.5" required value="{{ old('total_days') }}" />

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Half Day</label>
                            <div class="flex items-center gap-3 h-[42px]">
                                <label class="inline-flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="is_half_day" id="is_half_day" value="1"
                                        class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500" />
                                    <span class="text-sm text-slate-600">Half Day</span>
                                </label>
                                <select name="half_day_period" id="half_day_period" disabled
                                    class="border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                                    <option value="">-- Period --</option>
                                    <option value="First Half">First Half</option>
                                    <option value="Second Half">Second Half</option>
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
                    <x-form-textarea label="Reason" name="reason" id="reason" rows="4"
                        placeholder="Describe the reason for your leave...">{{ old('reason') }}</x-form-textarea>
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
                        <textarea name="professional_email" id="professional_email" rows="10"
                            placeholder="Dear Manager,&#10;&#10;I am writing to formally request leave from [date] to [date]...">
                        </textarea>
                        <p class="text-xs text-slate-400 mt-2">
                            <i class="fas fa-info-circle"></i>
                            Use the toolbar above to format your email (bold, italic, lists, etc.)
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
                        <x-form-input label="Contact During Leave" name="contact_during_leave" id="contact_during_leave"
                            placeholder="Phone number" value="{{ old('contact_during_leave') }}" />

                        <x-form-select label="Substitute Employee" name="substitute_employee_id" id="substitute_employee_id">
                            <option value="">-- None --</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}"
                                    {{ old('substitute_employee_id') == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->full_name }} ({{ $employee->employee_code }})
                                </option>
                            @endforeach
                        </x-form-select>
                    </div>
                </div>
            </div>

            {{-- SECTION 6: Status & Submit --}}
            <div class="leave-section">
                <div class="leave-section-header">
                    <i class="fas fa-check-circle"></i>
                    <span>Submission</span>
                </div>
                <div class="leave-section-body">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
                        <x-form-select label="Status" name="status" id="status">
                            <option value="Draft">Draft (Save without submitting)</option>
                            <option value="Pending" selected>Pending (Submit for approval)</option>
                        </x-form-select>

                        <div class="flex flex-col">
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Document (optional)</label>
                            <input type="file" name="document_file" id="document_file"
                                class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm file:mr-3 file:py-1.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                        </div>
                    </div>
                    <div class="flex items-center gap-3 pt-2 border-t border-slate-100">
                        <button type="submit" class="btn-leave-primary">
                            <i class="fas fa-paper-plane mr-2"></i> Submit Application
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