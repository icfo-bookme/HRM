<x-app-layout>
    @include('employee::components.wizard-progress', ['current' => 6])

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white border border-slate-200 shadow-sm rounded-3xl p-8">

            <div class="mb-8 flex items-start justify-between">
                <div>
                    <h1 class="text-3xl font-semibold text-slate-900">Employee Registration</h1>
                    <p class="mt-2 text-sm text-slate-600">Step 6: Add educational qualifications. You can add multiple entries.</p>
                </div>
                <form method="POST" action="{{ route('employee.reset.step', 6) }}">
                    @csrf
                    <button type="submit" onclick="return confirm('Reset Step 6 data? This will clear all entered information for this step.')"
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

            <form method="POST" action="{{ route('employee.store.step6') }}" enctype="multipart/form-data">
                @csrf

                <div id="educations-container">
                    {{-- Default Education Row --}}
                    <div class="education-row border border-slate-200 rounded-2xl p-6 mb-6 bg-slate-50">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-semibold text-slate-800">Education #1</h3>
                            <button type="button" class="remove-education hidden text-red-600 text-sm font-medium">Remove</button>
                        </div>

                        <div class="grid gap-6 sm:grid-cols-2">
                            <x-form-input label="Degree" name="educations[0][degree]" id="educations_0_degree" placeholder="B.Sc. in CSE" />
                            <x-form-input label="Major Subject" name="educations[0][major_subject]" id="educations_0_major_subject" placeholder="Computer Science" />
                        </div>

                        <div class="grid gap-6 sm:grid-cols-2 mt-4">
                            <x-form-input label="Institution" name="educations[0][institution]" id="educations_0_institution" placeholder="University Name" />
                            <x-form-input label="Board / University" name="educations[0][board_university]" id="educations_0_board_university" />
                        </div>

                        <div class="grid gap-6 sm:grid-cols-3 mt-4">
                            <x-form-input label="Passing Year" name="educations[0][passing_year]" id="educations_0_passing_year" type="number" placeholder="2020" />
                            <div>
                                <label class="font-semibold text-sm text-slate-700 block mb-1">Result Type</label>
                                <select name="educations[0][result_type]" id="educations_0_result_type"
                                    class="w-full border border-slate-300 rounded-md p-2 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all cursor-pointer">
                                    <option value="">-- Select Result Type --</option>
                                    <option value="CGPA">CGPA</option>
                                    <option value="Percentage">Percentage</option>
                                    <option value="Grade">Grade</option>
                                    <option value="Division">Division</option>
                                </select>
                            </div>
                            <x-form-input label="Result Value" name="educations[0][result_value]" id="educations_0_result_value" placeholder="3.50" />
                        </div>

                        <div class="grid gap-6 sm:grid-cols-3 mt-4">
                            <x-form-input label="Duration From" name="educations[0][duration_from]" id="educations_0_duration_from" type="date" />
                            <x-form-input label="Duration To" name="educations[0][duration_to]" id="educations_0_duration_to" type="date" />
                            <x-form-input label="Country" name="educations[0][country]" id="educations_0_country" value="Bangladesh" />
                        </div>

                        <div class="mt-4">
                            <label class="font-semibold text-sm text-slate-700 block mb-1">Certificate Document</label>
                            <input type="file" name="educations[0][certificate_file]"
                                class="block w-full rounded-md border border-slate-300 bg-white p-2 text-sm text-slate-800">
                        </div>

                        <div class="mt-4">
                            <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700 cursor-pointer">
                                <input type="hidden" name="educations[0][is_highest]" value="0">
                                <input type="checkbox" name="educations[0][is_highest]" value="1"
                                    class="rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                                Highest qualification
                            </label>
                        </div>
                    </div>
                </div>

                <button type="button" id="add-education"
                    class="mb-8 inline-flex items-center rounded-xl border border-sky-600 px-4 py-2 text-sm font-semibold text-sky-600 hover:bg-sky-50 transition">
                    + Add Another Education
                </button>

                <div class="flex justify-between items-center pt-4 border-t border-slate-200">
                    <a href="{{ route('employee.create.step5') }}"
                        class="text-sm font-medium text-slate-600 hover:text-slate-900">&larr; Back to Step 5</a>
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
        let educationIndex = 1;

        document.getElementById('add-education').addEventListener('click', function() {
            const container = document.getElementById('educations-container');
            const i = educationIndex;

            const html = `
                <div class="education-row border border-slate-200 rounded-2xl p-6 mb-6 bg-slate-50">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-semibold text-slate-800">Education #${i + 1}</h3>
                        <button type="button" class="remove-education text-red-600 text-sm font-medium">Remove</button>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <label class="font-semibold text-sm text-slate-700 block mb-1">Degree <span class="text-rose-500 font-bold">*</span></label>
                            <input type="text" name="educations[${i}][degree]" id="educations_${i}_degree" placeholder="B.Sc. in CSE"
                                class="w-full border border-slate-300 rounded-md p-2 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all placeholder:text-slate-400">
                        </div>
                        <div>
                            <label class="font-semibold text-sm text-slate-700 block mb-1">Major Subject</label>
                            <input type="text" name="educations[${i}][major_subject]" id="educations_${i}_major_subject" placeholder="Computer Science"
                                class="w-full border border-slate-300 rounded-md p-2 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all placeholder:text-slate-400">
                        </div>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2 mt-4">
                        <div>
                            <label class="font-semibold text-sm text-slate-700 block mb-1">Institution</label>
                            <input type="text" name="educations[${i}][institution]" id="educations_${i}_institution" placeholder="University Name"
                                class="w-full border border-slate-300 rounded-md p-2 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all placeholder:text-slate-400">
                        </div>
                        <div>
                            <label class="font-semibold text-sm text-slate-700 block mb-1">Board / University</label>
                            <input type="text" name="educations[${i}][board_university]" id="educations_${i}_board_university"
                                class="w-full border border-slate-300 rounded-md p-2 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all placeholder:text-slate-400">
                        </div>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-3 mt-4">
                        <div>
                            <label class="font-semibold text-sm text-slate-700 block mb-1">Passing Year</label>
                            <input type="number" name="educations[${i}][passing_year]" id="educations_${i}_passing_year" placeholder="2020"
                                class="w-full border border-slate-300 rounded-md p-2 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all placeholder:text-slate-400">
                        </div>
                        <div>
                            <label class="font-semibold text-sm text-slate-700 block mb-1">Result Type</label>
                            <select name="educations[${i}][result_type]" id="educations_${i}_result_type"
                                class="w-full border border-slate-300 rounded-md p-2 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all cursor-pointer">
                                <option value="">-- Select Result Type --</option>
                                <option value="CGPA">CGPA</option>
                                <option value="Percentage">Percentage</option>
                                <option value="Grade">Grade</option>
                                <option value="Division">Division</option>
                            </select>
                        </div>
                        <div>
                            <label class="font-semibold text-sm text-slate-700 block mb-1">Result Value</label>
                            <input type="text" name="educations[${i}][result_value]" id="educations_${i}_result_value" placeholder="3.50"
                                class="w-full border border-slate-300 rounded-md p-2 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all placeholder:text-slate-400">
                        </div>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-3 mt-4">
                        <div>
                            <label class="font-semibold text-sm text-slate-700 block mb-1">Duration From</label>
                            <input type="date" name="educations[${i}][duration_from]" id="educations_${i}_duration_from"
                                class="w-full border border-slate-300 rounded-md p-2 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all">
                        </div>
                        <div>
                            <label class="font-semibold text-sm text-slate-700 block mb-1">Duration To</label>
                            <input type="date" name="educations[${i}][duration_to]" id="educations_${i}_duration_to"
                                class="w-full border border-slate-300 rounded-md p-2 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all">
                        </div>
                        <div>
                            <label class="font-semibold text-sm text-slate-700 block mb-1">Country</label>
                            <input type="text" name="educations[${i}][country]" id="educations_${i}_country" value="Bangladesh"
                                class="w-full border border-slate-300 rounded-md p-2 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all placeholder:text-slate-400">
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="font-semibold text-sm text-slate-700 block mb-1">Certificate Document</label>
                        <input type="file" name="educations[${i}][certificate_file]"
                            class="block w-full rounded-md border border-slate-300 bg-white p-2 text-sm text-slate-800">
                    </div>

                    <div class="mt-4">
                        <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700 cursor-pointer">
                            <input type="hidden" name="educations[${i}][is_highest]" value="0">
                            <input type="checkbox" name="educations[${i}][is_highest]" value="1"
                                class="rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                            Highest qualification
                        </label>
                    </div>
                </div>
            `;

            container.insertAdjacentHTML('beforeend', html);
            educationIndex++;
        });

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-education')) {
                e.target.closest('.education-row').remove();
            }
        });
    </script>
</x-app-layout>