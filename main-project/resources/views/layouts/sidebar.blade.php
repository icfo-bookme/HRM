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
                <a href="#"
                    class="flex items-center gap-2.5 pl-9 pr-3 py-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-50 text-[13px] transition-colors">
                    <i class="fas fa-user-plus w-3.5 text-center"></i><span>Add Employee</span>
                </a>
                <a href="#"
                    class="flex items-center gap-2.5 pl-9 pr-3 py-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-50 text-[13px] transition-colors">
                    <i class="fas fa-list w-3.5 text-center"></i><span>Employee List</span>
                </a>
                <a href="#"
                    class="flex items-center gap-2.5 pl-9 pr-3 py-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-50 text-[13px] transition-colors">
                    <i class="fas fa-sitemap w-3.5 text-center"></i><span>Departments</span>
                </a>
            </div>
        </div>

        <!-- Shifts -->
        <a href="#"
            class="nav-item flex items-center gap-2.5 px-3 py-2 rounded-lg text-gray-500 hover:bg-gray-50 hover:text-gray-800 text-sm font-medium transition-colors duration-150 mb-0.5"
            data-label="Shifts">
            <i class="fas fa-clock w-4 text-center flex-shrink-0 text-base"></i>
            <span class="nav-label">Shifts</span>
        </a>

        <!-- Attendance — ACTIVE -->
        <a href="#"
            class="nav-item flex items-center gap-2.5 px-3 py-2 rounded-lg text-gray-500 hover:bg-gray-50 hover:text-gray-800 text-sm font-medium transition-colors duration-150 mb-0.5"
            data-label="Attendance">
            <i class="fas fa-calendar-check w-4 text-center flex-shrink-0 text-base"></i>
            <span class="nav-label">Attendance</span>
        </a>

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
                <a href="#"
                    class="flex items-center gap-2.5 pl-9 pr-3 py-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-50 text-[13px] transition-colors">
                    <i class="fas fa-calendar-check w-3.5 text-center"></i><span>Attendance Report</span>
                </a>
                <a href="#"
                    class="flex items-center gap-2.5 pl-9 pr-3 py-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-50 text-[13px] transition-colors">
                    <i class="fas fa-money-bill-wave w-3.5 text-center"></i><span>Payroll Report</span>
                </a>
                <a href="#"
                    class="flex items-center gap-2.5 pl-9 pr-3 py-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-50 text-[13px] transition-colors">
                    <i class="fas fa-user-tie w-3.5 text-center"></i><span>HR Analytics</span>
                </a>
            </div>
        </div>

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
                <a href="#"
                    class="flex items-center gap-2.5 pl-9 pr-3 py-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-50 text-[13px] transition-colors">
                    <i class="fas fa-user-shield w-3.5 text-center"></i><span>User Management</span>
                </a>
                <a href="#"
                    class="flex items-center gap-2.5 pl-9 pr-3 py-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-50 text-[13px] transition-colors">
                    <i class="fas fa-lock w-3.5 text-center"></i><span>Roles & Permissions</span>
                </a>
                <a href="#"
                    class="flex items-center gap-2.5 pl-9 pr-3 py-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-50 text-[13px] transition-colors">
                    <i class="fas fa-sliders-h w-3.5 text-center"></i><span>System Settings</span>
                </a>
            </div>
        </div>

    </nav>
</aside>
