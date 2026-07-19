@props(['label', 'name' => null, 'id' => null, 'placeholder' => 'Select an option', 'required' => false])

<div>
    <label class="font-semibold text-sm text-slate-700 block mb-1">{{ $label }}
        @if ($required)
            <span class="text-rose-500 font-bold" aria-hidden="true">*</span>
        @endif
    </label>
    <select id="{{ $id ?? $name }}" name="{{ $name }}"
        {{ $attributes->merge([
            'class' =>
                'w-full border border-slate-300 rounded-md p-2 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all cursor-pointer',
        ]) }}>
        @if ($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        {{ $slot }}
    </select>
</div>
