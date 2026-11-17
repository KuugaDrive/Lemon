@props(['nilai'])

@php
    $enumVal = $nilai instanceof \App\Enums\Penilaian ? $nilai : \App\Enums\Penilaian::tryFrom($nilai);
@endphp

@if($enumVal)
    <span {{ $attributes->merge(['class' => 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border ' . $enumVal->badgeClass()]) }} title="{{ $enumVal->deskripsi() }}">
        {{ $enumVal->value }}
    </span>
@else
    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-600">
        {{ $nilai }}
    </span>
@endif
