<x-app-layout>
    @include('employee::components.wizard-progress', ['current' => 10])

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white border border-slate-200 shadow-sm rounded-3xl p-8">
            <div class="mb-8 flex items-start justify-between">
                <div>
                    <h1 class="text-3xl font-semibold text-slate-900">Employee Registration</h1>
                    <p class="mt-2 text-sm text-slate-600">Step 10: Add skills and competencies. This step can be skipped.</p>
                </div>
                <form method="POST" action="{{ route('employee.reset.step', 10) }}">
                    @csrf
                    <button type="submit" onclick="return confirm('Reset Step 10 data? This will clear all entered information for this step.')"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-amber-300 px-4 py-2 text-sm font-semibold text-amber-700 transition hover:bg-amber-50 focus:outline-none focus:ring-2 focus:ring-amber-400">
                        ⟳ Refresh
                    </button>
                </form>
            </div>

            <form method="POST" action="{{ route('employee.store.step10') }}" class="space-y-6">
                @csrf

                <div class="grid gap-6 sm:grid-cols-2">
                    <x-form-select2 label="Skill Category" name="category_id" id="category_id" placeholder="-- Select Category --">
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" data-description="{{ $cat->description }}"
                                {{ old('category_id', $data['category_id'] ?? '') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}</option>
                        @endforeach
                    </x-form-select2>
                    <x-form-input label="Skill Name" name="skill_name" id="skill_name"
                        value="{{ old('skill_name', $data['skill_name'] ?? '') }}" />
                </div>

                <x-form-textarea label="Description" name="description" id="description"
                    rows="3">{{ old('description', $data['description'] ?? '') }}</x-form-textarea>

                <div class="grid gap-6 sm:grid-cols-3">
                    <x-form-select label="Proficiency" name="proficiency" id="proficiency" placeholder="-- Select Level --">
                        @foreach (['Beginner', 'Intermediate', 'Advanced', 'Expert', 'Master'] as $level)
                            <option value="{{ $level }}"
                                {{ old('proficiency', $data['proficiency'] ?? '') === $level ? 'selected' : '' }}>
                                {{ $level }}</option>
                        @endforeach
                    </x-form-select>
                    <x-form-input label="Years of Experience" name="years_of_experience" id="years_of_experience" type="number" step="0.1"
                        value="{{ old('years_of_experience', $data['years_of_experience'] ?? '') }}" />
                    <x-form-input label="Last Used Date" name="last_used_date" id="last_used_date" type="date"
                        value="{{ old('last_used_date', $data['last_used_date'] ?? '') }}" />
                </div>

                <x-form-input label="Certification" name="certification" id="certification"
                    value="{{ old('certification', $data['certification'] ?? '') }}" />

                <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1"
                        {{ old('is_active', $data['is_active'] ?? true) ? 'checked' : '' }}
                        class="rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                    Active skill
                </label>

                <div class="flex justify-between items-center pt-4 border-t border-slate-200">
                    <a href="{{ route('employee.create.step9') }}"
                        class="text-sm font-medium text-slate-600 hover:text-slate-900">&larr; Back to Step 9</a>
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
        // Wait for everything including Select2 to be fully ready
        $(window).on('load', function() {
            const $cat = $('#category_id');
            const $desc = $('#description');

            if (!$cat.length) return;

            function fillDescription() {
                const nativeSelect = document.getElementById('category_id');
                if (!nativeSelect) return;
                const selected = nativeSelect.options[nativeSelect.selectedIndex];
                $desc.val(selected ? (selected.getAttribute('data-description') || '') : '');
            }

            // Bind to Select2's native change event (the original select still fires change)
            $cat.on('change', fillDescription);

            // Fill on page load if already selected
            fillDescription();
        });
    </script>

</x-app-layout>
