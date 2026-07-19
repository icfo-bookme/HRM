<x-app-layout>
    <div class="p-4 space-y-5">

        {{-- ====== SEARCH & FILTER SECTION ====== --}}
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <div class="flex flex-col md:flex-row md:items-end gap-4">
                <div class="flex flex-col w-full md:w-1/3 relative">
                    <label class="font-semibold text-sm text-slate-700 block mb-1">Search Employee</label>
                    <input type="text" id="employeeAutocomplete" placeholder="Type employee name or code..."
                        class="w-full border border-slate-300 rounded-md p-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none"
                        autocomplete="off">
                    <input type="hidden" id="selectedEmployeeId" value="">
                    <div id="autocompleteResults" class="hidden absolute z-[9999] mt-1 w-full bg-white border border-gray-200 rounded-md shadow-lg max-h-60 overflow-y-auto" style="top: 100%; left: 0;"></div>
                </div>
                <div class="w-full md:w-auto flex items-end gap-2">
                    <button id="btnResetSearch"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                        <i class="fas fa-undo mr-1"></i> Reset
                    </button>
                </div>
            </div>

        {{-- ====== EMPLOYEE INFO CARD ====== --}}

        {{-- ====== EMPLOYEE INFO CARD ====== --}}
        <div id="employeeInfoCard" class="hidden bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-blue-800 px-6 py-5">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center text-white text-2xl font-bold" id="empAvatar">?</div>
                    <div class="text-white">
                        <h2 class="text-xl font-bold" id="empName">-</h2>
                        <p class="text-blue-200 text-sm" id="empCode">-</p>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 p-6 bg-gray-50 border-b border-gray-200">
                <div><span class="text-xs text-gray-500 block">Department</span><span class="font-medium text-gray-800" id="empDept">-</span></div>
                <div><span class="text-xs text-gray-500 block">Designation</span><span class="font-medium text-gray-800" id="empDesig">-</span></div>
                <div><span class="text-xs text-gray-500 block">Date of Birth</span><span class="font-medium text-gray-800" id="empDob">-</span></div>
                <div><span class="text-xs text-gray-500 block">Joining Date</span><span class="font-medium text-gray-800" id="empJoining">-</span></div>
                <div><span class="text-xs text-gray-500 block">Phone</span><span class="font-medium text-gray-800" id="empPhone">-</span></div>
                <div><span class="text-xs text-gray-500 block">Email</span><span class="font-medium text-gray-800" id="empEmail">-</span></div>
                <div><span class="text-xs text-gray-500 block">Bank</span><span class="font-medium text-gray-800" id="empBank">-</span></div>
                <div><span class="text-xs text-gray-500 block">Account / Payment</span><span class="font-medium text-gray-800" id="empAccount">-</span></div>
                <div><span class="text-xs text-gray-500 block">Total Income</span><span class="font-medium text-green-600 font-bold" id="empTotalIncome">$0.00</span></div>
            </div>
        </div>

        {{-- ====== ATTENDANCE CALENDAR ====== --}}
        <div id="attendanceSection" class="hidden bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-gradient-to-r from-blue-50 to-indigo-50">
                <h3 class="font-semibold text-gray-800 flex items-center gap-2"><i class="fa-solid fa-calendar-check text-blue-600"></i> Attendance Calendar</h3>
                <div class="flex items-center gap-2">
                    <select id="attMonth" class="text-xs border border-gray-300 rounded px-2 py-1">
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $m == now()->month ? 'selected' : '' }}>{{ DateTime::createFromFormat('!m', $m)->format('F') }}</option>
                        @endfor
                    </select>
                    <select id="attYear" class="text-xs border border-gray-300 rounded px-2 py-1">
                        @for ($y = now()->year - 2; $y <= now()->year + 1; $y++)
                            <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </div>
            <div class="p-4">
                <div class="flex flex-wrap gap-3 mb-3 text-xs">
                    <span class="inline-flex items-center gap-1"><span class="w-3 h-3 rounded bg-green-500"></span> P</span>
                    <span class="inline-flex items-center gap-1"><span class="w-3 h-3 rounded bg-orange-500"></span> LP</span>
                    <span class="inline-flex items-center gap-1"><span class="w-3 h-3 rounded bg-yellow-500"></span> EL</span>
                    <span class="inline-flex items-center gap-1"><span class="w-3 h-3 rounded bg-red-500"></span> A</span>
                    <span class="inline-flex items-center gap-1"><span class="w-3 h-3 rounded bg-blue-500"></span> H</span>
                    <span class="inline-flex items-center gap-1"><span class="w-3 h-3 rounded bg-purple-500"></span> W</span>
                    <span class="ml-auto font-semibold text-orange-600" id="lateCount">Late: 0</span>
                    <span class="font-semibold text-yellow-600" id="earlyCount">Early Out: 0</span>
                </div>
                <div id="attendanceCalendar" class="min-h-[200px]"></div>
            </div>
        </div>

        {{-- ====== OVERTIME HISTORY ====== --}}
        <div id="overtimeSection" class="hidden bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-gradient-to-r from-purple-50 to-pink-50">
                <h3 class="font-semibold text-gray-800 flex items-center gap-2"><i class="fa-solid fa-clock text-purple-600"></i> Overtime History</h3>
                <div class="flex items-center gap-2">
                    <select id="otMonth" class="text-xs border border-gray-300 rounded px-2 py-1">
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $m == now()->month ? 'selected' : '' }}>{{ DateTime::createFromFormat('!m', $m)->format('F') }}</option>
                        @endfor
                    </select>
                    <select id="otYear" class="text-xs border border-gray-300 rounded px-2 py-1">
                        @for ($y = now()->year - 2; $y <= now()->year + 1; $y++)
                            <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </div>
            <div class="p-4" id="overtimeContent"><p class="text-gray-400 text-sm text-center py-4">Select an employee.</p></div>
        </div>

        {{-- ====== LOAN HISTORY ====== --}}
        <div id="loanSection" class="hidden bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-gradient-to-r from-red-50 to-pink-50">
                <h3 class="font-semibold text-gray-800 flex items-center gap-2"><i class="fa-solid fa-hand-holding-usd text-red-600"></i> Loan History</h3>
            </div>
            <div class="p-4" id="loanContent"><p class="text-gray-400 text-sm text-center py-4">Select an employee.</p></div>
        </div>

        {{-- ====== SALARY & INCOME ====== --}}
        <div id="salarySection" class="hidden bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-gradient-to-r from-green-50 to-emerald-50">
                <h3 class="font-semibold text-gray-800 flex items-center gap-2"><i class="fa-solid fa-money-bill-wave text-green-600"></i> Salary & Income</h3>
                <div class="flex items-center gap-2">
                    <select id="salYear" class="text-xs border border-gray-300 rounded px-2 py-1">
                        @for ($y = now()->year - 2; $y <= now()->year + 1; $y++)
                            <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </div>
            <div class="p-4" id="salaryContent"><p class="text-gray-400 text-sm text-center py-4">Select an employee.</p></div>
        </div>

        {{-- ====== KPI PERFORMANCE ====== --}}
        <div id="kpiSection" class="hidden bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-gradient-to-r from-yellow-50 to-orange-50">
                <h3 class="font-semibold text-gray-800 flex items-center gap-2"><i class="fa-solid fa-trophy text-yellow-600"></i> KPI Performance</h3>
                <div class="flex items-center gap-2">
                    <select id="kpiYear" class="text-xs border border-gray-300 rounded px-2 py-1">
                        @for ($y = now()->year - 2; $y <= now()->year + 1; $y++)
                            <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </div>
            <div class="p-4" id="kpiContent"><p class="text-gray-400 text-sm text-center py-4">Select an employee.</p></div>
        </div>

    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.15/index.global.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.15/index.global.min.js"></script>
        <script>
            $(document).ready(function() {
                let selectedEmployeeId = null;
                let calendar = null;
                let calendarEl = document.getElementById('attendanceCalendar');
                let searchTimeout = null;

                // ====== AUTOCOMPLETE SEARCH ======
                $('#employeeAutocomplete').on('input', function() {
                    clearTimeout(searchTimeout);
                    let keyword = $(this).val().trim();

                    if (keyword.length < 2) {
                        $('#autocompleteResults').addClass('hidden').empty();
                        return;
                    }

                    searchTimeout = setTimeout(function() {
                        $.ajax({
                            url: '{{ route('employee.report.search') }}',
                            data: { keyword },
                            success: function(res) {
                                let results = $('#autocompleteResults');
                                results.empty();

                                if (res.status && res.employees.length > 0) {
                                    $.each(res.employees, function(i, emp) {
                                        results.append(`
                                            <div class="px-3 py-2 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-0 flex items-center gap-3 autocomplete-item"
                                                 data-id="${emp.id}" data-name="${emp.full_name}" data-code="${emp.employee_code}">
                                                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold text-sm">${emp.full_name.charAt(0).toUpperCase()}</div>
                                                <div>
                                                    <div class="font-medium text-gray-800 text-sm">${emp.full_name}</div>
                                                    <div class="text-xs text-gray-500">${emp.employee_code} · ${emp.department} · ${emp.designation}</div>
                                                </div>
                                            </div>
                                        `);
                                    });
                                    results.removeClass('hidden');
                                } else {
                                    results.html('<div class="px-3 py-2 text-sm text-gray-400">No employees found</div>').removeClass('hidden');
                                }
                            }
                        });
                    }, 300);
                });

                $(document).on('click', '.autocomplete-item', function() {
                    let id = $(this).data('id');
                    let name = $(this).data('name');
                    let code = $(this).data('code');
                    $('#employeeAutocomplete').val(name + ' (' + code + ')');
                    $('#selectedEmployeeId').val(id);
                    $('#autocompleteResults').addClass('hidden').empty();

                    selectEmployee(id);
                });

                $(document).on('click', function(e) {
                    if (!$(e.target).closest('#employeeAutocomplete, #autocompleteResults').length) {
                        $('#autocompleteResults').addClass('hidden');
                    }
                });

                // ====== Select Employee ======
                function selectEmployee(empId) {
                    selectedEmployeeId = empId;
                    loadEmployeeInfo(empId);
                    loadAttendance(empId);
                    loadOvertime(empId);
                    loadLoan(empId);
                    loadSalary(empId);
                    loadKpi(empId);
                }

                // ====== Load Employee Info ======
                function loadEmployeeInfo(empId) {
                    $.ajax({
                        url: '{{ route('employee.report.search') }}',
                        data: { keyword: empId },
                        success: function(res) {
                            if (res.status && res.employees.length > 0) {
                                let emp = res.employees[0];
                                $('#empName').text(emp.full_name);
                                $('#empCode').text(emp.employee_code);
                                $('#empDept').text(emp.department);
                                $('#empDesig').text(emp.designation);
                                $('#empDob').text(emp.date_of_birth);
                                $('#empJoining').text(emp.joining_date);
                                $('#empPhone').text(emp.phone);
                                $('#empEmail').text(emp.email);
                                $('#empBank').text(emp.bank_name);
                                $('#empAccount').text(emp.account_number + ' (' + emp.payment_method + ')');
                                $('#empTotalIncome').text('$' + (emp.total_income || '0.00'));
                                $('#empAvatar').text(emp.full_name.charAt(0).toUpperCase());
                                $('#employeeInfoCard').removeClass('hidden');
                            }
                        }
                    });
                }

                // ====== Load Attendance ======
                function loadAttendance(empId) {
                    let month = $('#attMonth').val();
                    let year = $('#attYear').val();
                    $.ajax({
                        url: '{{ route('employee.report.attendance') }}',
                        data: { employee_id: empId, month, year },
                        success: function(res) {
                            if (res.status) {
                                $('#lateCount').text('Late: ' + (res.summary?.late_count || 0));
                                $('#earlyCount').text('Early Out: ' + (res.summary?.early_out_count || 0));
                                $('#attendanceSection').removeClass('hidden');
                                if (calendar) {
                                    calendar.removeAllEvents();
                                    calendar.addEventSource(res.events);
                                } else if (calendarEl && typeof FullCalendar !== 'undefined') {
                                    calendar = new FullCalendar.Calendar(calendarEl, {
                                        initialView: 'dayGridMonth',
                                        headerToolbar: { left: '', center: 'title', right: '' },
                                        height: 'auto', events: res.events, displayEventTime: false,
                                    });
                                    calendar.render();
                                }
                            }
                        }
                    });
                }
                $('#attMonth, #attYear').on('change', function() { if (selectedEmployeeId) loadAttendance(selectedEmployeeId); });

                // ====== Load Overtime ======
                function loadOvertime(empId) {
                    let month = $('#otMonth').val();
                    let year = $('#otYear').val();
                    $.ajax({
                        url: '{{ route('employee.report.overtime') }}',
                        data: { employee_id: empId, month, year },
                        success: function(res) {
                            if (res.status) {
                                let html = '';
                                if (res.days.length === 0) {
                                    html = '<p class="text-gray-400 text-sm text-center py-4">No records for ' + res.month_name + '.</p>';
                                } else {
                                    html = `<div class="border border-gray-200 rounded-lg overflow-hidden">
                                        <div class="bg-purple-50 px-4 py-2 flex justify-between items-center border-b border-gray-200">
                                            <span class="font-semibold text-gray-800">${res.month_name}</span>
                                            <span class="text-sm font-bold text-purple-700 bg-purple-100 px-2 py-0.5 rounded">Total OT: ${res.month_total}</span>
                                        </div>
                                        <div class="p-3">
                                            <table class="w-full text-xs">
                                                <thead><tr class="text-gray-500"><th class="text-left pb-1">Date</th><th class="text-center pb-1">Day</th><th class="text-right pb-1">Overtime / Status</th></tr></thead>
                                                <tbody>`;
                                    $.each(res.days, function(j, day) {
                                        let isOt = day.status === 'OT';
                                        let colorClass = isOt ? 'text-purple-700 font-bold' : 'text-gray-500';
                                        let bgClass = isOt ? 'bg-purple-50' : (day.status === 'Present' ? 'bg-green-50' : day.status === 'Absent' ? 'bg-red-50' : '');
                                        html += `<tr class="border-t border-gray-100 ${bgClass}">
                                            <td class="py-1.5">${day.date}</td>
                                            <td class="py-1.5 text-center text-gray-400">${day.day_num}</td>
                                            <td class="py-1.5 text-right ${colorClass}">${day.overtime_display}</td>
                                        </tr>`;
                                    });
                                    html += `</tbody></table></div></div>`;
                                }
                                $('#overtimeContent').html(html);
                                $('#overtimeSection').removeClass('hidden');
                            }
                        }
                        });
                }

                // ====== Load Salary ======
                function loadSalary(empId) {
                    let year = $('#salYear').val();
                    loadYearlySalary(empId, year);
                }

                function loadYearlySalary(empId, year) {
                    $.ajax({
                        url: '{{ route('employee.report.salary') }}',
                        data: { employee_id: empId, year },
                        success: function(res) {
                            if (res.status) {
                                let html = '';

                                if (res.monthly_records && res.monthly_records.length > 0) {
                                    html += `<h4 class="font-semibold text-gray-700 mb-2 text-sm">Payroll History &mdash; ${year}</h4>
                                        <table class="w-full text-sm"><thead><tr class="bg-gray-50 text-gray-600">
                                            <th class="text-left px-4 py-2">Month</th><th class="text-right px-4 py-2">Gross Pay</th>
                                            <th class="text-right px-4 py-2">Deductions</th><th class="text-right px-4 py-2">Net Pay</th>
                                        </tr></thead><tbody>`;
                                    $.each(res.monthly_records, function(i, rec) {
                                        html += `<tr class="border-t border-gray-100 hover:bg-gray-50">
                                            <td class="px-4 py-2 font-medium">${rec.month}</td>
                                            <td class="px-4 py-2 text-right">?${rec.gross_pay}</td>
                                            <td class="px-4 py-2 text-right text-red-600">?${rec.deductions}</td>
                                            <td class="px-4 py-2 text-right font-semibold text-green-600">?${rec.net_pay}</td>
                                        </tr>`;
                                    });
                                    html += `</tbody></table>`;

                                    html += `<div class="text-right text-lg font-bold text-green-700 bg-green-50 px-4 py-3 mt-3 rounded-lg border border-green-200">
                                        Total Income (${year}): ?${res.total_income}</div>`;
                                } else {
                                    html = '<p class="text-gray-400 text-sm text-center py-4">No payroll data found for ' + year + '.</p>';
                                }



                                $('#salaryContent').html(html);
                                $('#salarySection').removeClass('hidden');
                            }
                        }
                    });
                }



                // ====== Load KPI ======
                function loadKpi(empId) {
                    let year = $('#kpiYear').val();
                    $.ajax({
                        url: '{{ route('employee.report.kpi-monthly') }}',
                        data: { employee_id: empId, year },
                        success: function(res) {
                            if (res.status) {
                                let html = '';
                                if (res.scores.length === 0) {
                                    html = '<p class="text-gray-400 text-sm text-center py-4">No KPI records found.</p>';
                                } else {
                                    html = `<table class="w-full text-sm"><thead><tr class="bg-gray-50 text-gray-600">
                                        <th class="text-left px-3 py-2">Month</th><th class="text-center px-3 py-2">Att %</th>
                                        <th class="text-center px-3 py-2">Task %</th><th class="text-center px-3 py-2">Behavior</th>
                                        <th class="text-center px-3 py-2">Overall</th><th class="text-center px-3 py-2">Rating</th><th class="text-center px-3 py-2">Status</th>
                                    </tr></thead><tbody>`;
                                    $.each(res.scores, function(i, s) {
                                        let ratingColor = s.rating >= 4 ? 'bg-green-100 text-green-700' : s.rating >= 3 ? 'bg-blue-100 text-blue-700' : s.rating >= 2 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700';
                                        html += `<tr class="border-t border-gray-100 hover:bg-gray-50">
                                            <td class="px-3 py-2 font-medium">${s.month}</td>
                                            <td class="px-3 py-2 text-center">${s.attendance_percentage ?? '-'}%</td>
                                            <td class="px-3 py-2 text-center">${s.task_percentage ?? '-'}%</td>
                                            <td class="px-3 py-2 text-center">${s.behavior_score ?? '-'}</td>
                                            <td class="px-3 py-2 text-center font-semibold">${s.overall_percentage ?? '-'}%</td>
                                            <td class="px-3 py-2 text-center"><span class="inline-flex px-2 py-0.5 rounded text-xs font-medium ${ratingColor}">${s.rating ?? '-'}</span></td>
                                            <td class="px-3 py-2 text-center"><span class="inline-flex px-2 py-0.5 rounded text-xs font-medium ${s.status === 'Closed' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'}">${s.status}</span></td>
                                        </tr>`;
                                    });
                                    html += `</tbody></table>`;
                                }
                                $('#kpiContent').html(html);
                                $('#kpiSection').removeClass('hidden');
                            }
                        }
                    });
                }


                $('#otMonth, #otYear').on('change', function() { if (selectedEmployeeId) loadOvertime(selectedEmployeeId); });

                // ====== Load Loan ======
                function loadLoan(empId) {
                    $.ajax({
                        url: '{{ route('employee.report.loan') }}',
                        data: { employee_id: empId },
                        success: function(res) {
                            if (res.status) {
                                let html = '';
                                if (res.records.length === 0) {
                                    html = '<p class="text-gray-400 text-sm text-center py-4">No loan records found.</p>';
                                } else {
                                    html = `<table class="w-full text-sm"><thead><tr class="bg-gray-50 text-gray-600">
                                        <th class="text-left px-3 py-2">Loan #</th><th class="text-left px-3 py-2">Type</th>
                                        <th class="text-right px-3 py-2">Amount</th><th class="text-center px-3 py-2">Installments</th>
                                        <th class="text-right px-3 py-2">Remaining</th><th class="text-center px-3 py-2">Status</th><th class="text-left px-3 py-2">Date</th>
                                    </tr></thead><tbody>`;
                                    $.each(res.records, function(i, loan) {
                                        let statusColor = loan.status === 'Disbursed' ? 'bg-green-100 text-green-700' : loan.status === 'Approved' ? 'bg-blue-100 text-blue-700' : loan.status === 'Pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-700';
                                        html += `<tr class="border-t border-gray-100 hover:bg-gray-50">
                                            <td class="px-3 py-2 font-medium">${loan.loan_number}</td>
                                            <td class="px-3 py-2">${loan.loan_type}</td>
                                            <td class="px-3 py-2 text-right font-medium">?${loan.loan_amount}</td>
                                            <td class="px-3 py-2 text-center">${loan.paid_installments}/${loan.total_installments}</td>
                                            <td class="px-3 py-2 text-right text-red-600">?${loan.remaining_amount}</td>
                                            <td class="px-3 py-2 text-center"><span class="inline-flex px-2 py-0.5 rounded text-xs font-medium ${statusColor}">${loan.status}</span></td>
                                            <td class="px-3 py-2">${loan.application_date}</td>
                                        </tr>`;
                                    });
                                    html += `</tbody></table>
                                        <div class="flex justify-between mt-3 bg-red-50 px-4 py-2 rounded-lg border border-red-200 text-sm">
                                            <span class="font-semibold text-red-700">Total Loan Amount: ?${res.total_loan_amount}</span>
                                            <span class="font-semibold text-red-700">Total Remaining: ?${res.total_remaining}</span>
                                        </div>`;
                                }
                                $('#loanContent').html(html);
                                $('#loanSection').removeClass('hidden');
                            }
                        }
                    });
                }

                // Salary & KPI filter change handlers
                $('#salYear').on('change', function() { if (selectedEmployeeId) loadSalary(selectedEmployeeId); });
                $('#kpiYear').on('change', function() { if (selectedEmployeeId) loadKpi(selectedEmployeeId); });

                // ====== Reset ======
                $('#btnResetSearch').on('click', function() {
                    $('#employeeAutocomplete').val('');
                    $('#selectedEmployeeId').val('');
                    $('#autocompleteResults').addClass('hidden').empty();
                    selectedEmployeeId = null;
                    $('#employeeInfoCard, #attendanceSection, #overtimeSection, #loanSection, #salarySection, #kpiSection').addClass('hidden');
                });
            });
        </script>
    @endpush

        @push('styles')
        <style>
            .fc { font-family: inherit; }
            .fc .fc-toolbar-title { font-size: 1rem; font-weight: 600; color: #1e293b; }
            .fc .fc-daygrid-day-number { font-size: 0.8rem; font-weight: 500; color: #475569; padding: 0.3rem 0.3rem 0 0; }
            .fc .fc-daygrid-day.fc-day-today { background-color: #eff6ff; }
            .fc .fc-daygrid-day.fc-day-today .fc-daygrid-day-number { background-color: #1e3a8a; color: white; border-radius: 50%; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; padding: 0; margin: 0.25rem 0.25rem 0 0; font-weight: 600; font-size: 0.75rem; }
            .fc .fc-scrollgrid { border-color: #e2e8f0; border-radius: 0.5rem; }
            .fc .fc-col-header-cell-cushion { font-weight: 600; font-size: 0.7rem; text-transform: uppercase; color: #64748b; padding: 0.5rem 0; }
            .fc .fc-daygrid-day-frame { min-height: 30px; }
            .fc .fc-daygrid-day-events { display: none; }
            .fc .fc-daygrid-bg-harness { display: block !important; }
            .fc .fc-daygrid-day-bg { display: block !important; }
            .fc .fc-bg-event { opacity: 0.8; border-radius: 2px; }
        </style>
    @endpush
</x-app-layout>


test






