<div id="tab-skills" class="{{ $activeTab === 'skills' ? '' : 'hidden' }}">
    <form class="section-save-form" action="{{ route('employee.edit.skills', $employee->id) }}">
        @csrf
        @method('PUT')
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-yellow-100 flex items-center justify-center">
                    <i class="fa-solid fa-star text-yellow-600"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-900">Skills</h2>
                    <p class="text-xs text-slate-500">Competencies</p>
                </div>
            </div>

            <div id="skill-container" class="space-y-4">
                @foreach ($employee->skills as $skillIndex => $skill)
                    <div class="repeater-row bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="font-semibold text-slate-700 text-sm">
                                <i class="fa-solid fa-star text-yellow-500 mr-2"></i>
                                Skill #{{ $loop->iteration }}
                            </h4>
                            <button type="button" class="remove-row text-red-500 hover:text-red-700 text-xs font-medium px-3 py-1.5 rounded-lg hover:bg-red-50">
                                <i class="fa-solid fa-trash-can"></i> Remove
                            </button>
                        </div>
                        <input type="hidden" name="skills[{{ $skillIndex }}][id]" value="{{ $skill->id }}">
                        <div class="grid gap-4 sm:grid-cols-3">
                            <x-form-input label="Skill Name" :name="'skills['.$skillIndex.'][skill_name]'" :value="old('skills.'.$skillIndex.'.skill_name', $skill->skill_name)" placeholder="e.g. PHP" required />
                            <x-form-select label="Category" :name="'skills['.$skillIndex.'][category_id]'" placeholder="-- Select --">
                                @foreach ($skillCategories as $cat)
                                    <option value="{{ $cat->id }}" {{ old("skills.{$skillIndex}.category_id", $skill->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </x-form-select>
                            <x-form-select label="Proficiency" :name="'skills['.$skillIndex.'][proficiency]'" placeholder="-- Select --">
                                @foreach (['Beginner', 'Intermediate', 'Advanced', 'Expert', 'Master'] as $prof)
                                    <option value="{{ $prof }}" {{ old("skills.{$skillIndex}.proficiency", $skill->proficiency) === $prof ? 'selected' : '' }}>{{ $prof }}</option>
                                @endforeach
                            </x-form-select>
                            <x-form-input label="Years of Experience" :name="'skills['.$skillIndex.'][years_of_experience]'" type="number" step="0.1" min="0" max="50" :value="old('skills.'.$skillIndex.'.years_of_experience', $skill->years_of_experience)" />
                            <x-form-input label="Last Used Date" :name="'skills['.$skillIndex.'][last_used_date]'" type="date" :value="old('skills.'.$skillIndex.'.last_used_date', $skill->last_used_date?->format('Y-m-d') ?? '')" />
                            <x-form-input label="Certification" :name="'skills['.$skillIndex.'][certification]'" :value="old('skills.'.$skillIndex.'.certification', $skill->certification)" placeholder="Certification name" />
                            <div class="sm:col-span-3">
                                <x-form-textarea label="Description" :name="'skills['.$skillIndex.'][description]'" rows="2">{{ old('skills.'.$skillIndex.'.description', $skill->description) }}</x-form-textarea>
                            </div>
                            <div class="flex items-center gap-3">
                                <input type="checkbox" name="skills[{{ $skillIndex }}][is_active]" value="1"
                                    {{ ($skill->is_active ?? true) ? 'checked' : '' }}
                                    class="rounded border-slate-300 text-indigo-600 h-4 w-4">
                                <label class="text-sm text-slate-700">Active</label>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <template id="skill-template">
                <div class="repeater-row bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="font-semibold text-slate-700 text-sm">
                            <i class="fa-solid fa-star text-yellow-500 mr-2"></i>
                            Skill #<span class="row-number"></span>
                        </h4>
                        <button type="button" class="remove-row text-red-500 hover:text-red-700 text-xs font-medium px-3 py-1.5 rounded-lg hover:bg-red-50">
                            <i class="fa-solid fa-trash-can"></i> Remove
                        </button>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-3">
                        <x-form-input label="Skill Name" name="skills[__INDEX__][skill_name]" placeholder="e.g. PHP" required />
                        <x-form-select label="Category" name="skills[__INDEX__][category_id]" placeholder="-- Select --">
                            @foreach ($skillCategories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </x-form-select>
                        <x-form-select label="Proficiency" name="skills[__INDEX__][proficiency]" placeholder="-- Select --">
                            @foreach (['Beginner', 'Intermediate', 'Advanced', 'Expert', 'Master'] as $prof)
                                <option value="{{ $prof }}">{{ $prof }}</option>
                            @endforeach
                        </x-form-select>
                        <x-form-input label="Years of Experience" name="skills[__INDEX__][years_of_experience]" type="number" step="0.1" min="0" max="50" />
                        <x-form-input label="Last Used Date" name="skills[__INDEX__][last_used_date]" type="date" />
                        <x-form-input label="Certification" name="skills[__INDEX__][certification]" />
                        <div class="sm:col-span-3">
                            <x-form-textarea label="Description" name="skills[__INDEX__][description]" rows="2"></x-form-textarea>
                        </div>
                        <div class="flex items-center gap-3">
                            <input type="checkbox" name="skills[__INDEX__][is_active]" value="1" checked class="rounded border-slate-300 text-indigo-600 h-4 w-4">
                            <label class="text-sm text-slate-700">Active</label>
                        </div>
                    </div>
                </div>
            </template>

            <button type="button" class="add-row-btn mt-6 w-full inline-flex items-center justify-center gap-2 rounded-xl border-2 border-dashed border-yellow-300 px-5 py-3 text-sm font-semibold text-yellow-600 hover:border-yellow-500 hover:bg-yellow-50 transition-all"
                data-container="#skill-container" data-template="#skill-template">
                <i class="fa-solid fa-plus-circle"></i> Add Skill
            </button>

            <div class="mt-8 flex justify-end">
                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-8 py-3 text-sm font-bold text-white hover:from-emerald-700 hover:to-teal-700 transition-all shadow-lg">
                    <i class="fa-solid fa-floppy-disk"></i> Save Skills
                </button>
            </div>
        </div>
    </form>
</div>