<x-app-layout>
    @include('employee::components.wizard-progress', ['current' => 4])

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white border border-slate-200 shadow-sm rounded-3xl p-8">
            <div class="mb-8 flex items-start justify-between">
                <div>
                    <h1 class="text-3xl font-semibold text-slate-900">Employee Registration</h1>
                    <p class="mt-2 text-sm text-slate-600">Step 4: Finalize payment details and complete the employee profile.</p>
                </div>
                <form method="POST" action="{{ route('employee.reset.step', 4) }}">
                    @csrf
                    <button type="submit" onclick="return confirm('Reset Step 4 data? This will clear all entered information for this step.')"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-amber-300 px-4 py-2 text-sm font-semibold text-amber-700 transition hover:bg-amber-50 focus:outline-none focus:ring-2 focus:ring-amber-400">
                        ⟳ Refresh
                    </button>
                </form>
            </div>

            <form method="POST" action="{{ route('employee.store.step4') }}" class="space-y-6">
                @csrf

                <div class="grid gap-6 sm:grid-cols-2">
                    <x-form-input label="Bank Name" name="bank_name" id="bank_name"
                        value="{{ old('bank_name', $data['bank_name'] ?? '') }}" />
                    <x-form-input label="Bank Branch" name="bank_branch" id="bank_branch"
                        value="{{ old('bank_branch', $data['bank_branch'] ?? '') }}" />
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <x-form-input label="Bank Account" name="bank_account" id="bank_account"
                        value="{{ old('bank_account', $data['bank_account'] ?? '') }}" />
                    <x-form-input label="Bank Routing" name="bank_routing" id="bank_routing"
                        value="{{ old('bank_routing', $data['bank_routing'] ?? '') }}" />
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <x-form-input label="IBAN" name="iban" id="iban"
                        value="{{ old('iban', $data['iban'] ?? '') }}" />
                    <x-form-input label="SWIFT Code" name="swift_code" id="swift_code"
                        value="{{ old('swift_code', $data['swift_code'] ?? '') }}" />
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <x-form-select label="MFS Type" name="mfs_type" id="mfs_type" placeholder="-- Select MFS --">
                        @foreach (['bKash', 'Nagad', 'Rocket', 'Upay', 'Others'] as $mfs)
                            <option value="{{ $mfs }}"
                                {{ old('mfs_type', $data['mfs_type'] ?? '') === $mfs ? 'selected' : '' }}>
                                {{ $mfs }}</option>
                        @endforeach
                    </x-form-select>
                    <x-form-input label="MFS Number" name="mfs_number" id="mfs_number"
                        value="{{ old('mfs_number', $data['mfs_number'] ?? '') }}" />
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <x-form-select label="Payment Method" name="payment_method" id="payment_method" placeholder="-- Select Payment Method --">
                        @foreach (['Bank', 'Cash', 'MFS', 'Cheque'] as $method)
                            <option value="{{ $method }}"
                                {{ old('payment_method', $data['payment_method'] ?? '') === $method ? 'selected' : '' }}>
                                {{ $method }}</option>
                        @endforeach
                    </x-form-select>
                    {{-- <x-form-select label="Verification Status" name="verification_status" id="verification_status" placeholder="-- Select Status --">
                        @foreach (['Pending', 'Verified', 'Rejected'] as $status)
                            <option value="{{ $status }}"
                                {{ old('verification_status', $data['verification_status'] ?? '') === $status ? 'selected' : '' }}>
                                {{ $status }}</option>
                        @endforeach
                    </x-form-select> --}}
                </div>

                {{-- <div class="grid gap-6 sm:grid-cols-2">
                    <x-form-input label="Verified At" name="verified_at" id="verified_at" type="date"
                        value="{{ old('verified_at', $data['verified_at'] ?? '') }}" />
                </div> --}}

                <div class="flex justify-between items-center pt-4 border-t border-slate-200">
                    <a href="{{ route('employee.create.step3') }}"
                        class="text-sm font-medium text-slate-600 hover:text-slate-900">&larr; Back to Step 3</a>

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
</x-app-layout>