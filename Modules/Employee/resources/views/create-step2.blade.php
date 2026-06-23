<x-app-layout>
    @include('employee::components.wizard-progress', ['current' => 2])

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white border border-slate-200 shadow-sm rounded-3xl p-8">
            <div class="mb-8">
                <h1 class="text-3xl font-semibold text-slate-900">Employee Registration</h1>
                <p class="mt-2 text-sm text-slate-600">Step 2: Personal details for profile completeness and compliance.</p>
            </div>

            <form method="POST" action="{{ route('employee.store.step2') }}" class="space-y-6" enctype="multipart/form-data">
                @csrf

                {{-- Name Section (required) --}}
                <div class="grid gap-6 sm:grid-cols-3">
                    <div>
                        <x-form-input label="First Name" name="first_name" id="first_name"
                            value="{{ old('first_name', $data['first_name'] ?? '') }}" required />
                        @error('first_name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <x-form-input label="Last Name" name="last_name" id="last_name"
                            value="{{ old('last_name', $data['last_name'] ?? '') }}" required />
                        @error('last_name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <x-form-input label="Full Name" name="full_name" id="full_name"
                            value="{{ old('full_name', $data['full_name'] ?? '') }}" required />
                        @error('full_name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Contact Info (required fields) --}}
                <div class="grid gap-6 sm:grid-cols-2">
                    <div>
                        <x-form-input label="Phone" name="phone" id="phone"
                            value="{{ old('phone', $data['phone'] ?? '') }}" required />
                        @error('phone')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <x-form-input label="Phone 2 (Alternative)" name="phone_2" id="phone_2"
                            value="{{ old('phone_2', $data['phone_2'] ?? '') }}" />
                        @error('phone_2')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <div>
                        <x-form-input label="Email" name="email" id="email" type="email"
                            value="{{ old('email', $data['email'] ?? '') }}" />
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <x-form-input label="Date of Birth" name="date_of_birth" id="date_of_birth" type="date"
                            value="{{ old('date_of_birth', $data['date_of_birth'] ?? '') }}" required />
                        @error('date_of_birth')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <div>
                        <x-form-select label="Gender" name="gender" id="gender" placeholder="-- Select Gender --" required>
                            @foreach (['Male', 'Female', 'Other'] as $gender)
                                <option value="{{ $gender }}"
                                    {{ old('gender', $data['gender'] ?? '') === $gender ? 'selected' : '' }}>
                                    {{ $gender }}</option>
                            @endforeach
                        </x-form-select>
                        @error('gender')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <x-form-input label="Nationality" name="nationality" id="nationality"
                        value="{{ old('nationality', $data['nationality'] ?? 'Bangladeshi') }}" />
                </div>

                {{-- Status --}}
                <div class="grid gap-6 sm:grid-cols-2">
                    <x-form-select label="Marital Status" name="marital_status" id="marital_status" placeholder="-- Select Marital Status --">
                        @foreach (['Single', 'Married', 'Divorced', 'Widowed', 'Separated'] as $status)
                            <option value="{{ $status }}"
                                {{ old('marital_status', $data['marital_status'] ?? '') === $status ? 'selected' : '' }}>
                                {{ $status }}</option>
                        @endforeach
                    </x-form-select>

                    <x-form-select label="Blood Group" name="blood_group" id="blood_group" placeholder="-- Select Blood Group --">
                        @foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $blood)
                            <option value="{{ $blood }}"
                                {{ old('blood_group', $data['blood_group'] ?? '') === $blood ? 'selected' : '' }}>
                                {{ $blood }}</option>
                        @endforeach
                    </x-form-select>
                </div>

                {{-- Family Info --}}
                <div class="grid gap-6 sm:grid-cols-3">
                    <x-form-input label="Father Name" name="father_name" id="father_name"
                        value="{{ old('father_name', $data['father_name'] ?? '') }}" />
                    <x-form-input label="Mother Name" name="mother_name" id="mother_name"
                        value="{{ old('mother_name', $data['mother_name'] ?? '') }}" />
                    <x-form-input label="Spouse Name" name="spouse_name" id="spouse_name"
                        value="{{ old('spouse_name', $data['spouse_name'] ?? '') }}" />
                </div>

                {{-- Contact --}}
                <div class="grid gap-6 sm:grid-cols-2">
                    <x-form-input label="Personal Email" name="personal_email" id="personal_email" type="email"
                        value="{{ old('personal_email', $data['personal_email'] ?? '') }}" />
                    <x-form-input label="Personal Mobile" name="personal_mobile" id="personal_mobile"
                        value="{{ old('personal_mobile', $data['personal_mobile'] ?? '') }}" />
                </div>

                {{-- Extra --}}
                <div class="grid gap-6 sm:grid-cols-2">
                    <x-form-input label="Religion" name="religion" id="religion"
                        value="{{ old('religion', $data['religion'] ?? '') }}" />
                </div>

                {{-- File Uploads --}}
                <div class="grid gap-6 sm:grid-cols-2">
                    {{-- Profile Photo --}}
                    <div>
                        <label class="font-semibold text-sm text-slate-700 block mb-1">Profile Photo</label>
                        <input type="file" name="profile_photo" id="profile_photo" accept="image/*"
                            class="block w-full text-sm border border-slate-300 rounded-xl p-2 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100">
                        @if (!empty($data['profile_photo']))
                            <img id="profile_preview" class="mt-3 h-24 w-24 rounded-full object-cover border" alt="Profile Preview"
                                src="{{ asset('storage/' . $data['profile_photo']) }}">
                            <p class="text-xs text-slate-500 mt-1">Current file: {{ basename($data['profile_photo']) }}</p>
                        @else
                            <img id="profile_preview" class="mt-3 h-24 w-24 rounded-full object-cover border hidden" alt="Profile Preview">
                        @endif
                        @error('profile_photo')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Signature --}}
                    <div>
                        <label class="font-semibold text-sm text-slate-700 block mb-1">Signature File</label>
                        <input type="file" name="signature_file" id="signature_file" accept="image/*"
                            class="block w-full text-sm border border-slate-300 rounded-xl p-2 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100">
                        @if (!empty($data['signature_file']))
                            <img id="signature_preview" class="mt-3 h-24 w-40 object-contain border rounded-lg" alt="Signature Preview"
                                src="{{ asset('storage/' . $data['signature_file']) }}">
                            <p class="text-xs text-slate-500 mt-1">Current file: {{ basename($data['signature_file']) }}</p>
                        @else
                            <img id="signature_preview" class="mt-3 h-24 w-40 object-contain border rounded-lg hidden" alt="Signature Preview">
                        @endif
                        @error('signature_file')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex justify-between items-center pt-4 border-t border-slate-200">
                    <a href="{{ route('employee.create.step1') }}"
                        class="text-sm font-medium text-slate-600 hover:text-slate-900">&larr; Back to Step 1</a>

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
        function previewImage(input, previewId) {
            const file = input.files[0];
            const preview = document.getElementById(previewId);
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        }
        document.getElementById('profile_photo').addEventListener('change', function() { previewImage(this, 'profile_preview'); });
        document.getElementById('signature_file').addEventListener('change', function() { previewImage(this, 'signature_preview'); });

        // Auto-fill full_name from first_name + last_name — but only if user hasn't manually edited full_name
        let fullNameManuallyEdited = false;
        const firstNameInput = document.getElementById('first_name');
        const lastNameInput = document.getElementById('last_name');
        const fullNameInput = document.getElementById('full_name');

        fullNameInput.addEventListener('input', function() {
            fullNameManuallyEdited = true;
        });

        function autoFillFullName() {
            if (!fullNameManuallyEdited) {
                const first = firstNameInput.value.trim();
                const last = lastNameInput.value.trim();
                fullNameInput.value = [first, last].filter(Boolean).join(' ');
            }
        }

        firstNameInput.addEventListener('input', autoFillFullName);
        lastNameInput.addEventListener('input', autoFillFullName);
    </script>
</x-app-layout>