<div id="tab-personal" class="{{ $activeTab === 'personal' ? '' : 'hidden' }}">
    <form class="section-save-form" action="{{ route('employee.edit.personal', $employee->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center">
                    <i class="fa-solid fa-user text-emerald-600"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-900">Personal Information</h2>
                    <p class="text-xs text-slate-500">Personal & sensitive details</p>
                </div>
            </div>

            <div class="grid gap-6 sm:grid-cols-3">
                <x-form-input label="First Name" name="first_name" id="first_name" :value="old('first_name', $employee->personalInfo->first_name ?? '')" required />
                <x-form-input label="Last Name" name="last_name" id="last_name" :value="old('last_name', $employee->personalInfo->last_name ?? '')" required />
                <x-form-input label="Full Name" name="full_name" id="full_name" :value="old('full_name', $employee->personalInfo->full_name ?? '')" required />
            </div>

            <div class="grid gap-6 sm:grid-cols-2 mt-6">
                <x-form-input label="Phone" name="phone" :value="old('phone', $employee->personalInfo->phone ?? '')" />
                <x-form-input label="Phone 2" name="phone_2" :value="old('phone_2', $employee->personalInfo->phone_2 ?? '')" />
            </div>

            <div class="grid gap-6 sm:grid-cols-2 mt-6">
                <x-form-input label="Email" name="email" type="email" :value="old('email', $employee->personalInfo->email ?? '')" />
                <x-form-input label="Date of Birth" name="date_of_birth" type="date" :value="old('date_of_birth', $employee->personalInfo->date_of_birth ?? '')" />
            </div>

            <div class="grid gap-6 sm:grid-cols-2 mt-6">
                <x-form-select label="Gender" name="gender" placeholder="-- Select --">
                    @foreach (['Male', 'Female', 'Other', 'Prefer not to say'] as $g)
                        <option value="{{ $g }}" {{ old('gender', $employee->personalInfo->gender ?? '') === $g ? 'selected' : '' }}>{{ $g }}</option>
                    @endforeach
                </x-form-select>
                <x-form-input label="Nationality" name="nationality" :value="old('nationality', $employee->personalInfo->nationality ?? 'Bangladeshi')" />
            </div>

            <div class="grid gap-6 sm:grid-cols-2 mt-6">
                <x-form-select label="Marital Status" name="marital_status" placeholder="-- Select --">
                    @foreach (['Single', 'Married', 'Divorced', 'Widowed', 'Separated'] as $s)
                        <option value="{{ $s }}" {{ old('marital_status', $employee->personalInfo->marital_status ?? '') === $s ? 'selected' : '' }}>{{ $s }}</option>
                    @endforeach
                </x-form-select>
                <x-form-select label="Blood Group" name="blood_group" placeholder="-- Select --">
                    @foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $b)
                        <option value="{{ $b }}" {{ old('blood_group', $employee->personalInfo->blood_group ?? '') === $b ? 'selected' : '' }}>{{ $b }}</option>
                    @endforeach
                </x-form-select>
            </div>

            <div class="grid gap-6 sm:grid-cols-3 mt-6">
                <x-form-input label="Father Name" name="father_name" :value="old('father_name', $employee->personalInfo->father_name ?? '')" />
                <x-form-input label="Mother Name" name="mother_name" :value="old('mother_name', $employee->personalInfo->mother_name ?? '')" />
                <x-form-input label="Spouse Name" name="spouse_name" :value="old('spouse_name', $employee->personalInfo->spouse_name ?? '')" />
            </div>

            <div class="grid gap-6 sm:grid-cols-2 mt-6">
                <x-form-input label="Personal Email" name="personal_email" type="email" :value="old('personal_email', $employee->personalInfo->personal_email ?? '')" />
                <x-form-input label="Personal Mobile" name="personal_mobile" :value="old('personal_mobile', $employee->personalInfo->personal_mobile ?? '')" />
            </div>

            <div class="grid gap-6 sm:grid-cols-2 mt-6">
                <x-form-input label="Religion" name="religion" :value="old('religion', $employee->personalInfo->religion ?? '')" />
            </div>

            <div class="grid gap-6 sm:grid-cols-2 mt-6">
                <div>
                    <label class="font-semibold text-sm text-slate-700 block mb-1.5">Profile Photo</label>
                    <input type="file" name="profile_photo" accept="image/*"
                        class="block w-full text-sm border border-slate-300 rounded-xl p-2">
                    @if (!empty($employee->personalInfo->profile_photo))
                        <img src="{{ asset('storage/' . $employee->personalInfo->profile_photo) }}"
                            class="mt-3 h-20 w-20 rounded-full object-cover border" alt="Profile">
                    @endif
                </div>
                <div>
                    <label class="font-semibold text-sm text-slate-700 block mb-1.5">Signature</label>
                    <input type="file" name="signature_file" accept="image/*"
                        class="block w-full text-sm border border-slate-300 rounded-xl p-2">
                    @if (!empty($employee->personalInfo->signature_file))
                        <img src="{{ asset('storage/' . $employee->personalInfo->signature_file) }}"
                            class="mt-3 h-16 w-32 object-contain border rounded-lg" alt="Signature">
                    @endif
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-8 py-3 text-sm font-bold text-white hover:from-emerald-700 hover:to-teal-700 transition-all shadow-lg">
                    <i class="fa-solid fa-floppy-disk"></i> Save Personal Info
                </button>
            </div>
        </div>
    </form>
</div>