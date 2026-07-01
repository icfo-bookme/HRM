<x-app-layout>
    @include('employee::components.wizard-progress', ['current' => 1])

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white border border-slate-200 shadow-sm rounded-3xl p-8">
            <div class="mb-8 flex items-start justify-between">
                <div>
                    <h1 class="text-3xl font-semibold text-slate-900">Employee Registration</h1>
                    <p class="mt-2 text-sm text-slate-600">Step 1: Core employee details required to begin the onboarding
                        process.</p>
                </div>
                <form method="POST" action="{{ route('employee.reset.step', 1) }}">
                    @csrf
                    <button type="submit" onclick="return confirm('Reset Step 1 data? This will clear all entered information for this step.')"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-amber-300 px-4 py-2 text-sm font-semibold text-amber-700 transition hover:bg-amber-50 focus:outline-none focus:ring-2 focus:ring-amber-400">
                        ⟳ Refresh
                    </button>
                </form>
            </div>

            @if (session('error'))
                <div class="rounded-2xl bg-rose-50 border border-rose-200 p-4 text-rose-700 mb-6">{{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('employee.store.step1') }}" enctype="multipart/form-data"
                class="space-y-6">
                @csrf

                <div class="grid gap-6 sm:grid-cols-2">
                    <x-form-select label="Company" name="company_id" id="company_id" placeholder="-- Select Company --"
                        required>
                        @foreach ($companies as $company)
                            <option value="{{ $company->id }}"
                                {{ old('company_id', $data['company_id'] ?? $companies->first()?->id) == $company->id ? 'selected' : '' }}>
                                {{ $company->name }}
                            </option>
                        @endforeach
                    </x-form-select>

                    <div>
                        <x-form-input label="Employee Code" name="employee_code" id="employee_code" disabled readonly
                            value="{{ $employeeCode }}" placeholder="EMP-001" required />
                        <input type="hidden" name="employee_code"
                            value="{{ old('employee_code', $data['employee_code'] ?? $employeeCode) }}">
                    </div>
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <div>
                        <x-form-select2 label="Branch" name="branch_id" id="branch_id" placeholder="-- Select Branch --"
                            required
                            :options="$branches"
                            :selected="old('branch_id', $data['branch_id'] ?? '')" />
                        @error('branch_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <x-form-select2 label="Department" name="department_id" id="department_id"
                            placeholder="-- Select Department --" required
                            :options="$departments"
                            :selected="old('department_id', $data['department_id'] ?? '')" />
                        @error('department_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <div>
                        <x-form-select2 label="Designation" name="designation_id" id="designation_id"
                            placeholder="-- Select Designation --" required :options="$designations" :selected="old('designation_id', $data['designation_id'] ?? '')" />
                        @error('designation_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <x-form-select2 label="Grade" name="grade_id" id="grade_id" placeholder="-- Select Grade --"
                        :options="$grades"
                        :selected="old('grade_id', $data['grade_id'] ?? '')" />
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <x-form-select label="Shift" name="shift_id" id="shift_id" placeholder="-- Select Shift --">
                        @foreach ($shifts as $id => $name)
                            <option value="{{ $id }}"
                                {{ old('shift_id', $data['shift_id'] ?? '') == $id ? 'selected' : '' }}>
                                {{ $name }}</option>
                        @endforeach
                    </x-form-select>

                    <x-form-select2 label="Reports To" name="reports_to" id="reports_to"
                        placeholder="-- Select Manager --">
                        @foreach ($employee as $emp)
                            <option value="{{ $emp->id }}"
                                {{ old('reports_to', $data['reports_to'] ?? '') == $emp->id ? 'selected' : '' }}>
                                {{ $emp->first_name }} {{ $emp->last_name }} ({{ $emp->employee_code }})</option>
                        @endforeach
                    </x-form-select2>
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <x-form-select label="Employment Type" name="employment_type" id="employment_type"
                        placeholder="-- Select Type --">
                        @foreach (['Full-Time', 'Part-Time', 'Contractual', 'Intern', 'Probation', 'Freelance'] as $type)
                            <option value="{{ $type }}"
                                {{ old('employment_type', $data['employment_type'] ?? '') === $type ? 'selected' : '' }}>
                                {{ $type }}</option>
                        @endforeach
                    </x-form-select>

                    <div>
                        <x-form-input label="Joining Date" name="joining_date" id="joining_date" type="date"
                            value="{{ old('joining_date', $data['joining_date'] ?? '') }}" required />
                        @error('joining_date')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <x-form-input label="Confirmation Date" name="confirmation_date" id="confirmation_date"
                        type="date" value="{{ old('confirmation_date', $data['confirmation_date'] ?? '') }}" />
                    <x-form-input label="Probation End Date" name="probation_end_date" id="probation_end_date"
                        type="date" value="{{ old('probation_end_date', $data['probation_end_date'] ?? '') }}" />
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <x-form-input label="Last Working Day" name="last_working_day" id="last_working_day" type="date"
                        value="{{ old('last_working_day', $data['last_working_day'] ?? '') }}" />
                    <x-form-input label="Contract End Date" name="contract_end_date" id="contract_end_date"
                        type="date" value="{{ old('contract_end_date', $data['contract_end_date'] ?? '') }}" />
                </div>

                {{-- <div class="grid gap-6 sm:grid-cols-2">
                    <x-form-select label="Status" name="status" id="status" placeholder="-- Select Status --">
                        @foreach (['Active', 'Inactive', 'On Leave', 'Suspended', 'Terminated', 'Resigned', 'Retired'] as $status)
                            <option value="{{ $status }}"
                                {{ old('status', $data['status'] ?? '') === $status ? 'selected' : '' }}>
                                {{ $status }}</option>
                        @endforeach
                    </x-form-select>

                    <x-form-select label="Portal Active" name="portal_active" id="portal_active"
                        placeholder="-- Choose --">
                        <option value="1"
                            {{ old('portal_active', $data['portal_active'] ?? '') == 1 ? 'selected' : '' }}>Yes
                        </option>
                        <option value="0"
                            {{ old('portal_active', $data['portal_active'] ?? '') == '0' ? 'selected' : '' }}>No
                        </option>
                    </x-form-select>
                </div> --}}

                <div class="flex justify-end pt-4 border-t border-slate-200">
                    <button type="submit"
                        class="inline-flex items-center justify-center rounded-2xl bg-sky-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2">
                        Next Step &rarr;
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
