<x-app-layout>
    @include('employee::components.wizard-progress', ['current' => 3])

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white border border-slate-200 shadow-sm rounded-3xl p-8">
            <div class="mb-8 flex items-start justify-between">
                <div>
                    <h1 class="text-3xl font-semibold text-slate-900">Employee Registration</h1>
                    <p class="mt-2 text-sm text-slate-600">Step 3: Capture detailed Bangladesh-style address information.</p>
                </div>
                <form method="POST" action="{{ route('employee.reset.step', 3) }}">
                    @csrf
                    <button type="submit" onclick="return confirm('Reset Step 3 data? This will clear all entered information for this step.')"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-amber-300 px-4 py-2 text-sm font-semibold text-amber-700 transition hover:bg-amber-50 focus:outline-none focus:ring-2 focus:ring-amber-400">
                        ⟳ Refresh
                    </button>
                </form>
            </div>

            @if (session('error'))
                <div class="rounded-2xl bg-rose-50 border border-rose-200 p-4 text-rose-700 mb-6">{{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('employee.store.step3') }}" class="space-y-8">
                @csrf

                {{-- Present Address --}}
                <div class="grid gap-6 bg-slate-50 border border-slate-200 rounded-3xl p-6">
                    <h2 class="text-lg font-semibold text-slate-900">Present Address</h2>

                    <div class="grid gap-6 sm:grid-cols-3">
                        <x-form-input label="House No" name="present_address[house_no]"
                            value="{{ old('present_address.house_no', $data['addresses'][0]['house_no'] ?? '') }}" />
                        <x-form-input label="Road No" name="present_address[road_no]"
                            value="{{ old('present_address.road_no', $data['addresses'][0]['road_no'] ?? '') }}" />
                        <x-form-input label="Road Name" name="present_address[road_name]"
                            value="{{ old('present_address.road_name', $data['addresses'][0]['road_name'] ?? '') }}" />
                    </div>

                    <div class="grid gap-6 sm:grid-cols-3">
                        <x-form-input label="Village" name="present_address[village]"
                            value="{{ old('present_address.village', $data['addresses'][0]['village'] ?? '') }}" />
                        <x-form-input label="Area" name="present_address[area]"
                            value="{{ old('present_address.area', $data['addresses'][0]['area'] ?? '') }}" />
                        <x-form-input label="Post Office" name="present_address[post_office]"
                            value="{{ old('present_address.post_office', $data['addresses'][0]['post_office'] ?? '') }}" />
                    </div>

                    <div class="grid gap-6 sm:grid-cols-3">
                        <x-form-input label="Postal Code" name="present_address[postal_code]"
                            value="{{ old('present_address.postal_code', $data['addresses'][0]['postal_code'] ?? '') }}" />
                        <x-form-input label="City" name="present_address[city]"
                            value="{{ old('present_address.city', $data['addresses'][0]['city'] ?? '') }}" />
                        <x-form-input label="Upazila" name="present_address[upazila]"
                            value="{{ old('present_address.upazila', $data['addresses'][0]['upazila'] ?? '') }}" />
                    </div>

                    <div class="grid gap-6 sm:grid-cols-3">
                        <x-form-input label="District" name="present_address[district]"
                            value="{{ old('present_address.district', $data['addresses'][0]['district'] ?? '') }}" />
                        <x-form-input label="Division" name="present_address[division]"
                            value="{{ old('present_address.division', $data['addresses'][0]['division'] ?? '') }}" />
                        <x-form-input label="Country" name="present_address[country]"
                            value="{{ old('present_address.country', $data['addresses'][0]['country'] ?? 'Bangladesh') }}" />
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <x-form-input label="Latitude" name="present_address[latitude]" type="number" step="any"
                            value="{{ old('present_address.latitude', $data['addresses'][0]['latitude'] ?? '') }}" />
                        <x-form-input label="Longitude" name="present_address[longitude]" type="number" step="any"
                            value="{{ old('present_address.longitude', $data['addresses'][0]['longitude'] ?? '') }}" />
                    </div>
                </div>

                {{-- Permanent Address --}}
                <div class="grid gap-6 bg-slate-50 border border-slate-200 rounded-3xl p-6">
                    <h2 class="text-lg font-semibold text-slate-900">Permanent Address</h2>

                    <div class="grid gap-6 sm:grid-cols-3">
                        <x-form-input label="House No" name="permanent_address[house_no]"
                            value="{{ old('permanent_address.house_no', $data['addresses'][1]['house_no'] ?? '') }}" />
                        <x-form-input label="Road No" name="permanent_address[road_no]"
                            value="{{ old('permanent_address.road_no', $data['addresses'][1]['road_no'] ?? '') }}" />
                        <x-form-input label="Road Name" name="permanent_address[road_name]"
                            value="{{ old('permanent_address.road_name', $data['addresses'][1]['road_name'] ?? '') }}" />
                    </div>

                    <div class="grid gap-6 sm:grid-cols-3">
                        <x-form-input label="Village" name="permanent_address[village]"
                            value="{{ old('permanent_address.village', $data['addresses'][1]['village'] ?? '') }}" />
                        <x-form-input label="Area" name="permanent_address[area]"
                            value="{{ old('permanent_address.area', $data['addresses'][1]['area'] ?? '') }}" />
                        <x-form-input label="Post Office" name="permanent_address[post_office]"
                            value="{{ old('permanent_address.post_office', $data['addresses'][1]['post_office'] ?? '') }}" />
                    </div>

                    <div class="grid gap-6 sm:grid-cols-3">
                        <x-form-input label="Postal Code" name="permanent_address[postal_code]"
                            value="{{ old('permanent_address.postal_code', $data['addresses'][1]['postal_code'] ?? '') }}" />
                        <x-form-input label="City" name="permanent_address[city]"
                            value="{{ old('permanent_address.city', $data['addresses'][1]['city'] ?? '') }}" />
                        <x-form-input label="Upazila" name="permanent_address[upazila]"
                            value="{{ old('permanent_address.upazila', $data['addresses'][1]['upazila'] ?? '') }}" />
                    </div>

                    <div class="grid gap-6 sm:grid-cols-3">
                        <x-form-input label="District" name="permanent_address[district]"
                            value="{{ old('permanent_address.district', $data['addresses'][1]['district'] ?? '') }}" />
                        <x-form-input label="Division" name="permanent_address[division]"
                            value="{{ old('permanent_address.division', $data['addresses'][1]['division'] ?? '') }}" />
                        <x-form-input label="Country" name="permanent_address[country]"
                            value="{{ old('permanent_address.country', $data['addresses'][1]['country'] ?? 'Bangladesh') }}" />
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <x-form-input label="Latitude" name="permanent_address[latitude]" type="number" step="any"
                            value="{{ old('permanent_address.latitude', $data['addresses'][1]['latitude'] ?? '') }}" />
                        <x-form-input label="Longitude" name="permanent_address[longitude]" type="number" step="any"
                            value="{{ old('permanent_address.longitude', $data['addresses'][1]['longitude'] ?? '') }}" />
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex justify-between items-center pt-4 border-t border-slate-200">
                    <a href="{{ route('employee.create.step2') }}"
                        class="text-sm font-medium text-slate-600 hover:text-slate-900">&larr; Back to Step 2</a>

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