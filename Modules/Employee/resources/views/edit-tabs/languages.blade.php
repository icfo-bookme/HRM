<div id="tab-languages" class="{{ $activeTab === 'languages' ? '' : 'hidden' }}">
    <form class="section-save-form" action="{{ route('employee.edit.languages', $employee->id) }}">
        @csrf
        @method('PUT')
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-teal-100 flex items-center justify-center">
                    <i class="fa-solid fa-language text-teal-600"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-900">Languages</h2>
                    <p class="text-xs text-slate-500">Language skills</p>
                </div>
            </div>

            <div id="language-container" class="space-y-4">
                @foreach ($employee->languages as $langIndex => $language)
                    <div class="repeater-row bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="font-semibold text-slate-700 text-sm">
                                <i class="fa-solid fa-language text-teal-500 mr-2"></i>
                                Language #{{ $loop->iteration }}
                            </h4>
                            <button type="button" class="remove-row text-red-500 hover:text-red-700 text-xs font-medium px-3 py-1.5 rounded-lg hover:bg-red-50">
                                <i class="fa-solid fa-trash-can"></i> Remove
                            </button>
                        </div>
                        <input type="hidden" name="languages[{{ $langIndex }}][id]" value="{{ $language->id }}">
                        <div class="grid gap-4 sm:grid-cols-4">
                            <x-form-input label="Language" :name="'languages['.$langIndex.'][language_name]'" :value="old('languages.'.$langIndex.'.language_name', $language->language_name)" placeholder="e.g. English" required />
                            <x-form-select label="Proficiency" :name="'languages['.$langIndex.'][proficiency]'" placeholder="-- Select --">
                                @foreach (['Native', 'Fluent', 'Advanced', 'Intermediate', 'Basic'] as $prof)
                                    <option value="{{ $prof }}" {{ old("languages.{$langIndex}.proficiency", $language->proficiency) === $prof ? 'selected' : '' }}>{{ $prof }}</option>
                                @endforeach
                            </x-form-select>
                            <div class="flex items-center gap-2 mt-6">
                                <input type="checkbox" name="languages[{{ $langIndex }}][can_read]" value="1"
                                    {{ $language->can_read ? 'checked' : '' }}
                                    class="rounded border-slate-300 text-indigo-600 h-4 w-4">
                                <label class="text-sm text-slate-600">Read</label>
                            </div>
                            <div class="flex items-center gap-2 mt-6">
                                <input type="checkbox" name="languages[{{ $langIndex }}][can_write]" value="1"
                                    {{ $language->can_write ? 'checked' : '' }}
                                    class="rounded border-slate-300 text-indigo-600 h-4 w-4">
                                <label class="text-sm text-slate-600">Write</label>
                            </div>
                            <div class="flex items-center gap-2 mt-6">
                                <input type="checkbox" name="languages[{{ $langIndex }}][can_speak]" value="1"
                                    {{ $language->can_speak ? 'checked' : '' }}
                                    class="rounded border-slate-300 text-indigo-600 h-4 w-4">
                                <label class="text-sm text-slate-600">Speak</label>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <template id="language-template">
                <div class="repeater-row bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="font-semibold text-slate-700 text-sm">
                            <i class="fa-solid fa-language text-teal-500 mr-2"></i>
                            Language #<span class="row-number"></span>
                        </h4>
                        <button type="button" class="remove-row text-red-500 hover:text-red-700 text-xs font-medium px-3 py-1.5 rounded-lg hover:bg-red-50">
                            <i class="fa-solid fa-trash-can"></i> Remove
                        </button>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-4">
                        <x-form-input label="Language" name="languages[__INDEX__][language_name]" placeholder="e.g. English" required />
                        <x-form-select label="Proficiency" name="languages[__INDEX__][proficiency]" placeholder="-- Select --">
                            @foreach (['Native', 'Fluent', 'Advanced', 'Intermediate', 'Basic'] as $prof)
                                <option value="{{ $prof }}">{{ $prof }}</option>
                            @endforeach
                        </x-form-select>
                        <div class="flex items-center gap-2 mt-6">
                            <input type="checkbox" name="languages[__INDEX__][can_read]" value="1" class="rounded border-slate-300 text-indigo-600 h-4 w-4">
                            <label class="text-sm text-slate-600">Read</label>
                        </div>
                        <div class="flex items-center gap-2 mt-6">
                            <input type="checkbox" name="languages[__INDEX__][can_write]" value="1" class="rounded border-slate-300 text-indigo-600 h-4 w-4">
                            <label class="text-sm text-slate-600">Write</label>
                        </div>
                        <div class="flex items-center gap-2 mt-6">
                            <input type="checkbox" name="languages[__INDEX__][can_speak]" value="1" class="rounded border-slate-300 text-indigo-600 h-4 w-4">
                            <label class="text-sm text-slate-600">Speak</label>
                        </div>
                    </div>
                </div>
            </template>

            <button type="button" class="add-row-btn mt-6 w-full inline-flex items-center justify-center gap-2 rounded-xl border-2 border-dashed border-teal-300 px-5 py-3 text-sm font-semibold text-teal-600 hover:border-teal-500 hover:bg-teal-50 transition-all"
                data-container="#language-container" data-template="#language-template">
                <i class="fa-solid fa-plus-circle"></i> Add Language
            </button>

            <div class="mt-8 flex justify-end">
                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-8 py-3 text-sm font-bold text-white hover:from-emerald-700 hover:to-teal-700 transition-all shadow-lg">
                    <i class="fa-solid fa-floppy-disk"></i> Save Languages
                </button>
            </div>
        </div>
    </form>
</div>