@props([
    'index' => 0,
    'document' => [],
    'defaultCategory' => null,
    'displayTitle' => null,
])

<div class="document-row bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-200">
    <div class="bg-gradient-to-r from-slate-50 to-white px-6 py-3.5 border-b border-slate-100 flex items-center justify-between">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center">
                <i class="fa-solid fa-file text-indigo-500 text-sm"></i>
            </div>
            <h3 class="font-semibold text-slate-800 text-sm">
                Document #<span class="doc-number">{{ $index + 1 }}</span>
                @if($displayTitle)
                    — {{ $displayTitle }}
                @endif
            </h3>
        </div>
        <button type="button" class="remove-document inline-flex items-center gap-1.5 text-xs font-medium text-red-500 hover:text-red-700 hover:bg-red-50 px-3 py-1.5 rounded-lg transition-colors">
            <i class="fa-solid fa-trash-can"></i> Remove
        </button>
    </div>

    <div class="p-6 space-y-5">
        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label class="font-semibold text-sm text-slate-700 block mb-1.5">Document Category <span class="text-rose-500">*</span></label>
                <select name="documents[{{ $index }}][category]"
                    class="w-full border border-slate-300 rounded-xl p-2.5 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all cursor-pointer">
                    <option value="">-- Select Category --</option>
                    @foreach (['nid' => 'NID', 'passport' => 'Passport', 'tin' => 'TIN Certificate', 'birth_certificate' => 'Birth Certificate', 'driving_license' => 'Driving License', 'education_certificate' => 'Education Certificate', 'experience_certificate' => 'Experience Certificate', 'cv' => 'CV / Resume', 'photo' => 'Photograph', 'signature' => 'Signature', 'other' => 'Other'] as $val => $label)
                        <option value="{{ $val }}" {{ old("documents.{$index}.category", $document['category'] ?? $defaultCategory) === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <x-form-input label="Document Name" name="documents[{{ $index }}][document_name]"
                    placeholder="e.g. National ID Card"
                    :value="old(\"documents.{$index}.document_name\", $document['document_name'] ?? '')" />
            </div>
        </div>

        <div>
            <label class="font-semibold text-sm text-slate-700 block mb-1.5">Upload File <span class="text-rose-500">*</span></label>
            <div class="relative">
                <input type="file" name="documents[{{ $index }}][document_file]"
                    class="file-input block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-5 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100 cursor-pointer border border-slate-300 rounded-xl p-1.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
            </div>
            @if (!empty($document['file_path']))
                <p class="text-xs text-slate-500 mt-1.5 flex items-center gap-1.5">
                    <i class="fa-solid fa-paperclip text-slate-400"></i>
                    Current: {{ basename($document['file_path']) }}
                </p>
                <input type="hidden" name="documents[{{ $index }}][file_path]" value="{{ $document['file_path'] }}">
            @endif
            <div class="file-info mt-2 {{ empty($document['file_path']) ? 'hidden' : '' }}">
                <div class="flex items-center gap-2 text-xs text-emerald-600 bg-emerald-50 rounded-lg px-3 py-2">
                    <i class="fa-solid fa-check-circle"></i>
                    <span class="file-name">{{ !empty($document['file_path']) ? basename($document['file_path']) : '' }}</span>
                </div>
            </div>
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <x-form-input label="Document Number" name="documents[{{ $index }}][document_number]"
                placeholder="Reference number (optional)"
                :value="old(\"documents.{$index}.document_number\", $document['document_number'] ?? '')" />
            <x-form-input label="Issuing Authority" name="documents[{{ $index }}][issuing_authority]"
                placeholder="Issuer name (optional)"
                :value="old(\"documents.{$index}.issuing_authority\", $document['issuing_authority'] ?? '')" />
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <x-form-input label="Issue Date" name="documents[{{ $index }}][issue_date]" type="date"
                :value="old(\"documents.{$index}.issue_date\", $document['issue_date'] ?? '')" />
            <x-form-input label="Expiry Date" name="documents[{{ $index }}][expiry_date]" type="date"
                :value="old(\"documents.{$index}.expiry_date\", $document['expiry_date'] ?? '')" />
        </div>

        <div>
            <x-form-textarea label="Notes" name="documents[{{ $index }}][notes]" rows="2"
                placeholder="Additional notes (optional)">{{ old("documents.{$index}.notes", $document['notes'] ?? '') }}</x-form-textarea>
        </div>
    </div>
</div>