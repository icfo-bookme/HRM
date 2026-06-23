<div id="tab-job-history" class="{{ $activeTab === 'job-history' ? '' : 'hidden' }}">
    <form class="section-save-form" action="{{ route('employee.edit.job-history', $employee->id) }}">
        @csrf
        @method('PUT')
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-violet-100 flex items-center justify-center">
                    <i class="fa-solid fa-timeline text-violet-600"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-900">Job History</h2>
                    <p class="text-xs text-slate-500">Career changes</p>
                </div>
            </div>

            @php $jobHistory = $employee->jobHistory->first(); @endphp

            @if($jobHistory)
                <input type="hidden" name="id" value="{{ $jobHistory->id }}">
            @endif

            <div class="grid gap-6 sm:grid-cols-2">
                <x-form-input label="Effective Date" name="effective_date" type="date" :value="old('effective_date', $jobHistory->effective_date ?? '')" required />
                <x-form-select label="Change Type" name="change_type" placeholder="-- Select --" required>
                    @foreach (['Promotion', 'Demotion', 'Transfer', 'Salary Adjustment', 'Designation Change', 'Department Change', 'Branch Change', 'Grade Change', 'Other'] as $ct)
                        <option value="{{ $ct }}" {{ old('change_type', $jobHistory->change_type ?? '') === $ct ? 'selected' : '' }}>{{ $ct }}</option>
                    @endforeach
                </x-form-select>
                <x-form-select label="From Designation" name="from_desig_id" placeholder="-- Select --">
                    @foreach ($designations as $id => $title)
                        <option value="{{ $id }}" {{ old('from_desig_id', $jobHistory->from_desig_id ?? '') == $id ? 'selected' : '' }}>{{ $title }}</option>
                    @endforeach
                </x-form-select>
                <x-form-select label="To Designation" name="to_desig_id" placeholder="-- Select --">
                    @foreach ($designations as $id => $title)
                        <option value="{{ $id }}" {{ old('to_desig_id', $jobHistory->to_desig_id ?? '') == $id ? 'selected' : '' }}>{{ $title }}</option>
                    @endforeach
                </x-form-select>
                <x-form-select label="From Department" name="from_dept_id" placeholder="-- Select --">
                    @foreach ($departments as $id => $name)
                        <option value="{{ $id }}" {{ old('from_dept_id', $jobHistory->from_dept_id ?? '') == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </x-form-select>
                <x-form-select label="To Department" name="to_dept_id" placeholder="-- Select --">
                    @foreach ($departments as $id => $name)
                        <option value="{{ $id }}" {{ old('to_dept_id', $jobHistory->to_dept_id ?? '') == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </x-form-select>
                <x-form-select label="From Branch" name="from_branch_id" placeholder="-- Select --">
                    @foreach ($branches as $id => $name)
                        <option value="{{ $id }}" {{ old('from_branch_id', $jobHistory->from_branch_id ?? '') == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </x-form-select>
                <x-form-select label="To Branch" name="to_branch_id" placeholder="-- Select --">
                    @foreach ($branches as $id => $name)
                        <option value="{{ $id }}" {{ old('to_branch_id', $jobHistory->to_branch_id ?? '') == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </x-form-select>
                <x-form-select label="From Grade" name="from_grade_id" placeholder="-- Select --">
                    @foreach ($grades as $id => $name)
                        <option value="{{ $id }}" {{ old('from_grade_id', $jobHistory->from_grade_id ?? '') == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </x-form-select>
                <x-form-select label="To Grade" name="to_grade_id" placeholder="-- Select --">
                    @foreach ($grades as $id => $name)
                        <option value="{{ $id }}" {{ old('to_grade_id', $jobHistory->to_grade_id ?? '') == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </x-form-select>
                <x-form-input label="From Salary" name="from_salary" type="number" step="0.01" :value="old('from_salary', $jobHistory->from_salary ?? '')" />
                <x-form-input label="To Salary" name="to_salary" type="number" step="0.01" :value="old('to_salary', $jobHistory->to_salary ?? '')" />
                <div class="sm:col-span-2">
                    <x-form-input label="Reason" name="reason" :value="old('reason', $jobHistory->reason ?? '')" />
                </div>
                <div class="sm:col-span-2">
                    <x-form-textarea label="Remarks" name="remarks" rows="3">{{ old('remarks', $jobHistory->remarks ?? '') }}</x-form-textarea>
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-8 py-3 text-sm font-bold text-white hover:from-emerald-700 hover:to-teal-700 transition-all shadow-lg">
                    <i class="fa-solid fa-floppy-disk"></i> Save Job History
                </button>
            </div>
        </div>
    </form>
</div>