@props([
    'id',
    'label',
    'options' => [],
    'placeholder' => 'All',
    'tableId' => null,
])

<div class="flex items-center gap-2">
    <label for="{{ $id }}"
        class="text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">
        {{ $label }}
    </label>

    <select id="{{ $id }}"
        class="dt-filter-{{ $tableId ?? $id }} border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 bg-white focus:border-blue-400 focus:ring-1 focus:ring-blue-100 outline-none min-w-[160px]">

        <option value="">{{ $placeholder }}</option>

        @foreach ($options as $value => $text)
            <option value="{{ $value }}">{{ $text }}</option>
        @endforeach
    </select>
</div>
