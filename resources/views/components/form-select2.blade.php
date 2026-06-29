@props([
    'label',
    'name' => null,
    'id' => null,
    'placeholder' => 'Select an option',
    'required' => false,
    'multiple' => false,
    'options' => [],
    'selected' => null,
    'disabled' => false,
    'class' => null,
])

<div>
    @if ($label)
        <label for="{{ $id ?? $name }}" class="font-semibold text-sm text-slate-700 block mb-1">
            {{ $label }}
            @if ($required)
                <span class="text-rose-500 font-bold" aria-hidden="true">*</span>
            @endif
        </label>
    @endif

    <select
        id="{{ $id ?? $name }}"
        name="{{ $name }}"
        @if ($required) required @endif
        @if ($multiple) multiple @endif
        @if ($disabled) disabled @endif
        {{ $attributes->merge(['class' => 'select2 w-full']) }}
    >
        @if ($placeholder && !$multiple)
            <option value="">{{ $placeholder }}</option>
        @endif

        @forelse ($options as $key => $value)
            @php
                $isSelected = false;
                if ($selected !== null) {
                    if (is_array($selected)) {
                        $isSelected = in_array($key, $selected);
                    } else {
                        $isSelected = $selected == $key;
                    }
                }
            @endphp
            <option value="{{ $key }}" @selected($isSelected)>
                {{ $value }}
            </option>
        @empty
            {{ $slot }}
        @endforelse
    </select>

    @push('scripts')
        <script>
            $(document).ready(function() {
                $('#{{ $id ?? $name }}').select2({
                    placeholder: '{{ $placeholder }}',
                    width: '100%'
                });
            });
        </script>
    @endpush
</div>