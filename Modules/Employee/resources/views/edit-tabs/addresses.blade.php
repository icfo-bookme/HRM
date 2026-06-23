<div id="tab-addresses" class="{{ $activeTab === 'addresses' ? '' : 'hidden' }}">
    <form class="section-save-form" action="{{ route('employee.edit.addresses', $employee->id) }}">
        @csrf
        @method('PUT')
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center">
                    <i class="fa-solid fa-location-dot text-amber-600"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-900">Address Information</h2>
                    <p class="text-xs text-slate-500">Contact & location</p>
                </div>
            </div>

            @php
                $presentAddr = $employee->addresses->where('address_type', 'present')->first();
                $permanentAddr = $employee->addresses->where('address_type', 'permanent')->first();
            @endphp

            {{-- Present Address --}}
            <div class="border border-slate-200 rounded-2xl p-6 mb-6">
                <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-location-dot text-sky-500"></i> Present Address
                </h3>
                <div class="grid gap-5 sm:grid-cols-2">
                    <input type="hidden" name="addresses[0][address_type]" value="present">
                    @if($presentAddr)
                        <input type="hidden" name="addresses[0][id]" value="{{ $presentAddr->id }}">
                    @endif
                    <x-form-input label="House / Holding No" name="addresses[0][house_no]" :value="old('addresses.0.house_no', $presentAddr->house_no ?? '')" />
                    <x-form-input label="Road No" name="addresses[0][road_no]" :value="old('addresses.0.road_no', $presentAddr->road_no ?? '')" />
                    <div class="sm:col-span-2">
                        <x-form-input label="Road / Village / Area" name="addresses[0][road_name]" :value="old('addresses.0.road_name', $presentAddr->road_name ?? '')" placeholder="Road name, village or area" />
                    </div>
                    <x-form-input label="City" name="addresses[0][city]" :value="old('addresses.0.city', $presentAddr->city ?? '')" />
                    <x-form-input label="District" name="addresses[0][district]" :value="old('addresses.0.district', $presentAddr->district ?? '')" />
                    <x-form-input label="Postal Code" name="addresses[0][postal_code]" :value="old('addresses.0.postal_code', $presentAddr->postal_code ?? '')" />
                    <x-form-input label="Country" name="addresses[0][country]" :value="old('addresses.0.country', $presentAddr->country ?? 'Bangladesh')" />
                </div>
            </div>

            {{-- Permanent Address --}}
            <div class="border border-slate-200 rounded-2xl p-6">
                <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-house text-indigo-500"></i> Permanent Address
                </h3>
                <div class="grid gap-5 sm:grid-cols-2">
                    <input type="hidden" name="addresses[1][address_type]" value="permanent">
                    @if($permanentAddr)
                        <input type="hidden" name="addresses[1][id]" value="{{ $permanentAddr->id }}">
                    @endif
                    <x-form-input label="House / Holding No" name="addresses[1][house_no]" :value="old('addresses.1.house_no', $permanentAddr->house_no ?? '')" />
                    <x-form-input label="Road No" name="addresses[1][road_no]" :value="old('addresses.1.road_no', $permanentAddr->road_no ?? '')" />
                    <div class="sm:col-span-2">
                        <x-form-input label="Road / Village / Area" name="addresses[1][road_name]" :value="old('addresses.1.road_name', $permanentAddr->road_name ?? '')" placeholder="Road name, village or area" />
                    </div>
                    <x-form-input label="City" name="addresses[1][city]" :value="old('addresses.1.city', $permanentAddr->city ?? '')" />
                    <x-form-input label="District" name="addresses[1][district]" :value="old('addresses.1.district', $permanentAddr->district ?? '')" />
                    <x-form-input label="Postal Code" name="addresses[1][postal_code]" :value="old('addresses.1.postal_code', $permanentAddr->postal_code ?? '')" />
                    <x-form-input label="Country" name="addresses[1][country]" :value="old('addresses.1.country', $permanentAddr->country ?? 'Bangladesh')" />
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-8 py-3 text-sm font-bold text-white hover:from-emerald-700 hover:to-teal-700 transition-all shadow-lg">
                    <i class="fa-solid fa-floppy-disk"></i> Save Address Info
                </button>
            </div>
        </div>
    </form>
</div>