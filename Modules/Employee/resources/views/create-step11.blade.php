<x-app-layout>
    @include('employee::components.wizard-progress', ['current' => 11])

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white border border-slate-200 shadow-sm rounded-3xl p-8">
            <div class="mb-8 flex items-start justify-between">
                <div>
                    <h1 class="text-3xl font-semibold text-slate-900">Employee Registration</h1>
                    <p class="mt-2 text-sm text-slate-600">Step 11: Add dependents and nominees. Final step before completion.</p>
                </div>
                <form method="POST" action="{{ route('employee.reset.step', 11) }}">
                    @csrf
                    <button type="submit" onclick="return confirm('Reset Step 11 data? This will clear all entered information for this step.')"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-amber-300 px-4 py-2 text-sm font-semibold text-amber-700 transition hover:bg-amber-50 focus:outline-none focus:ring-2 focus:ring-amber-400">
                        ⟳ Refresh
                    </button>
                </form>
            </div>

            @if (session('error'))
                <div class="rounded-2xl bg-rose-50 border border-rose-200 p-4 text-rose-700 mb-6">{{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('employee.create.finalize') }}" class="space-y-6">
                @csrf

                <div class="grid gap-6 sm:grid-cols-2">
                    <x-form-input label="Full Name" name="full_name" id="full_name"
                        value="{{ old('full_name', $data['full_name'] ?? '') }}" />
                    <x-form-select2 label="Relation" name="relation" id="relation" placeholder="-- Select Relation --">
                        @foreach (['Spouse', 'Son', 'Daughter', 'Father', 'Mother', 'Brother', 'Sister', 'Other'] as $rel)
                            <option value="{{ $rel }}"
                                {{ old('relation', $data['relation'] ?? '') === $rel ? 'selected' : '' }}>
                                {{ $rel }}</option>
                        @endforeach
                    </x-form-select2>
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <x-form-input label="Date of Birth" name="date_of_birth" id="date_of_birth" type="date"
                        value="{{ old('date_of_birth', $data['date_of_birth'] ?? '') }}" />
                    <x-form-input label="NID Number" name="nid_number" id="nid_number"
                        value="{{ old('nid_number', $data['nid_number'] ?? '') }}" />
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <x-form-input label="Phone" name="phone" id="phone"
                        value="{{ old('phone', $data['phone'] ?? '') }}" />
                    <x-form-input label="Email" name="email" id="email" type="email"
                        value="{{ old('email', $data['email'] ?? '') }}" />
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <x-form-input label="Occupation" name="occupation" id="occupation"
                        value="{{ old('occupation', $data['occupation'] ?? '') }}" />
                </div>

                <div class="grid gap-6 sm:grid-cols-3">
                    <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700 cursor-pointer">
                        <input type="checkbox" name="is_nominee" value="1"
                            {{ old('is_nominee', $data['is_nominee'] ?? false) ? 'checked' : '' }}
                            class="rounded border-slate-300 text-sky-600 focus:ring-bg-blue-900">
                        Is Nominee
                    </label>
                    <x-form-input label="Nominee Percent (%)" name="nominee_percent" id="nominee_percent" type="number" step="0.01" min="0" max="100"
                        value="{{ old('nominee_percent', $data['nominee_percent'] ?? '') }}" />
                    <x-form-input label="Priority Order" name="priority_order" id="priority_order" type="number" min="0" max="255"
                        value="{{ old('priority_order', $data['priority_order'] ?? '') }}" />
                </div>

                <div class="rounded-3xl bg-slate-50 border border-slate-200 p-6">
                    <h2 class="text-xl font-semibold text-slate-900 mb-2">Review All Entered Information</h2>
                    <p class="text-sm text-slate-600 mb-6">Please review all details below before completing the registration.</p>

                    @php $wizard = session('employee_creation', []); @endphp

                    @if (!empty($wizard['step1']))
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-sky-700 border-b border-sky-200 pb-2 mb-3">Step 1 - Core Info</h3>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            @foreach ($wizard['step1'] as $key => $value)
                                @if (!empty($value) && !is_array($value))
                                <div class="bg-white rounded-xl p-3 border border-slate-200">
                                    <span class="text-xs font-medium text-slate-500 uppercase tracking-wide">{{ str_replace('_', ' ', $key) }}</span>
                                    <p class="text-sm font-semibold text-slate-800 mt-1">{{ $value }}</p>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if (!empty($wizard['step2']))
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-sky-700 border-b border-sky-200 pb-2 mb-3">Step 2 - Personal Info</h3>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            @foreach ($wizard['step2'] as $key => $value)
                                @if (!empty($value) && !is_array($value) && !in_array($key, ['profile_photo', 'signature_file']))
                                <div class="bg-white rounded-xl p-3 border border-slate-200">
                                    <span class="text-xs font-medium text-slate-500 uppercase tracking-wide">{{ str_replace('_', ' ', $key) }}</span>
                                    <p class="text-sm font-semibold text-slate-800 mt-1">{{ $value }}</p>
                                </div>
                                @endif
                            @endforeach
                            @if (!empty($wizard['step2']['profile_photo']))
                            <div class="bg-white rounded-xl p-3 border border-slate-200">
                                <span class="text-xs font-medium text-slate-500 uppercase tracking-wide">Profile Photo</span>
                                <p class="text-xs text-emerald-600 mt-1">✓ Uploaded</p>
                            </div>
                            @endif
                            @if (!empty($wizard['step2']['signature_file']))
                            <div class="bg-white rounded-xl p-3 border border-slate-200">
                                <span class="text-xs font-medium text-slate-500 uppercase tracking-wide">Signature</span>
                                <p class="text-xs text-emerald-600 mt-1">✓ Uploaded</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    @if (!empty($wizard['step3']['addresses']))
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-sky-700 border-b border-sky-200 pb-2 mb-3">Step 3 - Address Info</h3>
                        @foreach ($wizard['step3']['addresses'] as $addr)
                            <div class="bg-white rounded-xl p-4 border border-slate-200 mb-3">
                                <span class="text-xs font-semibold uppercase text-purple-600">{{ $addr['address_type'] ?? 'Address' }}</span>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-2 mt-2">
                                    @foreach ($addr as $key => $value)
                                        @if (!empty($value) && !is_array($value))
                                        <div>
                                            <span class="text-xs text-slate-500">{{ str_replace('_', ' ', $key) }}</span>
                                            <p class="text-sm font-medium text-slate-800">{{ $value }}</p>
                                        </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @endif

                    @if (!empty($wizard['step4']))
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-sky-700 border-b border-sky-200 pb-2 mb-3">Step 4 - Banking Info</h3>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            @foreach ($wizard['step4'] as $key => $value)
                                @if (!empty($value) && !is_array($value))
                                <div class="bg-white rounded-xl p-3 border border-slate-200">
                                    <span class="text-xs font-medium text-slate-500 uppercase tracking-wide">{{ str_replace('_', ' ', $key) }}</span>
                                    <p class="text-sm font-semibold text-slate-800 mt-1">{{ $value }}</p>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if (!empty($wizard['step5']['documents']))
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-sky-700 border-b border-sky-200 pb-2 mb-3">Step 5 - Documents ({{ count($wizard['step5']['documents']) }})</h3>
                        @foreach ($wizard['step5']['documents'] as $doc)
                            <div class="bg-white rounded-xl p-3 border border-slate-200 mb-2">
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                    @foreach ($doc as $key => $value)
                                        @if (!empty($value) && !is_array($value))
                                        <div>
                                            <span class="text-xs text-slate-500">{{ str_replace('_', ' ', $key) }}</span>
                                            <p class="text-sm font-medium text-slate-800">{{ $key === 'file_path' ? '✓ File uploaded' : $value }}</p>
                                        </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @endif

                    @if (!empty($wizard['step6']))
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-sky-700 border-b border-sky-200 pb-2 mb-3">Step 6 - Education</h3>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            @foreach ($wizard['step6'] as $key => $value)
                                @if (!empty($value) && !is_array($value))
                                <div class="bg-white rounded-xl p-3 border border-slate-200">
                                    <span class="text-xs font-medium text-slate-500 uppercase tracking-wide">{{ str_replace('_', ' ', $key) }}</span>
                                    <p class="text-sm font-semibold text-slate-800 mt-1">{{ $value }}</p>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if (!empty($wizard['step7']))
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-sky-700 border-b border-sky-200 pb-2 mb-3">Step 7 - Experience</h3>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            @foreach ($wizard['step7'] as $key => $value)
                                @if (!empty($value) && !is_array($value))
                                <div class="bg-white rounded-xl p-3 border border-slate-200">
                                    <span class="text-xs font-medium text-slate-500 uppercase tracking-wide">{{ str_replace('_', ' ', $key) }}</span>
                                    <p class="text-sm font-semibold text-slate-800 mt-1">{{ $value }}</p>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if (!empty($wizard['step8']))
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-sky-700 border-b border-sky-200 pb-2 mb-3">Step 8 - Job History</h3>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            @foreach ($wizard['step8'] as $key => $value)
                                @if (!empty($value) && !is_array($value))
                                <div class="bg-white rounded-xl p-3 border border-slate-200">
                                    <span class="text-xs font-medium text-slate-500 uppercase tracking-wide">{{ str_replace('_', ' ', $key) }}</span>
                                    <p class="text-sm font-semibold text-slate-800 mt-1">{{ $value }}</p>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if (!empty($wizard['step9']))
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-sky-700 border-b border-sky-200 pb-2 mb-3">Step 9 - Languages</h3>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            @foreach ($wizard['step9'] as $key => $value)
                                @if (!empty($value) && !is_array($value))
                                <div class="bg-white rounded-xl p-3 border border-slate-200">
                                    <span class="text-xs font-medium text-slate-500 uppercase tracking-wide">{{ str_replace('_', ' ', $key) }}</span>
                                    <p class="text-sm font-semibold text-slate-800 mt-1">{{ $value }}</p>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if (!empty($wizard['step10']))
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-sky-700 border-b border-sky-200 pb-2 mb-3">Step 10 - Skills</h3>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            @foreach ($wizard['step10'] as $key => $value)
                                @if (!empty($value) && !is_array($value))
                                <div class="bg-white rounded-xl p-3 border border-slate-200">
                                    <span class="text-xs font-medium text-slate-500 uppercase tracking-wide">{{ str_replace('_', ' ', $key) }}</span>
                                    <p class="text-sm font-semibold text-slate-800 mt-1">{{ $value }}</p>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if (empty($wizard['step1']))
                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-amber-700">
                        <p class="font-semibold">No data has been entered yet. Please fill in the previous steps first.</p>
                    </div>
                    @endif

                    <details class="mt-4">
                        <summary class="text-sm text-slate-500 cursor-pointer hover:text-slate-700">View raw data (JSON)</summary>
                        <pre class="mt-2 max-h-60 overflow-auto rounded-2xl bg-white p-4 text-xs text-slate-700 border border-slate-200">{{ json_encode($wizard, JSON_PRETTY_PRINT) }}</pre>
                    </details>
                </div>

                <div class="flex justify-between items-center pt-4 border-t border-slate-200">
                    <a href="{{ route('employee.create.step10') }}"
                        class="text-sm font-medium text-slate-600 hover:text-slate-900">&larr; Back to Step 10</a>
                    <div class="flex items-center gap-3">
                        <button type="submit" name="skip" value="1"
                            class="inline-flex items-center justify-center rounded-2xl border border-slate-300 px-6 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2">
                            Skip</button>
                        <button type="submit"
                            class="inline-flex items-center justify-center rounded-2xl bg-emerald-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                            Complete Registration &rarr;</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>