@props([
    'label', 
    'name', 
    'id' => null, 
    'placeholder' => '', 
    'rows' => 3,
    'required' => false,
])

<div>
    <label class="font-semibold text-sm text-slate-700 block mb-1">
        {{ $label }}
        @if($required)
            <span class="text-rose-500 font-bold" aria-hidden="true">*</span>
        @endif
    </label>
    <textarea id="{{ $id ?? $name }}" 
              name="{{ $name }}" 
              rows="{{ $rows }}" 
              placeholder="{{ $placeholder }}"
              {{ $required ? 'required' : '' }}
              {{ $attributes->merge([
                  'class' => 'w-full border border-slate-300 rounded-md p-2 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all placeholder:text-slate-400'
              ]) }}>{{ $slot }}</textarea>
</div>