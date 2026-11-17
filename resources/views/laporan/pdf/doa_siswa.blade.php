@extends('laporan.layout')

@section('content')
<table class="data-table">
    <thead>
        <tr>
            <th style="width: 5%;" class="text-center">No</th>
            <th style="width: 18%;">Tgl Pengujian</th>
            <th style="width: 30%;">Doa Harian</th>
            <th style="width: 15%;">Tgl Mulai</th>
            <th style="width: 12%;" class="text-center">Nilai</th>
            <th>Catatan</th>
        </tr>
    </thead>
    <tbody>
        @forelse($items as $idx => $item)
            <tr>
                <td class="text-center font-mono">{{ $idx + 1 }}</td>
                <td>{{ $item->tgl_pengujian->translatedFormat('d F Y') }}</td>
                <td class="font-bold">{{ $item->doa->nama_doa }}</td>
                <td>{{ $item->tgl_hafalan ? $item->tgl_hafalan->translatedFormat('d F Y') : '-' }}</td>
                <td class="text-center">
                    <span class="badge-{{ strtolower($item->nilai->value) }}">{{ $item->nilai->value }}</span>
                </td>
                <td>{{ $item->ket ?: '-' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center" style="padding: 16px; color: #94a3b8; font-style: italic;">
                    Belum ada catatan hafalan doa pada rentang tanggal ini.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
@endsection
