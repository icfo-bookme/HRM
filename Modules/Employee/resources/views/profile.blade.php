<x-app-layout>
    @php
        $personalInfo = $employee->personalInfo;
        $presentAddress = $employee->addresses->where('address_type', 'present')->first();
        $permanentAddress = $employee->addresses->where('address_type', 'permanent')->first();
        $banking = $employee->banking->first();
        $statusColors = [
            'Active' => 'bg-emerald-100 text-emerald-700 ring-emerald-600/20',
            'Inactive' => 'bg-slate-100 text-slate-600 ring-slate-500/20',
            'On Leave' => 'bg-amber-100 text-amber-700 ring-amber-600/20',
            'Suspended' => 'bg-red-100 text-red-700 ring-red-600/20',
            'Terminated' => 'bg-red-100 text-red-700 ring-red-600/20',
            'Resigned' => 'bg-orange-100 text-orange-700 ring-orange-600/20',
            'Retired' => 'bg-purple-100 text-purple-700 ring-purple-600/20',
        ];
        $employmentTypes = [
            'permanent' => 'Permanent',
            'probation' => 'Probation',
            'contractual' => 'Contractual',
            'intern' => 'Intern',
            'part-time' => 'Part Time',
        ];
    @endphp

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm text-slate-500 mb-6">
            <a href="{{ route('employee.index') }}" class="hover:text-indigo-600 transition-colors">Employees</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <span class="text-slate-800 font-medium">{{ $employee->full_name }}</span>
        </nav>

        {{-- Profile Header Card --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-6">
            <!-- Cover Banner -->
            <div class="h-32 bg-gradient-to-r from-[#006172] via-[#006172] to-pink-500 relative">
                <div class="absolute inset-0 bg-black/10"></div>
            </div>
            
            <!-- Profile Info Section -->
            <div class="px-6 pb-6">
                <div class="flex flex-col sm:flex-row items-start gap-5 -mt-14 relative z-10">
                    <!-- Profile Photo with download -->
                    <div class="relative group flex-shrink-0">
                        <div class="w-28 h-28 rounded-xl border-4 border-white shadow-lg bg-white overflow-hidden">
                            @if($personalInfo && $personalInfo->profile_photo)
                                <img src="{{ Storage::url($personalInfo->profile_photo) }}" 
                                     alt="{{ $employee->full_name }}"
                                     class="w-28 h-28 object-cover">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                                    <span class="text-white text-3xl font-bold">
                                        {{ strtoupper(substr($employee->full_name ?: $employee->employee_code, 0, 2)) }}
                                    </span>
                                </div>
                            @endif
                        </div>
                        @if($personalInfo && $personalInfo->profile_photo)
                            <a href="{{ Storage::url($personalInfo->profile_photo) }}" download
                               class="absolute -bottom-1 -right-1 w-7 h-7 rounded-full bg-white shadow-md border border-slate-200 flex items-center justify-center text-slate-500 hover:text-indigo-600 hover:border-indigo-300 transition-all opacity-0 group-hover:opacity-100"
                               title="Download Photo">
                                <i class="fa-solid fa-download text-[11px]"></i>
                            </a>
                        @endif
                    </div>

                    <!-- Name & Details -->
                    <div class="flex-1 min-w-0 pt-14 sm:pt-0 sm:mt-14">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-4">
                            <div>
                                <h1 class="text-2xl font-bold text-slate-900">{{ $employee->full_name ?: 'N/A' }}</h1>
                                <p class="text-sm text-slate-500 mt-0.5">
                                    {{ $employee->employee_code }}
                                    @if($employee->designation)
                                        <span class="mx-2">•</span>
                                        {{ $employee->designation->title }}
                                    @endif
                                </p>
                            </div>
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium ring-1 ring-inset {{ $statusColors[$employee->status] ?? 'bg-slate-100 text-slate-600' }}">
                                {{ $employee->status }}
                            </span>
                        </div>

                        <!-- Quick Info Badges -->
                        <div class="flex flex-wrap gap-3 mt-4">
                            @if($employee->department)
                                <div class="inline-flex items-center gap-1.5 rounded-lg bg-slate-50 border border-slate-200 px-3 py-1.5 text-xs text-slate-600">
                                    <i class="fa-solid fa-building text-indigo-500"></i>
                                    {{ $employee->department->name }}
                                </div>
                            @endif
                            @if($employee->branch)
                                <div class="inline-flex items-center gap-1.5 rounded-lg bg-slate-50 border border-slate-200 px-3 py-1.5 text-xs text-slate-600">
                                    <i class="fa-solid fa-location-dot text-emerald-500"></i>
                                    {{ $employee->branch->name }}
                                </div>
                            @endif
                            @if($employee->employment_type)
                                <div class="inline-flex items-center gap-1.5 rounded-lg bg-slate-50 border border-slate-200 px-3 py-1.5 text-xs text-slate-600">
                                    <i class="fa-solid fa-clock text-amber-500"></i>
                                    {{ $employmentTypes[$employee->employment_type] ?? $employee->employment_type }}
                                </div>
                            @endif
                            @if($employee->joining_date)
                                <div class="inline-flex items-center gap-1.5 rounded-lg bg-slate-50 border border-slate-200 px-3 py-1.5 text-xs text-slate-600">
                                    <i class="fa-solid fa-calendar-check text-rose-500"></i>
                                    Joined {{ \Carbon\Carbon::parse($employee->joining_date)->format('d M Y') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left Column --}}
            <div class="lg:col-span-1 space-y-6">
                {{-- Personal Information Card --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100">
                        <h3 class="text-sm font-semibold text-slate-900 flex items-center gap-2">
                            <i class="fa-solid fa-user text-indigo-500"></i>
                            Personal Information
                        </h3>
                    </div>
                    <div class="px-5 py-4 space-y-3.5">
                        @if($personalInfo)
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-slate-500">Full Name</span>
                                <span class="text-sm font-medium text-slate-800 text-right">{{ $personalInfo->full_name ?? 'N/A' }}</span>
                            </div>
                            @if($personalInfo->display_name)
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-slate-500">Display Name</span>
                                <span class="text-sm font-medium text-slate-800 text-right">{{ $personalInfo->display_name }}</span>
                            </div>
                            @endif
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-slate-500">Gender</span>
                                <span class="text-sm font-medium text-slate-800 text-right">{{ $personalInfo->gender ?? 'N/A' }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-slate-500">Date of Birth</span>
                                <span class="text-sm font-medium text-slate-800 text-right">
                                    {{ $personalInfo->date_of_birth ? \Carbon\Carbon::parse($personalInfo->date_of_birth)->format('d M Y') : 'N/A' }}
                                    @if($personalInfo->date_of_birth)
                                        <span class="text-xs text-slate-400">({{ $employee->age }} yrs)</span>
                                    @endif
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-slate-500">Blood Group</span>
                                <span class="text-sm font-medium text-slate-800 text-right">{{ $personalInfo->blood_group ?? 'N/A' }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-slate-500">Marital Status</span>
                                <span class="text-sm font-medium text-slate-800 text-right">{{ $personalInfo->marital_status ?? 'N/A' }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-slate-500">Religion</span>
                                <span class="text-sm font-medium text-slate-800 text-right">{{ $personalInfo->religion ?? 'N/A' }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-slate-500">Nationality</span>
                                <span class="text-sm font-medium text-slate-800 text-right">{{ $personalInfo->nationality ?? 'N/A' }}</span>
                            </div>
                        @else
                            <p class="text-sm text-slate-400 text-center py-4">No personal information available</p>
                        @endif
                    </div>
                </div>

                {{-- Contact Information Card --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100">
                        <h3 class="text-sm font-semibold text-slate-900 flex items-center gap-2">
                            <i class="fa-solid fa-address-card text-emerald-500"></i>
                            Contact
                        </h3>
                    </div>
                    <div class="px-5 py-4 space-y-3.5">
                        @if($personalInfo)
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-slate-500">Email</span>
                                <span class="text-sm font-medium text-slate-800 text-right">{{ $personalInfo->email ?? 'N/A' }}</span>
                            </div>
                            @if($personalInfo->personal_email)
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-slate-500">Personal Email</span>
                                <span class="text-sm font-medium text-slate-800 text-right">{{ $personalInfo->personal_email }}</span>
                            </div>
                            @endif
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-slate-500">Phone</span>
                                <span class="text-sm font-medium text-slate-800 text-right">{{ $personalInfo->phone ?? 'N/A' }}</span>
                            </div>
                            @if($personalInfo->phone_2)
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-slate-500">Phone (Alt)</span>
                                <span class="text-sm font-medium text-slate-800 text-right">{{ $personalInfo->phone_2 }}</span>
                            </div>
                            @endif
                            @if($personalInfo->personal_mobile)
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-slate-500">Personal Mobile</span>
                                <span class="text-sm font-medium text-slate-800 text-right">{{ $personalInfo->personal_mobile }}</span>
                            </div>
                            @endif
                        @else
                            <p class="text-sm text-slate-400 text-center py-4">No contact information available</p>
                        @endif
                    </div>
                </div>

                {{-- Family Information Card --}}
                @if($personalInfo && ($personalInfo->father_name || $personalInfo->mother_name || $personalInfo->spouse_name))
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100">
                        <h3 class="text-sm font-semibold text-slate-900 flex items-center gap-2">
                            <i class="fa-solid fa-people-roof text-rose-500"></i>
                            Family
                        </h3>
                    </div>
                    <div class="px-5 py-4 space-y-3.5">
                        @if($personalInfo->father_name)
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-slate-500">Father's Name</span>
                            <span class="text-sm font-medium text-slate-800 text-right">{{ $personalInfo->father_name }}</span>
                        </div>
                        @endif
                        @if($personalInfo->mother_name)
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-slate-500">Mother's Name</span>
                            <span class="text-sm font-medium text-slate-800 text-right">{{ $personalInfo->mother_name }}</span>
                        </div>
                        @endif
                        @if($personalInfo->spouse_name)
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-slate-500">Spouse Name</span>
                            <span class="text-sm font-medium text-slate-800 text-right">{{ $personalInfo->spouse_name }}</span>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Signature Card --}}
                @if($personalInfo && $personalInfo->signature_file)
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100">
                        <h3 class="text-sm font-semibold text-slate-900 flex items-center gap-2">
                            <i class="fa-solid fa-pen text-indigo-500"></i>
                            Signature
                        </h3>
                    </div>
                    <div class="px-5 py-4 flex justify-center">
                        <div class="bg-slate-50 rounded-xl p-4 border border-slate-100 inline-block">
                            <img src="{{ Storage::url($personalInfo->signature_file) }}" 
                                 alt="Signature of {{ $employee->full_name }}"
                                 class="max-h-16 object-contain">
                        </div>
                    </div>
                </div>
                @endif
            </div>

            {{-- Right Column (2/3 width) --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Employment Details Card --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100">
                        <h3 class="text-sm font-semibold text-slate-900 flex items-center gap-2">
                            <i class="fa-solid fa-briefcase text-blue-500"></i>
                            Employment Details
                        </h3>
                    </div>
                    <div class="px-5 py-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div class="bg-slate-50 rounded-xl p-3.5 border border-slate-100">
                                <span class="text-xs text-slate-500 block">Employee Code</span>
                                <span class="text-sm font-semibold text-slate-800 mt-1 block">{{ $employee->employee_code }}</span>
                            </div>
                            <div class="bg-slate-50 rounded-xl p-3.5 border border-slate-100">
                                <span class="text-xs text-slate-500 block">Department</span>
                                <span class="text-sm font-semibold text-slate-800 mt-1 block">{{ $employee->department->name ?? 'N/A' }}</span>
                            </div>
                            <div class="bg-slate-50 rounded-xl p-3.5 border border-slate-100">
                                <span class="text-xs text-slate-500 block">Designation</span>
                                <span class="text-sm font-semibold text-slate-800 mt-1 block">{{ $employee->designation->title ?? 'N/A' }}</span>
                            </div>
                            <div class="bg-slate-50 rounded-xl p-3.5 border border-slate-100">
                                <span class="text-xs text-slate-500 block">Employment Type</span>
                                <span class="text-sm font-semibold text-slate-800 mt-1 block">{{ $employmentTypes[$employee->employment_type] ?? $employee->employment_type ?? 'N/A' }}</span>
                            </div>
                            <div class="bg-slate-50 rounded-xl p-3.5 border border-slate-100">
                                <span class="text-xs text-slate-500 block">Salary Grade</span>
                                <span class="text-sm font-semibold text-slate-800 mt-1 block">{{ $employee->salaryGrade->name ?? 'N/A' }}</span>
                            </div>
                            <div class="bg-slate-50 rounded-xl p-3.5 border border-slate-100">
                                <span class="text-xs text-slate-500 block">Shift</span>
                                <span class="text-sm font-semibold text-slate-800 mt-1 block">{{ $employee->shift->name ?? 'N/A' }}</span>
                            </div>
                            <div class="bg-slate-50 rounded-xl p-3.5 border border-slate-100">
                                <span class="text-xs text-slate-500 block">Branch</span>
                                <span class="text-sm font-semibold text-slate-800 mt-1 block">{{ $employee->branch->name ?? 'N/A' }}</span>
                            </div>
                            <div class="bg-slate-50 rounded-xl p-3.5 border border-slate-100">
                                <span class="text-xs text-slate-500 block">Reports To</span>
                                <span class="text-sm font-semibold text-slate-800 mt-1 block">{{ $employee->manager->full_name ?? 'N/A' }}</span>
                            </div>
                            <div class="bg-slate-50 rounded-xl p-3.5 border border-slate-100">
                                <span class="text-xs text-slate-500 block">Portal Active</span>
                                <span class="text-sm font-semibold mt-1 block">
                                    @if($employee->portal_active)
                                        <span class="inline-flex items-center gap-1 text-emerald-600">
                                            <i class="fa-solid fa-circle text-[6px]"></i> Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-slate-400">
                                            <i class="fa-solid fa-circle text-[6px]"></i> Inactive
                                        </span>
                                    @endif
                                </span>
                            </div>
                        </div>

                        {{-- Dates Section --}}
                        @if($employee->joining_date || $employee->confirmation_date || $employee->probation_end_date || $employee->contract_end_date || $employee->last_working_day)
                        <div class="mt-4 pt-4 border-t border-slate-100">
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                @if($employee->joining_date)
                                <div class="bg-slate-50 rounded-xl p-3.5 border border-slate-100">
                                    <span class="text-xs text-slate-500 block">Joining Date</span>
                                    <span class="text-sm font-semibold text-slate-800 mt-1 block">{{ \Carbon\Carbon::parse($employee->joining_date)->format('d M Y') }}</span>
                                </div>
                                @endif
                                @if($employee->confirmation_date)
                                <div class="bg-slate-50 rounded-xl p-3.5 border border-slate-100">
                                    <span class="text-xs text-slate-500 block">Confirmation Date</span>
                                    <span class="text-sm font-semibold text-slate-800 mt-1 block">{{ \Carbon\Carbon::parse($employee->confirmation_date)->format('d M Y') }}</span>
                                </div>
                                @endif
                                @if($employee->probation_end_date)
                                <div class="bg-slate-50 rounded-xl p-3.5 border border-slate-100">
                                    <span class="text-xs text-slate-500 block">Probation End</span>
                                    <span class="text-sm font-semibold text-slate-800 mt-1 block">{{ \Carbon\Carbon::parse($employee->probation_end_date)->format('d M Y') }}</span>
                                </div>
                                @endif
                                @if($employee->contract_end_date)
                                <div class="bg-slate-50 rounded-xl p-3.5 border border-slate-100">
                                    <span class="text-xs text-slate-500 block">Contract End</span>
                                    <span class="text-sm font-semibold text-slate-800 mt-1 block">{{ \Carbon\Carbon::parse($employee->contract_end_date)->format('d M Y') }}</span>
                                </div>
                                @endif
                                @if($employee->last_working_day)
                                <div class="bg-slate-50 rounded-xl p-3.5 border border-slate-100">
                                    <span class="text-xs text-slate-500 block">Last Working Day</span>
                                    <span class="text-sm font-semibold text-slate-800 mt-1 block">{{ \Carbon\Carbon::parse($employee->last_working_day)->format('d M Y') }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Addresses Card --}}
                @if($presentAddress || $permanentAddress)
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100">
                        <h3 class="text-sm font-semibold text-slate-900 flex items-center gap-2">
                            <i class="fa-solid fa-location-dot text-teal-500"></i>
                            Address Information
                        </h3>
                    </div>
                    <div class="px-5 py-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @if($presentAddress)
                            <div>
                                <h4 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                                    <i class="fa-solid fa-house text-emerald-500"></i> Present Address
                                </h4>
                                <div class="bg-slate-50 rounded-xl p-4 border border-slate-100 text-sm text-slate-700 space-y-1">
                                    <p>{{ $presentAddress->house_no ? 'House: '.$presentAddress->house_no : '' }} {{ $presentAddress->road_name ? 'Road: '.$presentAddress->road_name : '' }}</p>
                                    <p>{{ $presentAddress->village ?: '' }} {{ $presentAddress->area ? '- '.$presentAddress->area : '' }}</p>
                                    <p>{{ $presentAddress->city ?: '' }} {{ $presentAddress->upazila ? '- '.$presentAddress->upazila : '' }}</p>
                                    <p>{{ $presentAddress->district ?: '' }} {{ $presentAddress->division ? '- '.$presentAddress->division : '' }}</p>
                                    <p>{{ $presentAddress->postal_code ? 'Postal: '.$presentAddress->postal_code : '' }} {{ $presentAddress->country ? '| '.$presentAddress->country : '' }}</p>
                                </div>
                            </div>
                            @endif
                            @if($permanentAddress)
                            <div>
                                <h4 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                                    <i class="fa-solid fa-house-circle-check text-blue-500"></i> Permanent Address
                                </h4>
                                <div class="bg-slate-50 rounded-xl p-4 border border-slate-100 text-sm text-slate-700 space-y-1">
                                    <p>{{ $permanentAddress->house_no ? 'House: '.$permanentAddress->house_no : '' }} {{ $permanentAddress->road_name ? 'Road: '.$permanentAddress->road_name : '' }}</p>
                                    <p>{{ $permanentAddress->village ?: '' }} {{ $permanentAddress->area ? '- '.$permanentAddress->area : '' }}</p>
                                    <p>{{ $permanentAddress->city ?: '' }} {{ $permanentAddress->upazila ? '- '.$permanentAddress->upazila : '' }}</p>
                                    <p>{{ $permanentAddress->district ?: '' }} {{ $permanentAddress->division ? '- '.$permanentAddress->division : '' }}</p>
                                    <p>{{ $permanentAddress->postal_code ? 'Postal: '.$permanentAddress->postal_code : '' }} {{ $permanentAddress->country ? '| '.$permanentAddress->country : '' }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                {{-- Education Card --}}
                @if($employee->educations && $employee->educations->count() > 0)
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100">
                        <h3 class="text-sm font-semibold text-slate-900 flex items-center gap-2">
                            <i class="fa-solid fa-graduation-cap text-violet-500"></i>
                            Education ({{ $employee->educations->count() }})
                        </h3>
                    </div>
                    <div class="px-5 py-4">
                        <div class="space-y-3">
                            @foreach($employee->educations as $education)
                            <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-800">{{ $education->degree }}</p>
                                        <p class="text-xs text-slate-500 mt-0.5">
                                            {{ $education->institution ?: '' }}{{ $education->board_university ? ' | '.$education->board_university : '' }}
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-xs font-medium text-slate-600 bg-white px-2.5 py-1 rounded-lg border border-slate-200">
                                            {{ $education->result_value ? $education->result_value : '' }}{{ $education->result_type == 'grade' ? ' (Grade)' : ($education->result_type == 'class' ? ' (Class)' : '') }}
                                        </span>
                                        @if($education->passing_year)
                                        <p class="text-xs text-slate-400 mt-1">{{ $education->passing_year }}</p>
                                        @endif
                                    </div>
                                </div>
                                @if($education->major_subject)
                                <p class="text-xs text-slate-500 mt-1.5">Major: {{ $education->major_subject }}</p>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                {{-- Experience Card --}}
                @if($employee->experiences && $employee->experiences->count() > 0)
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100">
                        <h3 class="text-sm font-semibold text-slate-900 flex items-center gap-2">
                            <i class="fa-solid fa-briefcase text-orange-500"></i>
                            Work Experience ({{ $employee->experiences->count() }})
                        </h3>
                    </div>
                    <div class="px-5 py-4">
                        <div class="space-y-3">
                            @foreach($employee->experiences as $experience)
                            <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-800">{{ $experience->company_name }}</p>
                                        <p class="text-xs text-slate-500 mt-0.5">{{ $experience->designation ?: '' }}</p>
                                    </div>
                                    <div class="text-right">
                                        @if($experience->from_date || $experience->to_date)
                                        <span class="text-xs text-slate-500">
                                            {{ $experience->from_date ? \Carbon\Carbon::parse($experience->from_date)->format('M Y') : '?' }} - 
                                            {{ $experience->is_current ? 'Present' : ($experience->to_date ? \Carbon\Carbon::parse($experience->to_date)->format('M Y') : '?') }}
                                        </span>
                                        @endif
                                    </div>
                                </div>
                                @if($experience->responsibilities)
                                <p class="text-xs text-slate-500 mt-2">{{ Str::limit($experience->responsibilities, 200) }}</p>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                {{-- Skills Card --}}
                @if($employee->skills && $employee->skills->count() > 0)
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100">
                        <h3 class="text-sm font-semibold text-slate-900 flex items-center gap-2">
                            <i class="fa-solid fa-star text-amber-500"></i>
                            Skills ({{ $employee->skills->count() }})
                        </h3>
                    </div>
                    <div class="px-5 py-4">
                        <div class="flex flex-wrap gap-2">
                            @foreach($employee->skills as $skill)
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200 px-3 py-1.5 text-xs font-medium">
                                <i class="fa-solid fa-circle-check text-[10px]"></i>
                                {{ $skill->skill_name }}
                                @if($skill->proficiency)
                                    <span class="text-amber-400">•</span>
                                    <span class="text-amber-500">{{ $skill->proficiency }}</span>
                                @endif
                            </span>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                {{-- Languages Card --}}
                @if($employee->languages && $employee->languages->count() > 0)
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100">
                        <h3 class="text-sm font-semibold text-slate-900 flex items-center gap-2">
                            <i class="fa-solid fa-language text-cyan-500"></i>
                            Languages ({{ $employee->languages->count() }})
                        </h3>
                    </div>
                    <div class="px-5 py-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach($employee->languages as $language)
                            <div class="bg-slate-50 rounded-xl p-3.5 border border-slate-100 flex items-center justify-between">
                                <span class="text-sm font-medium text-slate-700">{{ $language->language_name }}</span>
                                <span class="text-xs text-slate-500">
                                    {{ $language->proficiency ?? 'N/A' }}
                                    @if($language->is_native)
                                        <span class="text-emerald-500 ml-1">(Native)</span>
                                    @endif
                                </span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                {{-- Dependents Card --}}
                @if($employee->dependents && $employee->dependents->count() > 0)
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100">
                        <h3 class="text-sm font-semibold text-slate-900 flex items-center gap-2">
                            <i class="fa-solid fa-people-group text-pink-500"></i>
                            Dependents ({{ $employee->dependents->count() }})
                        </h3>
                    </div>
                    <div class="px-5 py-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach($employee->dependents as $dependent)
                            <div class="bg-slate-50 rounded-xl p-3.5 border border-slate-100">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-slate-800">{{ $dependent->full_name }}</p>
                                        <p class="text-xs text-slate-500">{{ $dependent->relation }}</p>
                                    </div>
                                    @if($dependent->date_of_birth)
                                    <span class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($dependent->date_of_birth)->age }} yrs</span>
                                    @endif
                                </div>
                                @if($dependent->is_emergency_contact)
                                <span class="inline-flex items-center gap-1 mt-2 text-[10px] font-medium text-rose-600 bg-rose-50 px-2 py-0.5 rounded-full">
                                    <i class="fa-solid fa-triangle-exclamation"></i> Emergency Contact
                                </span>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                {{-- Banking Card --}}
                @if($banking)
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100">
                        <h3 class="text-sm font-semibold text-slate-900 flex items-center gap-2">
                            <i class="fa-solid fa-building-columns text-emerald-500"></i>
                            Banking Information
                        </h3>
                    </div>
                    <div class="px-5 py-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @if($banking->bank_name)
                            <div class="bg-slate-50 rounded-xl p-3.5 border border-slate-100">
                                <span class="text-xs text-slate-500 block">Bank Name</span>
                                <span class="text-sm font-semibold text-slate-800 mt-1 block">{{ $banking->bank_name }}</span>
                            </div>
                            @endif
                            @if($banking->account_name)
                            <div class="bg-slate-50 rounded-xl p-3.5 border border-slate-100">
                                <span class="text-xs text-slate-500 block">Account Name</span>
                                <span class="text-sm font-semibold text-slate-800 mt-1 block">{{ $banking->account_name }}</span>
                            </div>
                            @endif
                            @if($banking->account_number)
                            <div class="bg-slate-50 rounded-xl p-3.5 border border-slate-100">
                                <span class="text-xs text-slate-500 block">Account Number</span>
                                <span class="text-sm font-semibold text-slate-800 mt-1 block font-mono">{{ $banking->account_number }}</span>
                            </div>
                            @endif
                            @if($banking->branch_name)
                            <div class="bg-slate-50 rounded-xl p-3.5 border border-slate-100">
                                <span class="text-xs text-slate-500 block">Bank Branch</span>
                                <span class="text-sm font-semibold text-slate-800 mt-1 block">{{ $banking->branch_name }}</span>
                            </div>
                            @endif
                            @if($banking->routing_number)
                            <div class="bg-slate-50 rounded-xl p-3.5 border border-slate-100">
                                <span class="text-xs text-slate-500 block">Routing Number</span>
                                <span class="text-sm font-semibold text-slate-800 mt-1 block font-mono">{{ $banking->routing_number }}</span>
                            </div>
                            @endif
                            @if($banking->mfs_type)
                            <div class="bg-slate-50 rounded-xl p-3.5 border border-slate-100">
                                <span class="text-xs text-slate-500 block">Mobile Financial Service</span>
                                <span class="text-sm font-semibold text-slate-800 mt-1 block">{{ $banking->mfs_type }} - {{ $banking->mfs_account }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                {{-- Documents Card --}}
                @if($employee->documents && $employee->documents->count() > 0)
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100">
                        <h3 class="text-sm font-semibold text-slate-900 flex items-center gap-2">
                            <i class="fa-solid fa-folder-open text-indigo-500"></i>
                            Documents ({{ $employee->documents->count() }})
                        </h3>
                    </div>
                    <div class="px-5 py-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach($employee->documents as $document)
                            <div class="bg-slate-50 rounded-xl p-3.5 border border-slate-100 flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center flex-shrink-0">
                                    <i class="fa-solid fa-file-lines"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-slate-800 truncate">{{ $document->document_name ?: $document->category }}</p>
                                    <p class="text-xs text-slate-500">{{ $document->category }}</p>
                                </div>
                                @if($document->file_path)
                                <a href="{{ Storage::url($document->file_path) }}" target="_blank" 
                                   class="text-indigo-600 hover:text-indigo-800 text-xs font-medium flex items-center gap-1">
                                    <i class="fa-solid fa-download"></i> View
                                </a>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Footer Note --}}
        <div class="mt-8 text-center">
            <p class="text-xs text-slate-400">
                <i class="fa-regular fa-eye"></i> 
                Profile view only — for changes, please use the edit function.
            </p>
        </div>
    </div>
</x-app-layout>