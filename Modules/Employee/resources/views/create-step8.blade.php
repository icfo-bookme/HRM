<x-app-layout>
    @include('employee::components.wizard-progress', ['current' => 8])

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white border border-slate-200 shadow-sm rounded-3xl p-8">
            <div class="mb-8 flex items-start justify-between">
                <div>
                    <h1 class="text-3xl font-semibold text-slate-900">Employee Registration</h1>
                    <p class="mt-2 text-sm text-slate-600">Step 8: Track employee job history / career changes. This step can be skipped.</p>
                </div>
                <form method="POST" action="{{ route('employee.reset.step', 8) }}">
                    @csrf
                    <button type="submit" onclick="return confirm('Reset Step 8 data? This will clear all entered information for this step.')"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-amber-300 px-4 py-2 text-sm font-semibold text-amber-700 transition hover:bg-amber-50 focus:outline-none focus:ring-2 focus:ring-amber-400">
                        ⟳ Refresh
                    </button>
                </form>
            </div>

            <form method="POST" action="{{ route('employee.store.step8') }}" class="space-y-6">
                @csrf

                @php
                    $changeTypes = array_combine(
                        ['Joining','Promotion','Demotion','Transfer','Designation Change','Grade Change','Salary Revision','Confirmation','Termination','Resignation','Retirement','Rehired'],
                        ['Joining','Promotion','Demotion','Transfer','Designation Change','Grade Change','Salary Revision','Confirmation','Termination','Resignation','Retirement','Rehired']
                    );
                @endphp
                <div class="grid gap-6 sm:grid-cols-2">
                    <x-form-input label="Effective Date" name="effective_date" id="effective_date" type="date"
                        value="{{ old('effective_date', $data['effective_date'] ?? '') }}" />
                    <x-form-select2 label="Change Type" name="change_type" id="change_type"
                        placeholder="-- Select Change Type --"
                        :options="$changeTypes"
                        :selected="old('change_type', $data['change_type'] ?? '')" />
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <x-form-select label="From Branch" name="from_branch_id" id="from_branch_id" placeholder="-- Select Branch --">
                        @foreach ($branches as $id => $name)
                            <option value="{{ $id }}"
                                {{ old('from_branch_id', $data['from_branch_id'] ?? '') == $id ? 'selected' : '' }}>
                                {{ $name }}</option>
                        @endforeach
                    </x-form-select>
                    <x-form-select label="To Branch" name="to_branch_id" id="to_branch_id" placeholder="-- Select Branch --">
                        @foreach ($branches as $id => $name)
                            <option value="{{ $id }}"
                                {{ old('to_branch_id', $data['to_branch_id'] ?? '') == $id ? 'selected' : '' }}>
                                {{ $name }}</option>
                        @endforeach
                    </x-form-select>
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <x-form-select2 label="From Department" name="from_dept_id" id="from_dept_id"
                        placeholder="-- Select Department --"
                        :options="$departments"
                        :selected="old('from_dept_id', $data['from_dept_id'] ?? '')" />
                    <x-form-select2 label="To Department" name="to_dept_id" id="to_dept_id"
                        placeholder="-- Select Department --"
                        :options="$departments"
                        :selected="old('to_dept_id', $data['to_dept_id'] ?? '')" />
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <x-form-select2 label="From Designation" name="from_desig_id" id="from_desig_id"
                        placeholder="-- Select Designation --"
                        :options="$designations"
                        :selected="old('from_desig_id', $data['from_desig_id'] ?? '')" />
                    <x-form-select2 label="To Designation" name="to_desig_id" id="to_desig_id"
                        placeholder="-- Select Designation --"
                        :options="$designations"
                        :selected="old('to_desig_id', $data['to_desig_id'] ?? '')" />
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <x-form-select2 label="From Grade" name="from_grade_id" id="from_grade_id"
                        placeholder="-- Select Grade --"
                        :options="$grades"
                        :selected="old('from_grade_id', $data['from_grade_id'] ?? '')" />
                    <x-form-select2 label="To Grade" name="to_grade_id" id="to_grade_id"
                        placeholder="-- Select Grade --"
                        :options="$grades"
                        :selected="old('to_grade_id', $data['to_grade_id'] ?? '')" />
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <x-form-input label="From Salary" name="from_salary" id="from_salary" type="number" step="0.01"
                        value="{{ old('from_salary', $data['from_salary'] ?? '') }}" />
                    <x-form-input label="To Salary" name="to_salary" id="to_salary" type="number" step="0.01"
                        value="{{ old('to_salary', $data['to_salary'] ?? '') }}" />
                </div>

                <x-form-textarea label="Reason" name="reason" id="reason"
                    rows="3">{{ old('reason', $data['reason'] ?? '') }}</x-form-textarea>
                <x-form-textarea label="Remarks" name="remarks" id="remarks"
                    rows="3">{{ old('remarks', $data['remarks'] ?? '') }}</x-form-textarea>

                <x-form-select2 label="Approved By" name="approved_by" id="approved_by"
                    placeholder="-- Select Employee --">
                    @foreach ($employees as $emp)
                        <option value="{{ $emp->id }}"
                            {{ old('approved_by', $data['approved_by'] ?? '') == $emp->id ? 'selected' : '' }}>
                            {{ $emp->full_name }} ({{ $emp->employee_code }})</option>
                    @endforeach
                </x-form-select2>

                <div class="flex justify-between items-center pt-4 border-t border-slate-200">
                    <a href="{{ route('employee.create.step7') }}"
                        class="text-sm font-medium text-slate-600 hover:text-slate-900">&larr; Back to Step 7</a>
                    <div class="flex items-center gap-3">
                        <button type="submit" name="skip" value="1"
                            class="inline-flex items-center justify-center rounded-2xl border border-slate-300 px-6 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2">
                            Skip</button>
                        <button type="submit"
                            class="inline-flex items-center justify-center rounded-2xl bg-blue-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-bg-blue-900 focus:ring-offset-2">
                            Next Step &rarr;</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>