<x-app-layout>
    @include('employee::components.wizard-progress', ['current' => 9])

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white border border-slate-200 shadow-sm rounded-3xl p-8">

            <div class="mb-8 flex items-start justify-between">
                <div>
                    <h1 class="text-3xl font-semibold text-slate-900">Employee Registration</h1>
                    <p class="mt-2 text-sm text-slate-600">Step 9: Add language skills. You can add multiple entries.</p>
                </div>
                <form method="POST" action="{{ route('employee.reset.step', 9) }}">
                    @csrf
                    <button type="submit" onclick="return confirm('Reset Step 9 data? This will clear all entered information for this step.')"
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

            <form method="POST" action="{{ route('employee.store.step9') }}">
                @csrf

                <div id="languages-container">
                    {{-- Default Language Row --}}
                    <div class="language-row border border-slate-200 rounded-2xl p-6 mb-6 bg-slate-50">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-semibold text-slate-800">Language #1</h3>
                            <button type="button" class="remove-language hidden text-red-600 text-sm font-medium">Remove</button>
                        </div>

                        <div class="grid gap-6 sm:grid-cols-2">
                            <x-form-input label="Language Name" name="languages[0][language_name]" id="languages_0_language_name" placeholder="English" />
                            <div>
                                <label class="font-semibold text-sm text-slate-700 block mb-1">Proficiency</label>
                                <select name="languages[0][proficiency]" id="languages_0_proficiency"
                                    class="w-full border border-slate-300 rounded-md p-2 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all cursor-pointer">
                                    <option value="">-- Select Proficiency --</option>
                                    <option value="Basic">Basic</option>
                                    <option value="Conversational">Conversational</option>
                                    <option value="Professional">Professional</option>
                                    <option value="Native">Native</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex gap-6 mt-4">
                            <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700 cursor-pointer">
                                <input type="hidden" name="languages[0][can_read]" value="0">
                                <input type="checkbox" name="languages[0][can_read]" value="1"
                                    class="rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                                Can Read
                            </label>
                            <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700 cursor-pointer">
                                <input type="hidden" name="languages[0][can_write]" value="0">
                                <input type="checkbox" name="languages[0][can_write]" value="1"
                                    class="rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                                Can Write
                            </label>
                            <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700 cursor-pointer">
                                <input type="hidden" name="languages[0][can_speak]" value="0">
                                <input type="checkbox" name="languages[0][can_speak]" value="1"
                                    class="rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                                Can Speak
                            </label>
                        </div>
                    </div>
                </div>

                <button type="button" id="add-language"
                    class="mb-8 inline-flex items-center rounded-xl border border-sky-600 px-4 py-2 text-sm font-semibold text-sky-600 hover:bg-sky-50 transition">
                    + Add Another Language
                </button>

                <div class="flex justify-between items-center pt-4 border-t border-slate-200">
                    <a href="{{ route('employee.create.step8') }}"
                        class="text-sm font-medium text-slate-600 hover:text-slate-900">&larr; Back to Step 8</a>
                    <div class="flex items-center gap-3">
                        <button type="submit" name="skip" value="1"
                            class="inline-flex items-center justify-center rounded-2xl border border-slate-300 px-6 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2">
                            Skip</button>
                        <button type="submit"
                            class="inline-flex items-center justify-center rounded-2xl bg-sky-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2">
                            Next Step &rarr;</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        let languageIndex = 1;

        document.getElementById('add-language').addEventListener('click', function() {
            const container = document.getElementById('languages-container');
            const i = languageIndex;

            const html = `
                <div class="language-row border border-slate-200 rounded-2xl p-6 mb-6 bg-slate-50">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-semibold text-slate-800">Language #${i + 1}</h3>
                        <button type="button" class="remove-language text-red-600 text-sm font-medium">Remove</button>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <label class="font-semibold text-sm text-slate-700 block mb-1">Language Name <span class="text-rose-500 font-bold">*</span></label>
                            <input type="text" name="languages[${i}][language_name]" id="languages_${i}_language_name" placeholder="English"
                                class="w-full border border-slate-300 rounded-md p-2 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all placeholder:text-slate-400">
                        </div>
                        <div>
                            <label class="font-semibold text-sm text-slate-700 block mb-1">Proficiency</label>
                            <select name="languages[${i}][proficiency]" id="languages_${i}_proficiency"
                                class="w-full border border-slate-300 rounded-md p-2 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all cursor-pointer">
                                <option value="">-- Select Proficiency --</option>
                                <option value="Basic">Basic</option>
                                <option value="Conversational">Conversational</option>
                                <option value="Professional">Professional</option>
                                <option value="Native">Native</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex gap-6 mt-4">
                        <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700 cursor-pointer">
                            <input type="hidden" name="languages[${i}][can_read]" value="0">
                            <input type="checkbox" name="languages[${i}][can_read]" value="1"
                                class="rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                            Can Read
                        </label>
                        <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700 cursor-pointer">
                            <input type="hidden" name="languages[${i}][can_write]" value="0">
                            <input type="checkbox" name="languages[${i}][can_write]" value="1"
                                class="rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                            Can Write
                        </label>
                        <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700 cursor-pointer">
                            <input type="hidden" name="languages[${i}][can_speak]" value="0">
                            <input type="checkbox" name="languages[${i}][can_speak]" value="1"
                                class="rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                            Can Speak
                        </label>
                    </div>
                </div>
            `;

            container.insertAdjacentHTML('beforeend', html);
            languageIndex++;
        });

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-language')) {
                e.target.closest('.language-row').remove();
            }
        });
    </script>
</x-app-layout>