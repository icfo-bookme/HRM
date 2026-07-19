<x-app-layout>
    @include('employee::components.wizard-progress', ['current' => 7])

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white border border-slate-200 shadow-sm rounded-3xl p-8">

            <div class="mb-8 flex items-start justify-between">
                <div>
                    <h1 class="text-3xl font-semibold text-slate-900">Employee Registration</h1>
                    <p class="mt-2 text-sm text-slate-600">Step 7: Add previous work experience. You can add multiple entries.</p>
                </div>
                <form method="POST" action="{{ route('employee.reset.step', 7) }}">
                    @csrf
                    <button type="submit" onclick="return confirm('Reset Step 7 data? This will clear all entered information for this step.')"
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

            <form method="POST" action="{{ route('employee.store.step7') }}" enctype="multipart/form-data">
                @csrf

                <div id="experiences-container">
                    {{-- Default Experience Row --}}
                    <div class="experience-row border border-slate-200 rounded-2xl p-6 mb-6 bg-slate-50">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-semibold text-slate-800">Experience #1</h3>
                            <button type="button" class="remove-experience hidden text-red-600 text-sm font-medium">Remove</button>
                        </div>

                        <div class="grid gap-6 sm:grid-cols-3">
                            <x-form-input label="Company Name" name="experiences[0][company_name]" id="experiences_0_company_name" placeholder="Company Ltd." />
                            <x-form-input label="Designation" name="experiences[0][designation]" id="experiences_0_designation" placeholder="Software Engineer" />
                            <x-form-input label="Department" name="experiences[0][department]" id="experiences_0_department" placeholder="IT Department" />
                        </div>

                        <div class="grid gap-6 sm:grid-cols-3 mt-4">
                            <x-form-input label="From Date" name="experiences[0][from_date]" id="experiences_0_from_date" type="date" />
                            <x-form-input label="To Date" name="experiences[0][to_date]" id="experiences_0_to_date" type="date" />
                            <x-form-input label="Salary Scale" name="experiences[0][salary_scale]" id="experiences_0_salary_scale" placeholder="50,000 - 80,000" />
                        </div>

                        <div class="mt-4">
                            <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700 cursor-pointer">
                                <input type="hidden" name="experiences[0][is_current]" value="0">
                                <input type="checkbox" name="experiences[0][is_current]" value="1"
                                    class="rounded border-slate-300 text-sky-600 focus:ring-bg-blue-900">
                                Current job
                            </label>
                        </div>

                        <div class="grid gap-6 sm:grid-cols-2 mt-4">
                            <div>
                                <label class="font-semibold text-sm text-slate-700 block mb-1">Responsibilities</label>
                                <textarea name="experiences[0][responsibilities]" id="experiences_0_responsibilities" rows="4"
                                    class="w-full border border-slate-300 rounded-md p-2 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all placeholder:text-slate-400"></textarea>
                            </div>
                            <div>
                                <label class="font-semibold text-sm text-slate-700 block mb-1">Achievements</label>
                                <textarea name="experiences[0][achievements]" id="experiences_0_achievements" rows="4"
                                    class="w-full border border-slate-300 rounded-md p-2 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all placeholder:text-slate-400"></textarea>
                            </div>
                        </div>

                        <div class="grid gap-6 sm:grid-cols-3 mt-4">
                            <x-form-input label="Reference Name" name="experiences[0][reference_name]" id="experiences_0_reference_name" />
                            <x-form-input label="Reference Phone" name="experiences[0][reference_phone]" id="experiences_0_reference_phone" />
                            <x-form-input label="Reference Email" name="experiences[0][reference_email]" id="experiences_0_reference_email" type="email" />
                        </div>

                        <div class="grid gap-6 sm:grid-cols-2 mt-4">
                            <x-form-input label="Reason For Leaving" name="experiences[0][reason_for_leaving]" id="experiences_0_reason_for_leaving" />
                            <div>
                                <label class="font-semibold text-sm text-slate-700 block mb-1">Certificate Document</label>
                                <input type="file" name="experiences[0][certificate_file]"
                                    class="block w-full rounded-md border border-slate-300 bg-white p-2 text-sm text-slate-800">
                            </div>
                        </div>
                    </div>
                </div>

                <button type="button" id="add-experience"
                    class="mb-8 inline-flex items-center rounded-xl border border-sky-600 px-4 py-2 text-sm font-semibold text-sky-600 hover:bg-sky-50 transition">
                    + Add Another Experience
                </button>

                <div class="flex justify-between items-center pt-4 border-t border-slate-200">
                    <a href="{{ route('employee.create.step6') }}"
                        class="text-sm font-medium text-slate-600 hover:text-slate-900">&larr; Back to Step 6</a>
                    <div class="flex items-center gap-3">
                        <button type="submit" name="skip" value="1"
                            class="inline-flex items-center justify-center rounded-2xl border border-slate-300 px-6 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2">
                            Skip</button>
                        <button type="submit"
                            class="inline-flex items-center justify-center rounded-2xl bg-blue-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-bg-blue-900 focus:ring-offset-2">
                            Next Step &rarr;</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        let experienceIndex = 1;

        document.getElementById('add-experience').addEventListener('click', function() {
            const container = document.getElementById('experiences-container');
            const i = experienceIndex;

            const html = `
                <div class="experience-row border border-slate-200 rounded-2xl p-6 mb-6 bg-slate-50">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-semibold text-slate-800">Experience #${i + 1}</h3>
                        <button type="button" class="remove-experience text-red-600 text-sm font-medium">Remove</button>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-3">
                        <div>
                            <label class="font-semibold text-sm text-slate-700 block mb-1">Company Name <span class="text-rose-500 font-bold">*</span></label>
                            <input type="text" name="experiences[${i}][company_name]" id="experiences_${i}_company_name" placeholder="Company Ltd."
                                class="w-full border border-slate-300 rounded-md p-2 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all placeholder:text-slate-400">
                        </div>
                        <div>
                            <label class="font-semibold text-sm text-slate-700 block mb-1">Designation</label>
                            <input type="text" name="experiences[${i}][designation]" id="experiences_${i}_designation" placeholder="Software Engineer"
                                class="w-full border border-slate-300 rounded-md p-2 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all placeholder:text-slate-400">
                        </div>
                        <div>
                            <label class="font-semibold text-sm text-slate-700 block mb-1">Department</label>
                            <input type="text" name="experiences[${i}][department]" id="experiences_${i}_department" placeholder="IT Department"
                                class="w-full border border-slate-300 rounded-md p-2 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all placeholder:text-slate-400">
                        </div>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-3 mt-4">
                        <div>
                            <label class="font-semibold text-sm text-slate-700 block mb-1">From Date</label>
                            <input type="date" name="experiences[${i}][from_date]" id="experiences_${i}_from_date"
                                class="w-full border border-slate-300 rounded-md p-2 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all">
                        </div>
                        <div>
                            <label class="font-semibold text-sm text-slate-700 block mb-1">To Date</label>
                            <input type="date" name="experiences[${i}][to_date]" id="experiences_${i}_to_date"
                                class="w-full border border-slate-300 rounded-md p-2 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all">
                        </div>
                        <div>
                            <label class="font-semibold text-sm text-slate-700 block mb-1">Salary Scale</label>
                            <input type="text" name="experiences[${i}][salary_scale]" id="experiences_${i}_salary_scale" placeholder="50,000 - 80,000"
                                class="w-full border border-slate-300 rounded-md p-2 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all placeholder:text-slate-400">
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700 cursor-pointer">
                            <input type="hidden" name="experiences[${i}][is_current]" value="0">
                            <input type="checkbox" name="experiences[${i}][is_current]" value="1"
                                class="rounded border-slate-300 text-sky-600 focus:ring-bg-blue-900">
                            Current job
                        </label>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2 mt-4">
                        <div>
                            <label class="font-semibold text-sm text-slate-700 block mb-1">Responsibilities</label>
                            <textarea name="experiences[${i}][responsibilities]" id="experiences_${i}_responsibilities" rows="4"
                                class="w-full border border-slate-300 rounded-md p-2 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all placeholder:text-slate-400"></textarea>
                        </div>
                        <div>
                            <label class="font-semibold text-sm text-slate-700 block mb-1">Achievements</label>
                            <textarea name="experiences[${i}][achievements]" id="experiences_${i}_achievements" rows="4"
                                class="w-full border border-slate-300 rounded-md p-2 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all placeholder:text-slate-400"></textarea>
                        </div>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-3 mt-4">
                        <div>
                            <label class="font-semibold text-sm text-slate-700 block mb-1">Reference Name</label>
                            <input type="text" name="experiences[${i}][reference_name]" id="experiences_${i}_reference_name"
                                class="w-full border border-slate-300 rounded-md p-2 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all placeholder:text-slate-400">
                        </div>
                        <div>
                            <label class="font-semibold text-sm text-slate-700 block mb-1">Reference Phone</label>
                            <input type="text" name="experiences[${i}][reference_phone]" id="experiences_${i}_reference_phone"
                                class="w-full border border-slate-300 rounded-md p-2 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all placeholder:text-slate-400">
                        </div>
                        <div>
                            <label class="font-semibold text-sm text-slate-700 block mb-1">Reference Email</label>
                            <input type="email" name="experiences[${i}][reference_email]" id="experiences_${i}_reference_email"
                                class="w-full border border-slate-300 rounded-md p-2 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all placeholder:text-slate-400">
                        </div>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2 mt-4">
                        <div>
                            <label class="font-semibold text-sm text-slate-700 block mb-1">Reason For Leaving</label>
                            <input type="text" name="experiences[${i}][reason_for_leaving]" id="experiences_${i}_reason_for_leaving"
                                class="w-full border border-slate-300 rounded-md p-2 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all placeholder:text-slate-400">
                        </div>
                        <div>
                            <label class="font-semibold text-sm text-slate-700 block mb-1">Certificate Document</label>
                            <input type="file" name="experiences[${i}][certificate_file]"
                                class="block w-full rounded-md border border-slate-300 bg-white p-2 text-sm text-slate-800">
                        </div>
                    </div>
                </div>
            `;

            container.insertAdjacentHTML('beforeend', html);
            experienceIndex++;
        });

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-experience')) {
                e.target.closest('.experience-row').remove();
            }
        });
    </script>
</x-app-layout>