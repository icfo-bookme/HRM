<div id="tab-basic" class="{{ $activeTab === 'basic' ? '' : 'hidden' }}">
    <form class="section-save-form" action="{{ route('employee.edit.basic', $employee->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-sky-100 flex items-center justify-center">
                    <i class="fa-solid fa-building text-sky-600"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-900">Core Information</h2>
                    <p class="text-xs text-slate-500">Employee basics</p>
                </div>
            </div>

            <div class="grid gap-6 sm:grid-cols-2">
                <x-form-input label="Employee Code" name="employee_code" :value="old('employee_code', $employee->employee_code ?? '')" readonly required />

                <div>
                    <x-form-select label="Company" name="company_id">
                        @foreach ($companies as $company)
                            <option value="{{ $company->id }}" {{ old('company_id', $employee->company_id ?? '') == $company->id ? 'selected' : '' }}>
                                {{ $company->name }}
                            </option>
                        @endforeach
                    </x-form-select>
                </div>

                <x-form-select label="Branch" name="branch_id" placeholder="-- Select Branch --">
                    @foreach ($branches as $id => $name)
                        <option value="{{ $id }}" {{ old('branch_id', $employee->branch_id ?? '') == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </x-form-select>

                <x-form-select label="Department" name="department_id" placeholder="-- Select Department --">
                    @foreach ($departments as $id => $name)
                        <option value="{{ $id }}" {{ old('department_id', $employee->department_id ?? '') == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </x-form-select>

                <x-form-select label="Designation" name="designation_id" placeholder="-- Select Designation --">
                    @foreach ($designations as $id => $title)
                        <option value="{{ $id }}" {{ old('designation_id', $employee->designation_id ?? '') == $id ? 'selected' : '' }}>{{ $title }}</option>
                    @endforeach
                </x-form-select>

                <x-form-select label="Grade" name="grade_id" placeholder="-- Select Grade --">
                    @foreach ($grades as $id => $name)
                        <option value="{{ $id }}" {{ old('grade_id', $employee->grade_id ?? '') == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </x-form-select>

                <x-form-select label="Shift" name="shift_id" placeholder="-- Select Shift --">
                    @foreach ($shifts as $id => $name)
                        <option value="{{ $id }}" {{ old('shift_id', $employee->shift_id ?? '') == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </x-form-select>

                <x-form-select label="Reports To" name="reports_to" placeholder="-- Select Manager --">
                    @foreach ($managers as $manager)
                        <option value="{{ $manager->id }}" {{ old('reports_to', $employee->reports_to ?? '') == $manager->id ? 'selected' : '' }}>
                            {{ $manager->full_name }} ({{ $manager->employee_code }})
                        </option>
                    @endforeach
                </x-form-select>

                <x-form-select label="Employment Type" name="employment_type" placeholder="-- Select Type --">
                    @foreach (['Full-Time', 'Part-Time', 'Contractual', 'Intern', 'Probation', 'Freelance'] as $type)
                        <option value="{{ $type }}" {{ old('employment_type', $employee->employment_type ?? '') === $type ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </x-form-select>

                <x-form-input label="Joining Date" name="joining_date" type="date" :value="old('joining_date', $employee->joining_date ?? '')" />
                <x-form-input label="Confirmation Date" name="confirmation_date" type="date" :value="old('confirmation_date', $employee->confirmation_date ?? '')" />
                <x-form-input label="Probation End Date" name="probation_end_date" type="date" :value="old('probation_end_date', $employee->probation_end_date ?? '')" />
                <x-form-input label="Contract End Date" name="contract_end_date" type="date" :value="old('contract_end_date', $employee->contract_end_date ?? '')" />

                <x-form-select label="Status" name="status" placeholder="-- Select Status --">
                    @foreach (['Active', 'Inactive', 'On Leave', 'Suspended', 'Terminated', 'Resigned', 'Retired'] as $status)
                        <option value="{{ $status }}" {{ old('status', $employee->status ?? '') === $status ? 'selected' : '' }}>{{ $status }}</option>
                    @endforeach
                </x-form-select>

                <x-form-select label="Portal Active" name="portal_active" placeholder="">
                    <option value="1" {{ old('portal_active', $employee->portal_active ?? 1) == 1 ? 'selected' : '' }}>Yes</option>
                    <option value="0" {{ old('portal_active', $employee->portal_active ?? 1) == 0 ? 'selected' : '' }}>No</option>
                </x-form-select>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-8 py-3 text-sm font-bold text-white hover:from-emerald-700 hover:to-teal-700 transition-all shadow-lg">
                    <i class="fa-solid fa-floppy-disk"></i> Save Core Info
                </button>
            </div>
        </div>
    </form>
</div>