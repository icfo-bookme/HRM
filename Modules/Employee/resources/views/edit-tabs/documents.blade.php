<div id="tab-documents" class="{{ $activeTab === 'documents' ? '' : 'hidden' }}">
    <form class="section-save-form" action="{{ route('employee.edit.documents', $employee->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center">
                    <i class="fa-solid fa-folder-open text-amber-600"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-900">Documents</h2>
                    <p class="text-xs text-slate-500">Official files</p>
                </div>
            </div>

            <div id="documents-container" class="space-y-6">
                @forelse ($employee->documents as $docIndex => $document)
                    <div class="document-row bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-200">
                        <div class="bg-gradient-to-r from-slate-50 to-white px-6 py-3.5 border-b border-slate-100 flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center">
                                    <i class="fa-solid fa-file text-indigo-500 text-sm"></i>
                                </div>
                                <h3 class="font-semibold text-slate-800 text-sm">
                                    Document #{{ $loop->iteration }}
                                    @if($document->document_name)
                                        — {{ $document->document_name }}
                                    @endif
                                </h3>
                            </div>
                            <button type="button" class="remove-document inline-flex items-center gap-1.5 text-xs font-medium text-red-500 hover:text-red-700 hover:bg-red-50 px-3 py-1.5 rounded-lg transition-colors">
                                <i class="fa-solid fa-trash-can"></i> Remove
                            </button>
                        </div>
                        <div class="p-6 space-y-5">
                            <input type="hidden" name="documents[{{ $docIndex }}][id]" value="{{ $document->id }}">
                            <div class="grid gap-5 sm:grid-cols-2">
                                <div>
                                    <label class="font-semibold text-sm text-slate-700 block mb-1.5">Document Category <span class="text-rose-500">*</span></label>
                                    <select name="documents[{{ $docIndex }}][category]"
                                        class="w-full border border-slate-300 rounded-xl p-2.5 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all cursor-pointer">
                                        <option value="">-- Select Category --</option>
                                        @foreach (['nid' => 'NID', 'passport' => 'Passport', 'tin' => 'TIN Certificate', 'birth_certificate' => 'Birth Certificate', 'driving_license' => 'Driving License', 'education_certificate' => 'Education Certificate', 'experience_certificate' => 'Experience Certificate', 'cv' => 'CV / Resume', 'photo' => 'Photograph', 'signature' => 'Signature', 'other' => 'Other'] as $val => $label)
                                            <option value="{{ $val }}" {{ old("documents.{$docIndex}.category", $document->category) === $val ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <x-form-input label="Document Name" :name="'documents['.$docIndex.'][document_name]'"
                                        placeholder="e.g. National ID Card"
                                        :value="old('documents.'.$docIndex.'.document_name', $document->document_name)" />
                                </div>
                            </div>

                            <div>
                                <label class="font-semibold text-sm text-slate-700 block mb-1.5">Upload File <span class="text-rose-500">*</span></label>
                                <div class="relative">
                                    <input type="file" name="documents[{{ $docIndex }}][document_file]"
                                        class="file-input block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-5 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100 cursor-pointer border border-slate-300 rounded-xl p-1.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                                </div>
                                @if (!empty($document->file_path))
                                    <p class="text-xs text-slate-500 mt-2 flex items-center gap-1.5">
                                        <i class="fa-solid fa-paperclip text-slate-400"></i>
                                        Current file:
                                        <a href="{{ asset('storage/' . $document->file_path) }}" target="_blank"
                                           class="text-indigo-600 hover:text-indigo-800 underline font-medium inline-flex items-center gap-1">
                                            <i class="fa-solid fa-eye"></i> {{ basename($document->file_path) }}
                                        </a>
                                    </p>
                                    <input type="hidden" name="documents[{{ $docIndex }}][file_path]" value="{{ $document->file_path }}">
                                @endif
                            </div>

                            <div class="grid gap-5 sm:grid-cols-2">
                                <x-form-input label="Document Number" :name="'documents['.$docIndex.'][document_number]'"
                                    placeholder="Reference number (optional)"
                                    :value="old('documents.'.$docIndex.'.document_number', $document->document_number)" />
                                <x-form-input label="Issuing Authority" :name="'documents['.$docIndex.'][issuing_authority]'"
                                    placeholder="Issuer name (optional)"
                                    :value="old('documents.'.$docIndex.'.issuing_authority', $document->issuing_authority)" />
                            </div>

                            <div class="grid gap-5 sm:grid-cols-2">
                                <x-form-input label="Issue Date" :name="'documents['.$docIndex.'][issue_date]'" type="date"
                                    :value="old('documents.'.$docIndex.'.issue_date', $document->issue_date)" />
                                <x-form-input label="Expiry Date" :name="'documents['.$docIndex.'][expiry_date]'" type="date"
                                    :value="old('documents.'.$docIndex.'.expiry_date', $document->expiry_date)" />
                            </div>

                            <div>
                                <x-form-textarea label="Notes" :name="'documents['.$docIndex.'][notes]'" rows="2"
                                    placeholder="Additional notes (optional)">{{ old('documents.'.$docIndex.'.notes', $document->notes) }}</x-form-textarea>
                            </div>
                        </div>
                    </div>
                @empty
                    {{-- Always show at least one empty row --}}
                    <div class="document-row bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
                        <div class="bg-gradient-to-r from-slate-50 to-white px-6 py-3.5 border-b border-slate-100 flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center">
                                    <i class="fa-solid fa-file text-indigo-500 text-sm"></i>
                                </div>
                                <h3 class="font-semibold text-slate-800 text-sm">Document #1</h3>
                            </div>
                            <button type="button" class="remove-document text-red-500 hover:text-red-700 text-xs font-medium px-3 py-1.5 rounded-lg hover:bg-red-50 transition-colors">
                                <i class="fa-solid fa-trash-can"></i> Remove
                            </button>
                        </div>
                        <div class="p-6 space-y-5">
                            <div class="grid gap-5 sm:grid-cols-2">
                                <div>
                                    <label class="font-semibold text-sm text-slate-700 block mb-1.5">Category</label>
                                    <select name="documents[0][category]" class="w-full border border-slate-300 rounded-xl p-2.5 text-sm">
                                        <option value="">-- Select --</option>
                                        @foreach (['nid'=>'NID','passport'=>'Passport','cv'=>'CV','education_certificate'=>'Education Cert','other'=>'Other'] as $v=>$l)
                                            <option value="{{ $v }}">{{ $l }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <x-form-input label="Document Name" name="documents[0][document_name]" placeholder="e.g. National ID Card" />
                                </div>
                            </div>
                            <div>
                                <label class="font-semibold text-sm text-slate-700 block mb-1.5">Upload File <span class="text-rose-500">*</span></label>
                                <input type="file" name="documents[0][document_file]" class="block w-full text-sm border border-slate-300 rounded-xl p-2">
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>

            <button type="button" id="add-document"
                class="mt-6 w-full inline-flex items-center justify-center gap-2 rounded-xl border-2 border-dashed border-sky-300 px-5 py-3 text-sm font-semibold text-sky-600 hover:border-sky-500 hover:bg-sky-50 transition-all">
                <i class="fa-solid fa-plus-circle"></i> Add Another Document
            </button>

            <div class="mt-8 flex justify-end">
                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-8 py-3 text-sm font-bold text-white hover:from-emerald-700 hover:to-teal-700 transition-all shadow-lg">
                    <i class="fa-solid fa-floppy-disk"></i> Save Documents
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    let documentIndex = Math.max({{ $employee->documents->count() }}, 1);

    document.getElementById('add-document')?.addEventListener('click', function() {
        const container = document.getElementById('documents-container');
        const i = documentIndex;
        const html = `
            <div class="document-row bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
                <div class="bg-gradient-to-r from-slate-50 to-white px-6 py-3.5 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center">
                            <i class="fa-solid fa-file text-indigo-500 text-sm"></i>
                        </div>
                        <h3 class="font-semibold text-slate-800 text-sm">Document #<span class="doc-number">${i + 1}</span></h3>
                    </div>
                    <button type="button" class="remove-document text-red-500 hover:text-red-700 text-xs font-medium px-3 py-1.5 rounded-lg hover:bg-red-50 transition-colors">
                        <i class="fa-solid fa-trash-can"></i> Remove
                    </button>
                </div>
                <div class="p-6 space-y-5">
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label class="font-semibold text-sm text-slate-700 block mb-1.5">Category</label>
                            <select name="documents[${i}][category]" class="w-full border border-slate-300 rounded-xl p-2.5 text-sm">
                                <option value="">-- Select --</option>
                                @foreach (['nid'=>'NID','passport'=>'Passport','cv'=>'CV','education_certificate'=>'Education Cert','other'=>'Other'] as $v=>$l)
                                    <option value="{{ $v }}">{{ $l }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-form-input label="Document Name" name="documents[${i}][document_name]" placeholder="e.g. National ID Card" />
                        </div>
                    </div>
                    <div>
                        <label class="font-semibold text-sm text-slate-700 block mb-1.5">File</label>
                        <input type="file" name="documents[${i}][document_file]" class="block w-full text-sm border border-slate-300 rounded-xl p-2">
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
        documentIndex++;
    });

    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-document')) {
            e.target.closest('.document-row').remove();
        }
    });
</script>
@endpush