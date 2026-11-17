@extends('laporan.layout')

@section('content')
<table class="data-table">
    <thead>
        <tr>
            <th style="width: 5%;" class="text-center">No</th>
            <th style="width: 20%;">Tanggal</th>
            <th style="width: 25%;">Materi Iqro</th>
            <th style="width: 15%;" class="text-center">Halaman</th>
            <th style="width: 12%;" class="text-center">Penilaian</th>
            <th>Keterangan / Catatan</th>
        </tr>
    </thead>
    <tbody>
        @forelse($items as $idx => $item)
            <tr>
                <td class="text-center font-mono">{{ $idx + 1 }}</td>
                <td>{{ $item->tanggal->translatedFormat('d F Y') }}</td>
                <td class="font-bold">{{ $item->iqro->nama_iqro }}</td>
                <td class="text-center">Hal. {{ $item->halaman }}</td>
                <td class="text-center">
                    <span class="badge-{{ strtolower($item->nilai->value) }}">{{ $item->nilai->value }}</span>
                </td>
                <td>{{ $item->ket ?: '-' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center" style="padding: 16px; color: #94a3b8; font-style: italic;">
                    Belum ada catatan setoran Iqro pada rentang tanggal ini.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
@endsection
