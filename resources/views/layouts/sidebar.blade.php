<div class="nav-tooltip" id="navTooltip"></div>

<!-- ========== SIDEBAR ========== -->
<aside id="sidebar" class="w-52 bg-white border-r border-gray-200 flex flex-col flex-shrink-0 z-30 overflow-hidden">

    <!-- Logo -->
    <div class="h-[60px] flex items-center justify-between px-3.5 border-b border-blue-300 flex-shrink-0">
        <div class="flex items-center gap-2 overflow-hidden">
            <!-- A icon circle matching BookMe HRM -->
            <div
                class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-600 to-blue-800 flex items-center justify-center flex-shrink-0 shadow-sm">
                <span class="text-white font-bold text-sm leading-none">A</span>
            </div>
            <span class="logo-text font-semibold text-gray-800 text-[20px] whitespace-nowrap">BookMe HRM</span>
        </div>


    </div>

    <!-- Search Box -->
    <div id="searchBox" class="px-2.5 py-2.5 border-b border-gray-100 flex-shrink-0">
        <div
            class="flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-lg px-3  focus-within:border-blue-400 focus-within:ring-1 focus-within:ring-blue-100 transition-all">
            <i class="fas fa-search text-gray-400 text-xs flex-shrink-0"></i>
            <input id="sidebarSearch" type="text" placeholder="Search menus..."
                class="bg-transparent border-0 outline-none focus:outline-none focus:ring-0 text-[13px] text-gray-700 placeholder-gray-400 w-full" />
            <button id="searchClear" class="hidden text-gray-300 hover:text-gray-500 transition-colors">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>
        <p id="searchEmptyMsg" class="hidden text-[11px] text-gray-400 mt-1.5 px-1">No matching items found</p>
    </div>

    <!-- Navigation -->
    <nav id="sideNav" class="flex-1 overflow-y-auto overflow-x-hidden py-2.5 px-2">

        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}"
            class="nav-item flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-150 mb-0.5 
    {{ request()->routeIs('dashboard') ? 'bg-[#1e3a8a] text-white active' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-800' }}"
            data-label="Dashboard">
            <i class="fas fa-th-large w-4 text-center flex-shrink-0 text-base"></i>
            <span class="nav-label font-bold">Dashboard</span>
        </a>

        @permission('employees.view')
            <!-- Employees (submenu) -->
            <div class="mb-0.5">
                <button
                    class="nav-item has-sub w-full flex items-center gap-2.5 px-3 py-2 rounded-lg text-gray-500 hover:bg-gray-50 hover:text-gray-800 text-sm font-medium transition-colors duration-150"
                    data-label="Employees" data-sub="sub-emp">
                    <i class="fas fa-users w-4 text-center flex-shrink-0 text-base"></i>
                    <span class="nav-label flex-1 text-left ">Employees</span>
                    <i class="nav-chevron fas fa-chevron-down text-[10px] flex-shrink-0"></i>
                </button>
                <div class="submenu sub-indent" id="sub-emp">
                    @permission('employees.create')
                        <a href="/employees/create/step-1" wire:navigate
                            class="flex items-center gap-2.5 pl-9 pr-3 py-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-50 text-[13px] transition-colors">
                            <i class="fas fa-user-plus w-3.5 text-center"></i><span>Add Employee</span>
                        </a>
                    @endpermission
                    @permission('employees.view')
                        <a href="/employees"
                            class="flex items-center gap-2.5 pl-9 pr-3 py-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-50 text-[13px] transition-colors">
                            <i class="fas fa-list w-3.5 text-center"></i><span>Employee List</span>
                        </a>
                    @endpermission
                    @permission('departments.view')
                        <a href="/departments"
                            class="flex items-center gap-2.5 pl-9 pr-3 py-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-50 text-[13px] transition-colors">
                            <i class="fas fa-sitemap w-3.5 text-center"></i><span>Departments</span>
                        </a>
                    @endpermission
                    @permission('skills.view')
                        <a href="/skill-categories"
                            class="flex items-center gap-2.5 pl-9 pr-3 py-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-50 text-[13px] transition-colors">
                            <i class="fas fa-code-branch w-3.5 text-center"></i><span>Skill Categories</span>
                        </a>
                    @endpermission
                    @permission('weekends.view')
                        <a href="/employee-weekends"
                            class="flex items-center gap-2.5 pl-9 pr-3 py-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-50 text-[13px] transition-colors">
                            <i class="fas fa-calendar-week w-3.5 text-center"></i><span>Employee Weekends</span>
                        </a>
                    @endpermission
                    @permission('attendance-rules.view')
                        <a href="/employee-attendance-rules"
                            class="flex items-center gap-2.5 pl-9 pr-3 py-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-50 text-[13px] transition-colors">
                            <i class="fas fa-gavel w-3.5 text-center"></i><span>Attendance Rules</span>
                        </a>
                    @endpermission
                </div>
            </div>
        @endpermission

        @permission('shifts.view')
            <!-- Shifts -->
            <a href="/shifts"
                class="nav-item flex items-center gap-2.5 px-3 py-2 rounded-lg text-gray-500 hover:bg-gray-50 hover:text-gray-800 text-sm font-medium transition-colors duration-150 mb-0.5"
                data-label="Shifts">
                <i class="fas fa-clock w-4 text-center flex-shrink-0 text-base"></i>
                <span class="nav-label">Shifts</span>
            </a>
        @endpermission

        @permission('notices.view')
            <!-- Notice (submenu) -->
            <div class="mb-0.5">
                <button
                    class="nav-item has-sub w-full flex items-center gap-2.5 px-3 py-2 rounded-lg text-gray-500 hover:bg-gray-50 hover:text-gray-800 text-sm font-medium transition-colors duration-150"
                    data-label="Notice" data-sub="sub-ntc">
                    <i class="fa-solid fa-bullhorn w-4 text-center flex-shrink-0 text-base"></i>
                    <span class="nav-label flex-1 text-left">Notice</span>
                    <i class="nav-chevron fas fa-chevron-down text-[10px] flex-shrink-0"></i>
                </button>
                <div class="submenu sub-indent" id="sub-ntc">
                    @permission('notices.view')
                        <a href="{{ route('notice.list') }}"
                            class="flex items-center gap-2.5 pl-9 pr-3 py-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-50 text-[13px] transition-colors">
                            <i class="fas fa-list w-3.5 text-center"></i><span>Notice</span>
                        </a>
                    @endpermission
                    @permission('notices.manage')
                        <a href="{{ route('notice.manage') }}"
                            class="flex items-center gap-2.5 pl-9 pr-3 py-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-50 text-[13px] transition-colors">
                            <i class="fas fa-cog w-3.5 text-center"></i><span>Manage Notice</span>
                        </a>
                    @endpermission
                </div>
            </div>
        @endpermission

        @permission('leave.apply')
            <!-- Leave (submenu) -->
            <div class="mb-0.5">
                <button
                    class="nav-item has-sub w-full flex items-center gap-2.5 px-3 py-2 rounded-lg text-gray-500 hover:bg-gray-50 hover:text-gray-800 text-sm font-medium transition-colors duration-150"
                    data-label="Leave" data-sub="sub-leave">
                    <i class="fas fa-umbrella-beach w-4 text-center flex-shrink-0 text-base"></i>
                    <span class="nav-label flex-1 text-left">Leave</span>
                    <i class="nav-chevron fas fa-chevron-down text-[10px] flex-shrink-0"></i>
                </button>
                <div class="submenu sub-indent" id="sub-leave">
                    @permission('leave.apply')
                        <a href="{{ route('leave-applications.create') }}"
                            class="flex items-center gap-2.5 pl-9 pr-3 py-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-50 text-[13px] transition-colors">
                            <i class="fas fa-paper-plane w-3.5 text-center"></i><span>Apply Leave</span>
                        </a>
                    @endpermission
                    @permission('leave.my')
                        <a href="{{ route('leave-applications.my') }}"
                            class="flex items-center gap-2.5 pl-9 pr-3 py-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-50 text-[13px] transition-colors">
                            <i class="fas fa-user-check w-3.5 text-center"></i><span>My Leave</span>
                        </a>
                    @endpermission
                    @permission('leave.view-all')
                        <a href="{{ route('leave-applications.index') }}"
                            class="flex items-center gap-2.5 pl-9 pr-3 py-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-50 text-[13px] transition-colors">
                            <i class="fas fa-list w-3.5 text-center"></i><span>All Leaves (Admin)</span>
                        </a>
                    @endpermission
                    @permission('leave.manage-types')
                        <a href="{{ route('leave-types.index') }}"
                            class="flex items-center gap-2.5 pl-9 pr-3 py-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-50 text-[13px] transition-colors">
                            <i class="fas fa-tag w-3.5 text-center"></i><span>Leave Types</span>
                        </a>
                    @endpermission
                    @permission('leave.manage-balance')
                        <a href="{{ route('employee-leave-balances.index') }}"
                            class="flex items-center gap-2.5 pl-9 pr-3 py-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-50 text-[13px] transition-colors">
                            <i class="fas fa-scale-balanced w-3.5 text-center"></i><span>Leave Balance</span>
                        </a>
                    @endpermission
                    @permission('leave.encashment')
                        <a href="{{ route('leave-encashment.index') }}"
                            class="flex items-center gap-2.5 pl-9 pr-3 py-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-50 text-[13px] transition-colors">
                            <i class="fas fa-money-bill-wave w-3.5 text-center"></i><span>Leave Encashment</span>
                        </a>
                    @endpermission
                </div>
            </div>
        @endpermission

        @permission('holidays.view')
            <!-- Holiday (submenu) -->
            <div class="mb-0.5">
                <button
                    class="nav-item has-sub w-full flex items-center gap-2.5 px-3 py-2 rounded-lg text-gray-500 hover:bg-gray-50 hover:text-gray-800 text-sm font-medium transition-colors duration-150"
                    data-label="Holiday" data-sub="sub-hol">
                    <i class="fas fa-calendar-day w-4 text-center flex-shrink-0 text-base"></i>
                    <span class="nav-label flex-1 text-left">Holiday</span>
                    <i class="nav-chevron fas fa-chevron-down text-[10px] flex-shrink-0"></i>
                </button>
                <div class="submenu sub-indent" id="sub-hol">
                    @permission('holidays.view')
                        <a href="/holidays"
                            class="flex items-center gap-2.5 pl-9 pr-3 py-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-50 text-[13px] transition-colors">
                            <i class="fas fa-list w-3.5 text-center"></i><span>Holidays</span>
                        </a>
                    @endpermission
                    @permission('holidays.calendar')
                        <a href="/holiday-calendar"
                            class="flex items-center gap-2.5 pl-9 pr-3 py-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-50 text-[13px] transition-colors">
                            <i class="fas fa-calendar-alt w-3.5 text-center"></i><span>Holiday Calendar</span>
                        </a>
                    @endpermission
                    @permission('holidays.assign')
                        <a href="/holiday-assignments"
                            class="flex items-center gap-2.5 pl-9 pr-3 py-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-50 text-[13px] transition-colors">
                            <i class="fas fa-user-check w-3.5 text-center"></i><span>Assign Holidays</span>
                        </a>
                    @endpermission
                </div>
            </div>
        @endpermission

        @permission('attendance.view')
            <div class="mb-0.5">
                <button
                    class="nav-item has-sub w-full flex items-center gap-2.5 px-3 py-2 rounded-lg text-gray-500 hover:bg-gray-50 hover:text-gray-800 text-sm font-medium transition-colors duration-150"
                    data-label="Attendance" data-sub="sub-att">
                    <i class="fas fa-users w-4 text-center flex-shrink-0 text-base"></i>
                    <span class="nav-label flex-1 text-left ">Attendance</span>
                    <i class="nav-chevron fas fa-chevron-down text-[10px] flex-shrink-0"></i>
                </button>
                <div class="submenu sub-indent" id="sub-att">
                    @permission('attendance.create')
                        <a href="/attendances/create" wire:navigate
                            class="flex items-center gap-2.5 pl-9 pr-3 py-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-50 text-[13px] transition-colors">
                            <i class="fas fa-user-plus w-3.5 text-center"></i><span>Add Attendance</span>
                        </a>
                    @endpermission
                    @permission('attendance.view')
                        <a href="/attendances"
                            class="flex items-center gap-2.5 pl-9 pr-3 py-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-50 text-[13px] transition-colors">
                            <i class="fas fa-list w-3.5 text-center"></i><span>Attendance List</span>
                        </a>
                    @endpermission
                    @permission('attendance.devices')
                        <a href="/attendance/devices"
                            class="flex items-center gap-2.5 pl-9 pr-3 py-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-50 text-[13px] transition-colors">
                            <i class="fas fa-microchip w-3.5 text-center"></i><span>Attendance Devices</span>
                        </a>
                    @endpermission
                </div>
            </div>
        @endpermission

        @permission('reports.attendance')
            <!-- Reports (submenu) -->
            <div class="mb-0.5">
                <button
                    class="nav-item has-sub w-full flex items-center gap-2.5 px-3 py-2 rounded-lg text-gray-500 hover:bg-gray-50 hover:text-gray-800 text-sm font-medium transition-colors duration-150"
                    data-label="Reports" data-sub="sub-rep">
                    <i class="fas fa-chart-bar w-4 text-center flex-shrink-0 text-base"></i>
                    <span class="nav-label flex-1 text-left">Reports</span>
                    <i class="nav-chevron fas fa-chevron-down text-[10px] flex-shrink-0"></i>
                </button>
                <div class="submenu sub-indent" id="sub-rep">
                    @permission('reports.attendance')
                        <a href="/attendance/report"
                            class="flex items-center gap-2.5 pl-9 pr-3 py-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-50 text-[13px] transition-colors">
                            <i class="fas fa-calendar-check w-3.5 text-center"></i><span>Attendance Report</span>
                        </a>
                    @endpermission
                    @permission('reports.attendance')
                        <a href="/attendance/overtime-report"
                            class="flex items-center gap-2.5 pl-9 pr-3 py-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-50 text-[13px] transition-colors">
                            <i class="fas fa-clock w-3.5 text-center"></i><span>Overtime Report</span>
                        </a>
                    @endpermission
                    @permission('reports.payroll')
                        <a href="/payroll-runs"
                            class="flex items-center gap-2.5 pl-9 pr-3 py-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-50 text-[13px] transition-colors">
                            <i class="fas fa-money-bill-wave w-3.5 text-center"></i><span>Payroll Report</span>
                        </a>
                    @endpermission
                    @permission('reports.attendance')
                        <a href="/employee-report"
                            class="flex items-center gap-2.5 pl-9 pr-3 py-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-50 text-[13px] transition-colors">
                            <i class="fas fa-user w-3.5 text-center"></i><span>Employee Report</span>
                        </a>
                    @endpermission
                    @permission('reports.hr-analytics')
                        <a href="#"
                            class="flex items-center gap-2.5 pl-9 pr-3 py-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-50 text-[13px] transition-colors">
                            <i class="fas fa-user-tie w-3.5 text-center"></i><span>HR Analytics</span>
                        </a>
                    @endpermission
                </div>
            </div>
        @endpermission

        @permission('kpi.dashboard')
            <!-- KPI (submenu) -->
            <div class="mb-0.5">
                <button
                    class="nav-item has-sub w-full flex items-center gap-2.5 px-3 py-2 rounded-lg text-gray-500 hover:bg-gray-50 hover:text-gray-800 text-sm font-medium transition-colors duration-150"
                    data-label="KPI" data-sub="sub-kpi">
                    <i class="fas fa-trophy w-4 text-center flex-shrink-0 text-base"></i>
                    <span class="nav-label flex-1 text-left">KPI</span>
                    <i class="nav-chevron fas fa-chevron-down text-[10px] flex-shrink-0"></i>
                </button>
                <div class="submenu sub-indent" id="sub-kpi">
                    @permission('kpi.dashboard')
                        <a href="{{ route('kpi.dashboard') }}"
                            class="flex items-center gap-2.5 pl-9 pr-3 py-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-50 text-[13px] transition-colors">
                            <i class="fas fa-chart-line w-3.5 text-center"></i><span>KPI Dashboard</span>
                        </a>
                    @endpermission
                    @permission('kpi.daily')
                        <a href="{{ route('kpi.daily') }}"
                            class="flex items-center gap-2.5 pl-9 pr-3 py-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-50 text-[13px] transition-colors">
                            <i class="fas fa-calendar-day w-3.5 text-center"></i><span>Daily Performance</span>
                        </a>
                    @endpermission
                    @permission('kpi.monthly')
                        <a href="{{ route('kpi.monthly') }}"
                            class="flex items-center gap-2.5 pl-9 pr-3 py-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-50 text-[13px] transition-colors">
                            <i class="fas fa-calendar-alt w-3.5 text-center"></i><span>Monthly Performance</span>
                        </a>
                    @endpermission
                    @permission('kpi.tasks')
                        <a href="{{ route('kpi.tasks.index') }}"
                            class="flex items-center gap-2.5 pl-9 pr-3 py-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-50 text-[13px] transition-colors">
                            <i class="fas fa-tasks w-3.5 text-center"></i><span>Tasks</span>
                        </a>
                    @endpermission
                    @permission('kpi.reviews')
                        <a href="{{ route('kpi.reviews.index') }}"
                            class="flex items-center gap-2.5 pl-9 pr-3 py-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-50 text-[13px] transition-colors">
                            <i class="fas fa-star w-3.5 text-center"></i><span>Monthly Reviews</span>
                        </a>
                    @endpermission
                    @permission('kpi.settings')
                        <a href="{{ route('kpi.settings') }}"
                            class="flex items-center gap-2.5 pl-9 pr-3 py-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-50 text-[13px] transition-colors">
                            <i class="fas fa-cog w-3.5 text-center"></i><span>KPI Settings</span>
                        </a>
                    @endpermission
                </div>
            </div>
        @endpermission

        @permission('salary.components')
            <!-- Salary (submenu) -->
            <div class="mb-0.5">
                <button
                    class="nav-item has-sub w-full flex items-center gap-2.5 px-3 py-2 rounded-lg text-gray-500 hover:bg-gray-50 hover:text-gray-800 text-sm font-medium transition-colors duration-150"
                    data-label="Salary" data-sub="sub-sal">
                    <i class="fas fa-money-bill-wave w-4 text-center flex-shrink-0 text-base"></i>
                    <span class="nav-label flex-1 text-left">Salary</span>
                    <i class="nav-chevron fas fa-chevron-down text-[10px] flex-shrink-0"></i>
                </button>
                <div class="submenu sub-indent" id="sub-sal">
                    @permission('salary.components')
                        <a href="{{ route('salary-components.index') }}"
                            class="flex items-center gap-2.5 pl-9 pr-3 py-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-50 text-[13px] transition-colors">
                            <i class="fas fa-cogs w-3.5 text-center"></i><span>Salary Components</span>
                        </a>
                    @endpermission
                    @permission('salary.structure')
                        <a href="{{ route('employee-salary-structure.index') }}"
                            class="flex items-center gap-2.5 pl-9 pr-3 py-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-50 text-[13px] transition-colors">
                            <i class="fas fa-file-invoice-dollar w-3.5 text-center"></i><span>Employee Salary Structure</span>
                        </a>
                    @endpermission
                    @permission('salary.payroll-view')
                        <a href="{{ route('payroll-runs.index') }}"
                            class="flex items-center gap-2.5 pl-9 pr-3 py-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-50 text-[13px] transition-colors">
                            <i class="fas fa-list w-3.5 text-center"></i><span>All Payroll Runs</span>
                        </a>
                    @endpermission
                    @permission('salary.payroll-generate')
                        <a href="{{ route('payroll-runs.generate') }}"
                            class="flex items-center gap-2.5 pl-9 pr-3 py-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-50 text-[13px] transition-colors">
                            <i class="fas fa-plus-circle w-3.5 text-center"></i><span>Generate Payroll</span>
                        </a>
                    @endpermission
                    @permission('salary.payment-list')
                        <a href="{{ route('payment-list.index') }}"
                            class="flex items-center gap-2.5 pl-9 pr-3 py-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-50 text-[13px] transition-colors">
                            <i class="fas fa-credit-card w-3.5 text-center"></i><span>Payment List</span>
                        </a>
                    @endpermission
                </div>
            </div>
        @endpermission

        @permission('loan.apply')
            <!-- Loan (submenu) -->
            <div class="mb-0.5">
                <button
                    class="nav-item has-sub w-full flex items-center gap-2.5 px-3 py-2 rounded-lg text-gray-500 hover:bg-gray-50 hover:text-gray-800 text-sm font-medium transition-colors duration-150"
                    data-label="Loan" data-sub="sub-loan">
                    <i class="fas fa-hand-holding-usd w-4 text-center flex-shrink-0 text-base"></i>
                    <span class="nav-label flex-1 text-left">Loan</span>
                    <i class="nav-chevron fas fa-chevron-down text-[10px] flex-shrink-0"></i>
                </button>
                <div class="submenu sub-indent" id="sub-loan">
                    @permission('loan.apply')
                        <a href="{{ route('loan.create') }}"
                            class="flex items-center gap-2.5 pl-9 pr-3 py-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-50 text-[13px] transition-colors">
                            <i class="fas fa-paper-plane w-3.5 text-center"></i><span>Apply Loan</span>
                        </a>
                    @endpermission
                    @permission('loan.my')
                        <a href="{{ route('loan.my') }}"
                            class="flex items-center gap-2.5 pl-9 pr-3 py-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-50 text-[13px] transition-colors">
                            <i class="fas fa-user-check w-3.5 text-center"></i><span>My Loans</span>
                        </a>
                    @endpermission
                    @permission('loan.view-all')
                        <a href="{{ route('loan.index') }}"
                            class="flex items-center gap-2.5 pl-9 pr-3 py-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-50 text-[13px] transition-colors">
                            <i class="fas fa-list w-3.5 text-center"></i><span>All Loans (Admin)</span>
                        </a>
                    @endpermission
                </div>
            </div>
        @endpermission

        @permission('settings.users')
            <!-- Settings (submenu) -->
            <div class="mb-0.5">
                <button
                    class="nav-item has-sub w-full flex items-center gap-2.5 px-3 py-2 rounded-lg text-gray-500 hover:bg-gray-50 hover:text-gray-800 text-sm font-medium transition-colors duration-150"
                    data-label="Settings" data-sub="sub-set">
                    <i class="fas fa-cog w-4 text-center flex-shrink-0 text-base"></i>
                    <span class="nav-label flex-1 text-left">Settings</span>
                    <i class="nav-chevron fas fa-chevron-down text-[10px] flex-shrink-0"></i>
                </button>
                <div class="submenu sub-indent" id="sub-set">
                    @permission('settings.users')
                        <a href="{{ route('users.index') }}"
                            class="flex items-center gap-2.5 pl-9 pr-3 py-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-50 text-[13px] transition-colors">
                            <i class="fas fa-user-shield w-3.5 text-center"></i><span>User Management</span>
                        </a>
                    @endpermission
                    @permission('settings.roles')
                        <a href="{{ route('roles.index') }}"
                            class="flex items-center gap-2.5 pl-9 pr-3 py-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-50 text-[13px] transition-colors">
                            <i class="fas fa-lock w-3.5 text-center"></i><span>Roles</span>
                        </a>
                    @endpermission
                    @permission('settings.roles')
                        <a href="{{ route('permissions.index') }}"
                            class="flex items-center gap-2.5 pl-9 pr-3 py-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-50 text-[13px] transition-colors">
                            <i class="fas fa-key w-3.5 text-center"></i><span>Permissions</span>
                        </a>
                    @endpermission
                    @permission('settings.system')
                        <a href="#"
                            class="flex items-center gap-2.5 pl-9 pr-3 py-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-50 text-[13px] transition-colors">
                            <i class="fas fa-sliders-h w-3.5 text-center"></i><span>System Settings</span>
                        </a>
                    @endpermission
                    @permission('settings.fiscal-years')
                        <a href="{{ route('fiscal-years.index') }}"
                            class="flex items-center gap-2.5 pl-9 pr-3 py-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-50 text-[13px] transition-colors">
                            <i class="fas fa-calendar-alt w-3.5 text-center"></i><span>Fiscal Years</span>
                        </a>
                    @endpermission
                </div>
            </div>
        @endpermission

        @permission('admin.company')
            <div class="mb-0.5">
                <button
                    class="nav-item has-sub w-full flex items-center gap-2.5 px-3 py-2 rounded-lg text-gray-500 hover:bg-gray-50 hover:text-gray-800 text-sm font-medium transition-colors duration-150"
                    data-label="Administration" data-sub="sub-admin">
                    <i class="fa-solid fa-user-tie w-4 text-center flex-shrink-0 text-base"></i>
                    <span class="nav-label flex-1 text-left">Administration</span>
                    <i class="nav-chevron fas fa-chevron-down text-[10px] flex-shrink-0"></i>
                </button>
                <div class="submenu sub-indent" id="sub-admin">
                    @permission('admin.company')
                        <a href="#"
                            class="flex items-center gap-2.5 pl-9 pr-3 py-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-50 text-[13px] transition-colors">
                            <i class="fas fa-user-shield w-3.5 text-center"></i><span>Company Setup</span>
                        </a>
                    @endpermission
                    @permission('departments.manage')
                        <a href="/departments"
                            class="flex items-center gap-2.5 pl-9 pr-3 py-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-50 text-[13px] transition-colors">
                            <i class="fas fa-lock w-3.5 text-center"></i><span>Department</span>
                        </a>
                    @endpermission
                    @permission('admin.branches')
                        <a href="/branches"
                            class="flex items-center gap-2.5 pl-9 pr-3 py-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-50 text-[13px] transition-colors">
                            <i class="fas fa-sliders-h w-3.5 text-center"></i><span>Branch</span>
                        </a>
                    @endpermission
                    @permission('admin.salary-grades')
                        <a href="/salarygrades"
                            class="flex items-center gap-2.5 pl-9 pr-3 py-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-50 text-[13px] transition-colors">
                            <i class="fas fa-sliders-h w-3.5 text-center"></i><span>Salary Grade</span>
                        </a>
                    @endpermission
                    @permission('admin.designations')
                        <a href="/designations"
                            class="flex items-center gap-2.5 pl-9 pr-3 py-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-50 text-[13px] transition-colors">
                            <i class="fas fa-sliders-h w-3.5 text-center"></i><span>Designation</span>
                        </a>
                    @endpermission
                </div>
            </div>
        @endpermission

    </nav>
</aside>
