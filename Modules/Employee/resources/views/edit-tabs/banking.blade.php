<div id="tab-banking" class="{{ $activeTab === 'banking' ? '' : 'hidden' }}">
    <form class="section-save-form" action="{{ route('employee.edit.banking', $employee->id) }}">
        @csrf
        @method('PUT')
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-rose-100 flex items-center justify-center">
                    <i class="fa-solid fa-building-columns text-rose-600"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-900">Banking Information</h2>
                    <p class="text-xs text-slate-500">Payment setup</p>
                </div>
            </div>

            @php $banking = $employee->banking->first(); @endphp

            <div class="grid gap-6 sm:grid-cols-2">
                <x-form-select label="Payment Method" name="payment_method" placeholder="-- Select --">
                    @foreach (['Bank Transfer', 'MFS (Mobile Financial Service)', 'Cheque', 'Cash'] as $pm)
                        <option value="{{ $pm }}" {{ old('payment_method', $banking->payment_method ?? '') === $pm ? 'selected' : '' }}>{{ $pm }}</option>
                    @endforeach
                </x-form-select>
                <x-form-select label="Is Primary" name="is_primary" placeholder="">
                    <option value="1" {{ old('is_primary', $banking->is_primary ?? 1) == 1 ? 'selected' : '' }}>Yes</option>
                    <option value="0" {{ old('is_primary', $banking->is_primary ?? 1) == 0 ? 'selected' : '' }}>No</option>
                </x-form-select>
            </div>

            <hr class="my-6 border-slate-200">

            <h4 class="font-bold text-slate-700 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-building-columns text-sky-600"></i> Bank Account Details
            </h4>

            <div class="grid gap-6 sm:grid-cols-2">
                <x-form-input label="Bank Name" name="bank_name" :value="old('bank_name', $banking->bank_name ?? '')" />
                <x-form-input label="Branch" name="bank_branch" :value="old('bank_branch', $banking->bank_branch ?? '')" />
                <x-form-input label="Account Number" name="bank_account" :value="old('bank_account', $banking->bank_account ?? '')" />
                <x-form-input label="Routing Number" name="bank_routing" :value="old('bank_routing', $banking->bank_routing ?? '')" />
                <x-form-input label="IBAN" name="iban" :value="old('iban', $banking->iban ?? '')" />
                <x-form-input label="SWIFT Code" name="swift_code" :value="old('swift_code', $banking->swift_code ?? '')" />
            </div>

            <hr class="my-6 border-slate-200">

            <h4 class="font-bold text-slate-700 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-mobile-screen text-purple-600"></i> MFS (Mobile Financial Service)
            </h4>

            <div class="grid gap-6 sm:grid-cols-2">
                <x-form-select label="MFS Type" name="mfs_type" placeholder="-- Select --">
                    @foreach (['bKash', 'Nagad', 'Rocket', 'Upay', 'SureCash', 'Other'] as $mfs)
                        <option value="{{ $mfs }}" {{ old('mfs_type', $banking->mfs_type ?? '') === $mfs ? 'selected' : '' }}>{{ $mfs }}</option>
                    @endforeach
                </x-form-select>
                <x-form-input label="MFS Number" name="mfs_number" :value="old('mfs_number', $banking->mfs_number ?? '')" />
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-8 py-3 text-sm font-bold text-white hover:from-emerald-700 hover:to-teal-700 transition-all shadow-lg">
                    <i class="fa-solid fa-floppy-disk"></i> Save Banking Info
                </button>
            </div>
        </div>
    </form>
</div>