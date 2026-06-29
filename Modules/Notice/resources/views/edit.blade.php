<x-app-layout>

    @push('head')
        {{-- CKEditor 5 Classic CDN --}}
        <script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>
        <style>
            .ck-editor__editable_inline {
                min-height: 250px;
            }
        </style>
    @endpush

    <div class="p-4 max-w-4xl mx-auto">
        <div class="bg-white border border-slate-200 shadow-sm rounded-3xl p-8">
            <div class="mb-8 flex items-start justify-between">
                <div>
                    <h1 class="text-3xl font-semibold text-slate-900">Edit Notice</h1>
                    <p class="mt-2 text-sm text-slate-600">Update the notice details below.</p>
                </div>
            </div>

            @if (session('success'))
                <div class="rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-emerald-700 mb-6">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="rounded-2xl bg-rose-50 border border-rose-200 p-4 text-rose-700 mb-6">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('notice.update.page', $notice->id) }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- Title --}}
                <x-form-input label="Notice Title" name="title" placeholder="Enter notice title" required
                    value="{{ old('title', $notice->title) }}" />

                {{-- Notice Type & Priority --}}
                <div class="grid gap-6 sm:grid-cols-2">
                    <x-form-select label="Notice Type" name="notice_type" placeholder="Select Type" required>
                        @foreach ($noticeTypes as $value => $label)
                            <option value="{{ $value }}" {{ old('notice_type', $notice->notice_type) === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </x-form-select>
                    <x-form-select label="Priority" name="priority" placeholder="Select Priority" required>
                        @foreach ($priorities as $value => $label)
                            <option value="{{ $value }}" {{ old('priority', $notice->priority) === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </x-form-select>
                </div>

                {{-- Dates --}}
                <div class="grid gap-6 sm:grid-cols-2">
                    <x-form-input label="Publish Date" name="publish_date" type="datetime-local" required
                        value="{{ old('publish_date', $notice->publish_date ? $notice->publish_date->format('Y-m-d\TH:i') : '') }}" />
                    <x-form-input label="Expiry Date" name="expiry_date" type="datetime-local"
                        value="{{ old('expiry_date', $notice->expiry_date ? $notice->expiry_date->format('Y-m-d\TH:i') : '') }}" />
                </div>

                {{-- Target Type & Branch --}}
                <div class="grid gap-6 sm:grid-cols-2">
                    <x-form-select label="Target Type" name="target_type" placeholder="Select Target" required>
                        @foreach (['All', 'Department', 'Designation', 'Branch', 'Employee'] as $type)
                            <option value="{{ $type }}" {{ old('target_type', $notice->target_type) === $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </x-form-select>
                    <x-form-select label="Branch" name="branch_id" placeholder="-- Select Branch --">
                        <option value="">All Branches</option>
                        @foreach ($branches as $id => $name)
                            <option value="{{ $id }}" {{ old('branch_id', $notice->branch_id) == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </x-form-select>
                </div>

                {{-- Description (CKEditor) --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Description <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="description" id="description" rows="10"
                        placeholder="Enter notice description...">{{ old('description', $notice->description) }}</textarea>
                    <p class="text-xs text-slate-400 mt-2">
                        <i class="fas fa-info-circle"></i>
                        Use the toolbar above to format the notice content (bold, italic, lists, etc.)
                    </p>
                </div>

                {{-- Attachment --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Attachment File</label>
                    @if($notice->attachment_path)
                        <div class="mb-2 flex items-center gap-2 text-sm text-slate-600 bg-slate-50 rounded-lg px-3 py-2 border border-slate-200">
                            <i class="fas fa-paperclip text-indigo-500"></i>
                            <span>{{ basename($notice->attachment_path) }}</span>
                            <label class="ml-auto flex items-center gap-1.5 text-xs text-red-600 cursor-pointer">
                                <input type="checkbox" name="remove_attachment" value="1">
                                Remove existing file
                            </label>
                        </div>
                    @endif
                    <input type="file" name="attachment"
                        class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
                    <p class="text-xs text-slate-400 mt-1">Accepted: jpg, png, pdf, doc, docx (Max: 5MB)</p>
                </div>

                {{-- Checkboxes --}}
                <div class="space-y-3 bg-slate-50 p-4 rounded-2xl border border-slate-200">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="hidden" name="is_popup" value="0">
                        <input type="checkbox" name="is_popup" value="1" {{ old('is_popup', $notice->is_popup) ? 'checked' : '' }}
                            class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500">
                        <span class="text-sm font-semibold text-slate-700">Show as Popup</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="hidden" name="is_pinned" value="0">
                        <input type="checkbox" name="is_pinned" value="1" {{ old('is_pinned', $notice->is_pinned) ? 'checked' : '' }}
                            class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500">
                        <span class="text-sm font-semibold text-slate-700">Pin to Top</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $notice->is_active) ? 'checked' : '' }}
                            class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500">
                        <span class="text-sm font-semibold text-slate-700">Active</span>
                    </label>
                </div>

                {{-- Actions --}}
                <div class="flex justify-between items-center pt-4 border-t border-slate-200">
                    <a href="{{ route('notice.manage') }}"
                        class="text-sm font-medium text-slate-600 hover:text-slate-900">&larr; Back to List</a>
                    <button type="submit"
                        class="inline-flex items-center justify-center rounded-2xl bg-sky-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2">
                        Update Notice
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            let ckeditorInstance = null;

            // ===== CKEDITOR INIT =====
            ClassicEditor
                .create(document.querySelector('#description'), {
                    toolbar: [
                        'heading', '|',
                        'bold', 'italic', 'underline', 'strikethrough', '|',
                        'bulletedList', 'numberedList', '|',
                        'alignment', '|',
                        'indent', 'outdent', '|',
                        'fontColor', 'fontBackgroundColor', '|',
                        'blockQuote', 'link', '|',
                        'undo', 'redo'
                    ],
                    placeholder: 'Enter notice description...',
                })
                .then(editor => {
                    ckeditorInstance = editor;
                })
                .catch(error => {
                    console.error('CKEditor initialization error:', error);
                });

            // ===== FORM SUBMIT - Update CKEditor content into textarea =====
            document.querySelector('form').addEventListener('submit', function() {
                if (ckeditorInstance) {
                    document.querySelector('#description').value = ckeditorInstance.getData();
                }
            });
        </script>
    @endpush
</x-app-layout>