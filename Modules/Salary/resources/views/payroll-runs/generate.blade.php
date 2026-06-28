<x-app-layout>

    <div class="p-4">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            {{-- Header --}}
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-blue-600 flex items-center justify-center">
                        <i class="fas fa-calculator text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-800">Generate Payroll Run</h2>
                        <p class="text-sm text-gray-500">All active employees will be automatically included based on their salary structures</p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                {{-- Selection Form --}}
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Fiscal Year <span class="text-red-500">*</span></label>
                        <select id="fiscal_year_id" name="fiscal_year_id"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">-- Select Fiscal Year --</option>
                            @foreach ($fiscalYears as $fy)
                                <option value="{{ $fy->id }}" data-start="{{ $fy->start_date }}" data-end="{{ $fy->end_date }}">{{ $fy->label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Year <span class="text-red-500">*</span></label>
                        <select id="run_year" name="run_year"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">-- Year --</option>
                            @for($y = now()->year - 2; $y <= now()->year + 1; $y++)
                            <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Month <span class="text-red-500">*</span></label>
                        <select id="run_month" name="run_month"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">-- Month --</option>
                            @foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $i => $m)
                            <option value="{{ str_pad($i+1, 2, '0', STR_PAD_LEFT) }}" {{ $i+1 == now()->month ? 'selected' : '' }}>{{ $m }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Run Type</label>
                        <select id="run_type" name="run_type"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="Regular">Regular</option>
                            <option value="Bonus">Bonus</option>
                            <option value="Advance">Advance</option>
                            <option value="Adjustment">Adjustment</option>
                        </select>
                    </div>
                    <div class="md:col-span-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Label (Optional)</label>
                        <input type="text" id="run_label" name="run_label" placeholder="e.g. June 2026 Payroll"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>

                {{-- Generate Button - Only action on this page --}}
                <div class="flex gap-3 mb-6">
                    <button id="btnGenerate"
                        class="px-6 py-2.5 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition flex items-center gap-2 shadow-sm">
                        <i class="fas fa-play"></i> Generate Payroll
                    </button>
                    <button type="button" onclick="window.location.href='{{ route('payroll-runs.index') }}'"
                        class="px-6 py-2.5 text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition flex items-center gap-2">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </button>
                </div>

                {{-- Loading State --}}
                <div id="loadingState" class="hidden text-center py-8">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-3"></div>
                    <p class="text-gray-500 text-sm">Generating payroll for all active employees...</p>
                </div>

                {{-- Success Result --}}
                <div id="resultCard" class="hidden">
                    <div class="bg-green-50 border border-green-200 rounded-xl p-6 mb-6">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-check-circle text-2xl text-green-600"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-green-800" id="resultTitle">Payroll Generated Successfully!</h3>
                                <p class="text-green-700 text-sm mt-1" id="resultMessage"></p>
                                <div class="mt-4 flex gap-3">
                                    <a id="viewRunBtn" href="#" class="px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg transition">
                                        <i class="fas fa-eye"></i> View Payroll Run
                                    </a>
                                    <button onclick="resetForm()" class="px-4 py-2 text-sm font-medium text-green-700 bg-green-100 hover:bg-green-200 rounded-lg transition">
                                        <i class="fas fa-redo"></i> Generate Another
                                    </button>
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
                // Auto-fill fiscal year when month changes
                function autoFillFiscalYear() {
                    const year = $('#run_year').val();
                    const month = $('#run_month').val();
                    if (!year || !month) return;
                    const selectedDate = new Date(year + '-' + month + '-01');
                    $('#fiscal_year_id option').each(function() {
                        if ($(this).val() === '') return;
                        const start = new Date($(this).data('start'));
                        const end = new Date($(this).data('end'));
                        if (selectedDate >= start && selectedDate <= end) {
                            $('#fiscal_year_id').val($(this).val());
                            return false;
                        }
                    });
                }

                $('#run_year, #run_month').on('change', autoFillFiscalYear);
                // Auto-fill on page load
                autoFillFiscalYear();

                // Update label suggestion
                function updateLabelSuggestion() {
                    const year = $('#run_year').val();
                    const monthVal = $('#run_month').val();
                    const monthNames = ['January','February','March','April','May','June','July','August','September','October','November','December'];
                    if (year && monthVal) {
                        const monthName = monthNames[parseInt(monthVal) - 1] || '';
                        const label = $('#run_label');
                        
                            label.val(monthName + ' ' + year + ' Payroll');
                       
                    }
                }
                $('#run_year, #run_month').on('change', updateLabelSuggestion);
            });

            // Generate button
            $('#btnGenerate').on('click', function() {
                const year = $('#run_year').val();
                const monthVal = $('#run_month').val();
                const fiscalYearId = $('#fiscal_year_id').val();

                if (!year) {
                    Swal.fire('Error', 'Please select a year.', 'warning');
                    return;
                }
                if (!monthVal) {
                    Swal.fire('Error', 'Please select a month.', 'warning');
                    return;
                }
                if (!fiscalYearId) {
                    Swal.fire('Error', 'Please select a fiscal year.', 'warning');
                    return;
                }

                const runMonth = year + '-' + monthVal;
                const monthNames = ['January','February','March','April','May','June','July','August','September','October','November','December'];
                const monthName = monthNames[parseInt(monthVal) - 1] || monthVal;

                Swal.fire({
                    title: 'Generate Payroll?',
                    html: `This will create a payroll run for <b>${monthName} ${year}</b>.<br>All employees with salary structures will be auto-calculated.`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#2563eb',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, Generate!',
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        return new Promise((resolve) => {
                            $('#loadingState').removeClass('hidden');
                            $('#resultCard').addClass('hidden');

                            $.ajax({
                                url: '{{ route("payroll-runs.store") }}',
                                type: 'POST',
                                data: {
                                    fiscal_year_id: fiscalYearId,
                                    run_month: runMonth + '-01',
                                    run_type: $('#run_type').val(),
                                    run_label: $('#run_label').val(),
                                },
                                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                                success: function(res) {
                                    $('#loadingState').addClass('hidden');
                                    if (res.status === 'success') {
                                        $('#resultMessage').text(res.message);
                                        $('#viewRunBtn').attr('href', '{{ route("payroll-runs.show-generated", ":id") }}'.replace(':id', res.payroll_run.id));
                                        $('#resultCard').removeClass('hidden');
                                        Swal.fire({ icon: 'success', title: 'Payroll Generated!', text: res.message });
                                    } else {
                                        Swal.fire('Error', res.message, 'error');
                                    }
                                    resolve();
                                },
                                error: function(xhr) {
                                    $('#loadingState').addClass('hidden');
                                    let msg = 'Server error occurred';
                                    if (xhr.responseJSON?.message) msg = xhr.responseJSON.message;
                                    Swal.fire('Error', msg, 'error');
                                    resolve();
                                }
                            });
                        });
                    },
                    allowOutsideClick: false
                });
            });

            function resetForm() {
                $('#fiscal_year_id').val('');
                $('#run_year').val(new Date().getFullYear());
                $('#run_month').val('');
                $('#run_type').val('Regular');
                $('#run_label').val('');
                $('#resultCard').addClass('hidden');
            }
        </script>
    @endpush
</x-app-layout>