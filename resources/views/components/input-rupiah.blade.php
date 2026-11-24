@props([
    'name' => 'nominal',
    'id' => null,
    'value' => null,
    'placeholder' => '0',
    'required' => false,
])

@php
    $inputId = $id ?? $name;
    $initialValue = old($name, $value);
    if (is_numeric($initialValue) && (int)$initialValue > 0) {
        $formattedInitial = number_format((int)$initialValue, 0, ',', '.');
    } else {
        $formattedInitial = $initialValue;
    }
@endphp

<div class="relative rounded-lg shadow-sm">
    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
        <span class="text-sm font-semibold text-slate-500">Rp</span>
    </div>
    <input type="text"
           name="{{ $name }}"
           id="{{ $inputId }}"
           inputmode="numeric"
           value="{{ $formattedInitial }}"
           placeholder="{{ $placeholder }}"
           {{ $required ? 'required' : '' }}
           {{ $attributes->merge(['class' => 'block w-full rounded-lg border-slate-300 pl-10 pr-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500 font-mono font-medium']) }}
           oninput="formatRupiahInput(this)">
</div>

@once
<script>
    function formatRupiahInput(element) {
        let value = element.value.replace(/[^0-9]/g, '');
        if (value) {
            element.value = new Intl.NumberFormat('id-ID').format(value);
        } else {
            element.value = '';
        }
    }
</script>
@endonce
