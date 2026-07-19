<div id="tab-education" class="{{ $activeTab === 'education' ? '' : 'hidden' }}">
    <form class="section-save-form" action="{{ route('employee.edit.education', $employee->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-cyan-100 flex items-center justify-center">
                    <i class="fa-solid fa-graduation-cap text-cyan-600"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-900">Education</h2>
                    <p class="text-xs text-slate-500">Qualifications</p>
                </div>
            </div>

            <div id="education-container" class="space-y-6">
                @foreach ($employee->educations as $eduIndex => $education)
                    <div class="repeater-row bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
                        <div class="bg-gradient-to-r from-slate-50 to-white px-6 py-3.5 border-b border-slate-100 flex items-center justify-between">
                            <h3 class="font-semibold text-slate-800 text-sm">
                                <i class="fa-solid fa-graduation-cap text-cyan-500 mr-2"></i>
                                Education #{{ $loop->iteration }}
                            </h3>
                            <button type="button" class="remove-row text-red-500 hover:text-red-700 text-xs font-medium px-3 py-1.5 rounded-lg hover:bg-red-50">
                                <i class="fa-solid fa-trash-can"></i> Remove
                            </button>
                        </div>
                        <div class="p-6 space-y-5">
                            <input type="hidden" name="educations[{{ $eduIndex }}][id]" value="{{ $education->id }}">
                            <div class="grid gap-5 sm:grid-cols-2">
                                <x-form-input label="Degree" :name="'educations['.$eduIndex.'][degree]'" :value="old('educations.'.$eduIndex.'.degree', $education->degree)"
                                    placeholder="e.g. B.Sc. in CSE" required />
                                <x-form-input label="Major / Subject" :name="'educations['.$eduIndex.'][major_subject]'" :value="old('educations.'.$eduIndex.'.major_subject', $education->major_subject)" />
                                <x-form-input label="Institution" :name="'educations['.$eduIndex.'][institution]'" :value="old('educations.'.$eduIndex.'.institution', $education->institution)" />
                                <x-form-input label="Board / University" :name="'educations['.$eduIndex.'][board_university]'" :value="old('educations.'.$eduIndex.'.board_university', $education->board_university)" />
                                <x-form-input label="Passing Year" :name="'educations['.$eduIndex.'][passing_year]'" type="number" :value="old('educations.'.$eduIndex.'.passing_year', $education->passing_year)" placeholder="e.g. 2020" />
                                <x-form-select label="Result Type" :name="'educations['.$eduIndex.'][result_type]'" placeholder="-- Select --">
                                    @foreach (['Grade', 'CGPA', 'Division', 'Percentage', 'Pass'] as $rt)
                                        <option value="{{ $rt }}" {{ old("educations.{$eduIndex}.result_type", $education->result_type) === $rt ? 'selected' : '' }}>{{ $rt }}</option>
                                    @endforeach
                                </x-form-select>
                                <x-form-input label="Result" :name="'educations['.$eduIndex.'][result_value]'" :value="old('educations.'.$eduIndex.'.result_value', $education->result_value)" placeholder="e.g. 3.50" />
                                <x-form-input label="Country" :name="'educations['.$eduIndex.'][country]'" :value="old('educations.'.$eduIndex.'.country', $education->country)" />
                                <x-form-input label="Duration From" :name="'educations['.$eduIndex.'][duration_from]'" type="date" :value="old('educations.'.$eduIndex.'.duration_from', $education->duration_from?->format('Y-m-d') ?? '')" />
                                <x-form-input label="Duration To" :name="'educations['.$eduIndex.'][duration_to]'" type="date" :value="old('educations.'.$eduIndex.'.duration_to', $education->duration_to?->format('Y-m-d') ?? '')" />
                                <div>
                                    <label class="font-semibold text-sm text-slate-700 block mb-1.5">Certificate File</label>
                                    <input type="file" :name="'educations['.$eduIndex.'][certificate_file]'" accept=".pdf,.jpg,.png"
                                        class="block w-full text-sm border border-slate-300 rounded-xl p-2">
                                    @if($education->certificate_path)
                                        <p class="text-xs text-slate-500 mt-1">Current: {{ basename($education->certificate_path) }}</p>
                                        <input type="hidden" name="educations[{{ $eduIndex }}][certificate_path]" value="{{ $education->certificate_path }}">
                                    @endif
                                </div>
                                <div class="flex items-center gap-3 mt-6">
                                    <input type="checkbox" name="educations[{{ $eduIndex }}][is_highest]" value="1"
                                        {{ $education->is_highest ? 'checked' : '' }}
                                        class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 h-4 w-4">
                                    <label class="text-sm text-slate-700">Mark as highest education</label>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Template for new row --}}
            <template id="education-template">
                <div class="repeater-row bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
                    <div class="bg-gradient-to-r from-slate-50 to-white px-6 py-3.5 border-b border-slate-100 flex items-center justify-between">
                        <h3 class="font-semibold text-slate-800 text-sm">
                            <i class="fa-solid fa-graduation-cap text-cyan-500 mr-2"></i>
                            Education #<span class="row-number"></span>
                        </h3>
                        <button type="button" class="remove-row text-red-500 hover:text-red-700 text-xs font-medium px-3 py-1.5 rounded-lg hover:bg-red-50">
                            <i class="fa-solid fa-trash-can"></i> Remove
                        </button>
                    </div>
                    <div class="p-6 space-y-5">
                        <div class="grid gap-5 sm:grid-cols-2">
                            <x-form-input label="Degree" name="educations[__INDEX__][degree]" placeholder="e.g. B.Sc. in CSE" required />
                            <x-form-input label="Major / Subject" name="educations[__INDEX__][major_subject]" />
                            <x-form-input label="Institution" name="educations[__INDEX__][institution]" />
                            <x-form-input label="Board / University" name="educations[__INDEX__][board_university]" />
                            <x-form-input label="Passing Year" name="educations[__INDEX__][passing_year]" type="number" placeholder="e.g. 2020" />
                            <x-form-select label="Result Type" name="educations[__INDEX__][result_type]" placeholder="-- Select --">
                                @foreach (['Grade', 'CGPA', 'Division', 'Percentage', 'Pass'] as $rt)
                                    <option value="{{ $rt }}">{{ $rt }}</option>
                                @endforeach
                            </x-form-select>
                            <x-form-input label="Result" name="educations[__INDEX__][result_value]" placeholder="e.g. 3.50" />
                            <x-form-input label="Country" name="educations[__INDEX__][country]" />
                            <x-form-input label="Duration From" name="educations[__INDEX__][duration_from]" type="date" />
                            <x-form-input label="Duration To" name="educations[__INDEX__][duration_to]" type="date" />
                            <div>
                                <label class="font-semibold text-sm text-slate-700 block mb-1.5">Certificate File</label>
                                <input type="file" name="educations[__INDEX__][certificate_file]" accept=".pdf,.jpg,.png" class="block w-full text-sm border border-slate-300 rounded-xl p-2">
                            </div>
                            <div class="flex items-center gap-3 mt-6">
                                <input type="checkbox" name="educations[__INDEX__][is_highest]" value="1" class="rounded border-slate-300 text-indigo-600 h-4 w-4">
                                <label class="text-sm text-slate-700">Mark as highest education</label>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <button type="button" class="add-row-btn mt-6 w-full inline-flex items-center justify-center gap-2 rounded-xl border-2 border-dashed border-cyan-300 px-5 py-3 text-sm font-semibold text-cyan-600 hover:border-cyan-500 hover:bg-cyan-50 transition-all"
                data-container="#education-container" data-template="#education-template">
                <i class="fa-solid fa-plus-circle"></i> Add Education
            </button>

            <div class="mt-8 flex justify-end">
                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-8 py-3 text-sm font-bold text-white hover:from-emerald-700 hover:to-teal-700 transition-all shadow-lg">
                    <i class="fa-solid fa-floppy-disk"></i> Save Education
                </button>
            </div>
        </div>
    </form>
</div>