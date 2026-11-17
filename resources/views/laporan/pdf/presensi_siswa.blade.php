@extends('laporan.layout')

@section('content')
@php
    $hadir = $items->where('status.value', 'hadir')->count();
    $izin = $items->where('status.value', 'izin')->count();
    $sakit = $items->where('status.value', 'sakit')->count();
    $alfa = $items->where('status.value', 'alfa')->count();
    $total = $items->count();
    $persen = $total > 0 ? round(($hadir / $total) * 100) : 0;
@endphp

<table style="width: 100%; margin-bottom: 12px; font-size: 8.5pt;">
    <tr>
        <td style="padding: 6px; background-color: #ecfdf5; border: 1px solid #a7f3d0; text-align: center;">
            <strong>Hadir:</strong> {{ $hadir }} Hari
        </td>
        <td style="padding: 6px; background-color: #eff6ff; border: 1px solid #bfdbfe; text-align: center;">
            <strong>Izin:</strong> {{ $izin }} Hari
        </td>
        <td style="padding: 6px; background-color: #fffbeb; border: 1px solid #fde68a; text-align: center;">
            <strong>Sakit:</strong> {{ $sakit }} Hari
        </td>
        <td style="padding: 6px; background-color: #fff1f2; border: 1px solid #fecdd3; text-align: center;">
            <strong>Alfa:</strong> {{ $alfa }} Hari
        </td>
        <td style="padding: 6px; background-color: #f8fafc; border: 1px solid #cbd5e1; text-align: center;">
            <strong>% Kehadiran:</strong> <strong>{{ $persen }}%</strong>
        </td>
    </tr>
</table>

<table class="data-table">
    <thead>
        <tr>
            <th style="width: 5%;" class="text-center">No</th>
            <th style="width: 20%;">Tanggal</th>
            <th style="width: 15%;">Hari</th>
            <th style="width: 15%;" class="text-center">Status</th>
            <th>Keterangan</th>
            <th style="width: 20%;">Guru Penginput</th>
        </tr>
    </thead>
    <tbody>
        @forelse($items as $idx => $item)
            <tr>
                <td class="text-center font-mono">{{ $idx + 1 }}</td>
                <td>{{ $item->tanggal->translatedFormat('d F Y') }}</td>
                <td>{{ $item->tanggal->translatedFormat('l') }}</td>
                <td class="text-center">
                    <strong>{{ $item->status->label() }}</strong>
                </td>
                <td>{{ $item->ket ?: '-' }}</td>
                <td>{{ $item->guru?->name ?: '-' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center" style="padding: 16px; color: #94a3b8; font-style: italic;">
                    Tidak ada catatan presensi pada rentang tanggal ini.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
@endsection
