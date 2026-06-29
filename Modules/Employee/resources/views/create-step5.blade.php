<x-app-layout>
    @include('employee::components.wizard-progress', ['current' => 5])

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white border border-slate-200 shadow-sm rounded-3xl p-8">
            <div class="mb-8 flex items-start justify-between">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 rounded-2xl bg-indigo-100 flex items-center justify-center">
                        <i class="fa-solid fa-folder-open text-indigo-600 text-lg"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-semibold text-slate-900">Document Upload</h1>
                        <p class="text-sm text-slate-500">Upload required documents (NID, CV) and add more if needed</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('employee.reset.step', 5) }}">
                    @csrf
                    <button type="submit" onclick="return confirm('Reset Step 5 data? This will clear all entered information for this step.')"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-amber-300 px-4 py-2 text-sm font-semibold text-amber-700 transition hover:bg-amber-50 focus:outline-none focus:ring-2 focus:ring-amber-400">
                        ⟳ Refresh
                    </button>
                </form>
            </div>

            @if ($errors->any())
                <div class="rounded-2xl bg-amber-50 border border-amber-200 p-4 text-amber-700 mb-6">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('employee.store.step5') }}" enctype="multipart/form-data">
                @csrf

                <div id="documents-container" class="space-y-6">
                    @php
                        $existingDocs = $data['documents'] ?? [];
                    @endphp

                    @if (count($existingDocs) > 0)
                        @foreach ($existingDocs as $docIndex => $document)
                            @include('employee::components.document-row', [
                                'index' => $docIndex,
                                'document' => $document,
                                'loop' => $loop,
                            ])
                        @endforeach
                    @else
                        {{-- Default: NID Document Row --}}
                        @include('employee::components.document-row', [
                            'index' => 0,
                            'document' => [
                                'category' => 'nid',
                                'document_name' => 'National ID Card',
                            ],
                            'defaultCategory' => 'nid',
                            'displayTitle' => 'NID',
                        ])

                        {{-- Default: CV Document Row --}}
                        @include('employee::components.document-row', [
                            'index' => 1,
                            'document' => [
                                'category' => 'cv',
                                'document_name' => 'CV / Resume',
                            ],
                            'defaultCategory' => 'cv',
                            'displayTitle' => 'CV / Resume',
                        ])
                    @endif
                </div>

                {{-- Add Document Button --}}
                <div class="mt-6 mb-8">
                    <button type="button" id="add-document"
                        class="group inline-flex items-center gap-2.5 rounded-xl border-2 border-dashed border-sky-300 px-5 py-3 text-sm font-semibold text-sky-600 hover:border-sky-500 hover:bg-sky-50 transition-all duration-200 w-full justify-center">
                        <i class="fa-solid fa-plus-circle text-lg group-hover:scale-110 transition-transform"></i>
                        Add Another Document
                    </button>
                </div>

                {{-- Actions --}}
                <div class="flex justify-between items-center pt-6 border-t border-slate-200">
                    <a href="{{ route('employee.create.step4') }}"
                        class="inline-flex items-center gap-2 text-sm font-medium text-slate-600 hover:text-slate-900 transition-colors">
                        <i class="fa-solid fa-arrow-left"></i> Back to Step 4
                    </a>
                    <div class="flex items-center gap-3">
                        <button type="submit" name="skip" value="1"
                            class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-300">
                            Skip
                        </button>
                        <button type="submit"
                            class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-sky-600 to-indigo-600 px-6 py-2.5 text-sm font-semibold text-white transition hover:from-sky-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 shadow-sm">
                            Next Step
                            <i class="fa-solid fa-arrow-right ml-2"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        @php
            $existingDocsCount = count($data['documents'] ?? []);
        @endphp
        let documentIndex = {{ $existingDocsCount > 0 ? $existingDocsCount : 2 }};

        const categoryList = [
            { value: 'nid', label: 'NID', icon: 'fa-id-card' },
            { value: 'passport', label: 'Passport', icon: 'fa-passport' },
            { value: 'tin', label: 'TIN Certificate', icon: 'fa-file-invoice' },
            { value: 'birth_certificate', label: 'Birth Certificate', icon: 'fa-calendar-alt' },
            { value: 'driving_license', label: 'Driving License', icon: 'fa-car' },
            { value: 'education_certificate', label: 'Education Certificate', icon: 'fa-graduation-cap' },
            { value: 'experience_certificate', label: 'Experience Certificate', icon: 'fa-briefcase' },
            { value: 'cv', label: 'CV / Resume', icon: 'fa-file-alt' },
            { value: 'photo', label: 'Photograph', icon: 'fa-camera' },
            { value: 'signature', label: 'Signature', icon: 'fa-pen' },
            { value: 'other', label: 'Other', icon: 'fa-file' },
        ];

        document.getElementById('add-document').addEventListener('click', function() {
            const container = document.getElementById('documents-container');
            const i = documentIndex;

            const html = `
                <div class="document-row bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-200">
                    <div class="bg-gradient-to-r from-slate-50 to-white px-6 py-3.5 border-b border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center">
                                <i class="fa-solid fa-file text-indigo-500 text-sm"></i>
                            </div>
                            <h3 class="font-semibold text-slate-800 text-sm">Document #<span class="doc-number">${i + 1}</span></h3>
                        </div>
                        <button type="button" class="remove-document inline-flex items-center gap-1.5 text-xs font-medium text-red-500 hover:text-red-700 hover:bg-red-50 px-3 py-1.5 rounded-lg transition-colors">
                            <i class="fa-solid fa-trash-can"></i> Remove
                        </button>
                    </div>

                    <div class="p-6 space-y-5">
                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label class="font-semibold text-sm text-slate-700 block mb-1.5">Document Category <span class="text-rose-500">*</span></label>
                                <select name="documents[${i}][category]" class="select2 w-full" id="doc-category-${i}">
                                    <option value="">-- Select Category --</option>
                                    ${categoryList.map(c => `<option value="${c.value}">${c.label}</option>`).join('')}
                                </select>
                            </div>
                            <div>
                                <label class="font-semibold text-sm text-slate-700 block mb-1.5">Document Name</label>
                                <input type="text" name="documents[${i}][document_name]" placeholder="e.g. National ID Card"
                                    class="w-full border border-slate-300 rounded-xl p-2.5 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all placeholder:text-slate-400">
                            </div>
                        </div>

                        <div>
                            <label class="font-semibold text-sm text-slate-700 block mb-1.5">Upload File <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <input type="file" name="documents[${i}][document_file]"
                                    class="file-input block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-5 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100 cursor-pointer border border-slate-300 rounded-xl p-1.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                            </div>
                            <div class="file-info mt-2 hidden">
                                <div class="flex items-center gap-2 text-xs text-emerald-600 bg-emerald-50 rounded-lg px-3 py-2">
                                    <i class="fa-solid fa-check-circle"></i>
                                    <span class="file-name"></span>
                                </div>
                            </div>
                        </div>

                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label class="font-semibold text-sm text-slate-700 block mb-1.5">Document Number</label>
                                <input type="text" name="documents[${i}][document_number]" placeholder="Reference number (optional)"
                                    class="w-full border border-slate-300 rounded-xl p-2.5 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all placeholder:text-slate-400">
                            </div>
                            <div>
                                <label class="font-semibold text-sm text-slate-700 block mb-1.5">Issuing Authority</label>
                                <input type="text" name="documents[${i}][issuing_authority]" placeholder="Issuer name (optional)"
                                    class="w-full border border-slate-300 rounded-xl p-2.5 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all placeholder:text-slate-400">
                            </div>
                        </div>

                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label class="font-semibold text-sm text-slate-700 block mb-1.5">Issue Date</label>
                                <input type="date" name="documents[${i}][issue_date]"
                                    class="w-full border border-slate-300 rounded-xl p-2.5 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all">
                            </div>
                            <div>
                                <label class="font-semibold text-sm text-slate-700 block mb-1.5">Expiry Date</label>
                                <input type="date" name="documents[${i}][expiry_date]"
                                    class="w-full border border-slate-300 rounded-xl p-2.5 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all">
                            </div>
                        </div>

                        <div>
                            <label class="font-semibold text-sm text-slate-700 block mb-1.5">Notes</label>
                            <textarea name="documents[${i}][notes]" rows="2" placeholder="Additional notes (optional)"
                                class="w-full border border-slate-300 rounded-xl p-2.5 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all placeholder:text-slate-400"></textarea>
                        </div>
                    </div>
                </div>
            `;

            container.insertAdjacentHTML('beforeend', html);
            $('#doc-category-' + i).select2({
                placeholder: '-- Select Category --',
                width: '100%'
            });
            documentIndex++;
        });

        // File input change handler
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('file-input')) {
                const row = e.target.closest('.document-row');
                const info = row.querySelector('.file-info');
                const name = row.querySelector('.file-name');
                if (e.target.files && e.target.files[0]) {
                    name.textContent = e.target.files[0].name;
                    info.classList.remove('hidden');
                } else {
                    info.classList.add('hidden');
                }
            }
        });

        // Remove document
        document.addEventListener('click', function(e) {
            if (e.target.closest('.remove-document')) {
                const row = e.target.closest('.document-row');
                Swal.fire({
                    title: 'Remove this document?',
                    text: "You can add it again later if needed.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, remove',
                    cancelButtonText: 'Cancel'
                }).then((r) => {
                    if (r.isConfirmed) {
                        row.remove();
                    }
                });
            }
        });
    </script>
    @endpush
</x-app-layout>