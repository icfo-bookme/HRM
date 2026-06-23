<div id="tab-dependents" class="{{ $activeTab === 'dependents' ? '' : 'hidden' }}">
    <form class="section-save-form" action="{{ route('employee.edit.dependents', $employee->id) }}">
        @csrf
        @method('PUT')
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-pink-100 flex items-center justify-center">
                    <i class="fa-solid fa-people-group text-pink-600"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-900">Dependent & Nominee</h2>
                    <p class="text-xs text-slate-500">Add dependent and nominee. One dependent per employee.</p>
                </div>
            </div>

            @php $dependent = $employee->dependents->first(); @endphp

            @if($dependent)
                <input type="hidden" name="id" value="{{ $dependent->id }}">
            @endif

            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
                <div class="bg-gradient-to-r from-slate-50 to-white px-6 py-3.5 border-b border-slate-100">
                    <h3 class="font-semibold text-slate-800 text-sm">
                        <i class="fa-solid fa-user text-pink-500 mr-2"></i>
                        Dependent Information
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid gap-5 sm:grid-cols-3">
                        <x-form-input label="Full Name" name="full_name" :value="old('full_name', $dependent->full_name ?? '')" required />
                        <x-form-select label="Relation" name="relation" placeholder="-- Select Relation --" required>
                            @foreach (['Spouse', 'Son', 'Daughter', 'Father', 'Mother', 'Brother', 'Sister', 'Other'] as $rel)
                                <option value="{{ $rel }}" {{ old('relation', $dependent->relation ?? '') === $rel ? 'selected' : '' }}>{{ $rel }}</option>
                            @endforeach
                        </x-form-select>
                        <x-form-input label="Date of Birth" name="date_of_birth" type="date" :value="old('date_of_birth', $dependent->date_of_birth ?? '')" />
                        <x-form-input label="NID Number" name="nid_number" :value="old('nid_number', $dependent->nid_number ?? '')" />
                        <x-form-input label="Phone" name="phone" :value="old('phone', $dependent->phone ?? '')" />
                        <x-form-input label="Email" name="email" type="email" :value="old('email', $dependent->email ?? '')" />
                        <x-form-input label="Occupation" name="occupation" :value="old('occupation', $dependent->occupation ?? '')" />
                        <x-form-input label="Priority Order" name="priority_order" type="number" min="1" :value="old('priority_order', $dependent->priority_order ?? '')" />
                        <div class="flex items-center gap-3 mt-6">
                            <input type="checkbox" name="is_nominee" value="1"
                                {{ old('is_nominee', $dependent->is_nominee ?? false) ? 'checked' : '' }}
                                class="rounded border-slate-300 text-indigo-600 h-4 w-4">
                            <label class="text-sm text-slate-700">Is Nominee</label>
                        </div>
                        <x-form-input label="Nominee Percent (%)" name="nominee_percent" type="number" step="0.01" min="0" max="100" :value="old('nominee_percent', $dependent->nominee_percent ?? '')" />
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-8 py-3 text-sm font-bold text-white hover:from-emerald-700 hover:to-teal-700 transition-all shadow-lg">
                    <i class="fa-solid fa-floppy-disk"></i> Save Dependent
                </button>
            </div>
        </div>
    </form>
</div>