<div id="tab-experience" class="{{ $activeTab === 'experience' ? '' : 'hidden' }}">
    <form class="section-save-form" action="{{ route('employee.edit.experience', $employee->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-orange-100 flex items-center justify-center">
                    <i class="fa-solid fa-briefcase text-orange-600"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-900">Experience</h2>
                    <p class="text-xs text-slate-500">Work history</p>
                </div>
            </div>

            <div id="experience-container" class="space-y-6">
                @foreach ($employee->experiences as $expIndex => $experience)
                    <div class="repeater-row bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
                        <div class="bg-gradient-to-r from-slate-50 to-white px-6 py-3.5 border-b border-slate-100 flex items-center justify-between">
                            <h3 class="font-semibold text-slate-800 text-sm">
                                <i class="fa-solid fa-briefcase text-orange-500 mr-2"></i>
                                Experience #{{ $loop->iteration }}
                            </h3>
                            <button type="button" class="remove-row text-red-500 hover:text-red-700 text-xs font-medium px-3 py-1.5 rounded-lg hover:bg-red-50">
                                <i class="fa-solid fa-trash-can"></i> Remove
                            </button>
                        </div>
                        <div class="p-6 space-y-5">
                            <input type="hidden" name="experiences[{{ $expIndex }}][id]" value="{{ $experience->id }}">
                            <div class="grid gap-5 sm:grid-cols-2">
                                <x-form-input label="Company Name" :name="'experiences['.$expIndex.'][company_name]'" :value="old('experiences.'.$expIndex.'.company_name', $experience->company_name)" required />
                                <x-form-input label="Designation" :name="'experiences['.$expIndex.'][designation]'" :value="old('experiences.'.$expIndex.'.designation', $experience->designation)" />
                                <x-form-input label="Department" :name="'experiences['.$expIndex.'][department]'" :value="old('experiences.'.$expIndex.'.department', $experience->department)" />
                                <x-form-input label="From Date" :name="'experiences['.$expIndex.'][from_date]'" type="date" :value="old('experiences.'.$expIndex.'.from_date', $experience->from_date?->format('Y-m-d') ?? '')" />
                                <x-form-input label="To Date" :name="'experiences['.$expIndex.'][to_date]'" type="date" :value="old('experiences.'.$expIndex.'.to_date', $experience->to_date?->format('Y-m-d') ?? '')" />
                                <div class="flex items-center gap-3 mt-6">
                                    <input type="checkbox" name="experiences[{{ $expIndex }}][is_current]" value="1"
                                        {{ $experience->is_current ? 'checked' : '' }}
                                        class="rounded border-slate-300 text-indigo-600 h-4 w-4">
                                    <label class="text-sm text-slate-700">I currently work here</label>
                                </div>
                                <div class="sm:col-span-2">
                                    <x-form-textarea label="Responsibilities" :name="'experiences['.$expIndex.'][responsibilities]'" rows="3">{{ old('experiences.'.$expIndex.'.responsibilities', $experience->responsibilities) }}</x-form-textarea>
                                </div>
                                <div class="sm:col-span-2">
                                    <x-form-textarea label="Achievements" :name="'experiences['.$expIndex.'][achievements]'" rows="2">{{ old('experiences.'.$expIndex.'.achievements', $experience->achievements) }}</x-form-textarea>
                                </div>
                                <x-form-input label="Reason for Leaving" :name="'experiences['.$expIndex.'][reason_for_leaving]'" :value="old('experiences.'.$expIndex.'.reason_for_leaving', $experience->reason_for_leaving)" />
                                <x-form-input label="Salary Scale" :name="'experiences['.$expIndex.'][salary_scale]'" :value="old('experiences.'.$expIndex.'.salary_scale', $experience->salary_scale)" />
                                <x-form-input label="Reference Name" :name="'experiences['.$expIndex.'][reference_name]'" :value="old('experiences.'.$expIndex.'.reference_name', $experience->reference_name)" />
                                <x-form-input label="Reference Phone" :name="'experiences['.$expIndex.'][reference_phone]'" :value="old('experiences.'.$expIndex.'.reference_phone', $experience->reference_phone)" />
                                <x-form-input label="Reference Email" :name="'experiences['.$expIndex.'][reference_email]'" type="email" :value="old('experiences.'.$expIndex.'.reference_email', $experience->reference_email)" />
                                <div>
                                    <label class="font-semibold text-sm text-slate-700 block mb-1.5">Certificate File</label>
                                    <input type="file" :name="'experiences['.$expIndex.'][certificate_file]'" accept=".pdf,.jpg,.png"
                                        class="block w-full text-sm border border-slate-300 rounded-xl p-2">
                                    @if($experience->certificate_path)
                                        <p class="text-xs text-slate-500 mt-1">Current: {{ basename($experience->certificate_path) }}</p>
                                        <input type="hidden" name="experiences[{{ $expIndex }}][certificate_path]" value="{{ $experience->certificate_path }}">
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <template id="experience-template">
                <div class="repeater-row bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
                    <div class="bg-gradient-to-r from-slate-50 to-white px-6 py-3.5 border-b border-slate-100 flex items-center justify-between">
                        <h3 class="font-semibold text-slate-800 text-sm">
                            <i class="fa-solid fa-briefcase text-orange-500 mr-2"></i>
                            Experience #<span class="row-number"></span>
                        </h3>
                        <button type="button" class="remove-row text-red-500 hover:text-red-700 text-xs font-medium px-3 py-1.5 rounded-lg hover:bg-red-50">
                            <i class="fa-solid fa-trash-can"></i> Remove
                        </button>
                    </div>
                    <div class="p-6 space-y-5">
                        <div class="grid gap-5 sm:grid-cols-2">
                            <x-form-input label="Company Name" name="experiences[__INDEX__][company_name]" required />
                            <x-form-input label="Designation" name="experiences[__INDEX__][designation]" />
                            <x-form-input label="Department" name="experiences[__INDEX__][department]" />
                            <x-form-input label="From Date" name="experiences[__INDEX__][from_date]" type="date" />
                            <x-form-input label="To Date" name="experiences[__INDEX__][to_date]" type="date" />
                            <div class="flex items-center gap-3 mt-6">
                                <input type="checkbox" name="experiences[__INDEX__][is_current]" value="1" class="rounded border-slate-300 text-indigo-600 h-4 w-4">
                                <label class="text-sm text-slate-700">I currently work here</label>
                            </div>
                            <div class="sm:col-span-2">
                                <x-form-textarea label="Responsibilities" name="experiences[__INDEX__][responsibilities]" rows="3"></x-form-textarea>
                            </div>
                            <div class="sm:col-span-2">
                                <x-form-textarea label="Achievements" name="experiences[__INDEX__][achievements]" rows="2"></x-form-textarea>
                            </div>
                            <x-form-input label="Reason for Leaving" name="experiences[__INDEX__][reason_for_leaving]" />
                            <x-form-input label="Salary Scale" name="experiences[__INDEX__][salary_scale]" />
                            <x-form-input label="Reference Name" name="experiences[__INDEX__][reference_name]" />
                            <x-form-input label="Reference Phone" name="experiences[__INDEX__][reference_phone]" />
                            <x-form-input label="Reference Email" name="experiences[__INDEX__][reference_email]" type="email" />
                            <div>
                                <label class="font-semibold text-sm text-slate-700 block mb-1.5">Certificate File</label>
                                <input type="file" name="experiences[__INDEX__][certificate_file]" accept=".pdf,.jpg,.png" class="block w-full text-sm border border-slate-300 rounded-xl p-2">
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <button type="button" class="add-row-btn mt-6 w-full inline-flex items-center justify-center gap-2 rounded-xl border-2 border-dashed border-orange-300 px-5 py-3 text-sm font-semibold text-orange-600 hover:border-orange-500 hover:bg-orange-50 transition-all"
                data-container="#experience-container" data-template="#experience-template">
                <i class="fa-solid fa-plus-circle"></i> Add Experience
            </button>

            <div class="mt-8 flex justify-end">
                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-8 py-3 text-sm font-bold text-white hover:from-emerald-700 hover:to-teal-700 transition-all shadow-lg">
                    <i class="fa-solid fa-floppy-disk"></i> Save Experience
                </button>
            </div>
        </div>
    </form>
</div>