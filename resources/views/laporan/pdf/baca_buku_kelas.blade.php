@extends('laporan.layout')

@section('content')
<table class="data-table">
    <thead>
        <tr>
            <th style="width: 4%;" class="text-center">No</th>
            <th style="width: 14%;">Tgl Uji</th>
            <th style="width: 14%;">NIS</th>
            <th>Nama Santri</th>
            <th style="width: 18%;">Huruf Hijaiyah</th>
            <th style="width: 16%;">Jilid & Halaman</th>
            <th style="width: 10%;" class="text-center">Nilai</th>
            <th style="width: 14%;">Penguji</th>
        </tr>
    </thead>
    <tbody>
        @forelse($items as $idx => $item)
            <tr>
                <td class="text-center font-mono">{{ $idx + 1 }}</td>
                <td>{{ $item->tgl_pengujian->translatedFormat('d M Y') }}</td>
                <td class="font-mono">{{ $item->siswa->nis }}</td>
                <td class="font-bold">{{ $item->siswa->nama_siswa }}</td>
                <td>{{ $item->alfabet->nama_alfabet }}</td>
                <td>{{ $item->jilid }} (Hal. {{ $item->halaman }})</td>
                <td class="text-center">
                    <span class="badge-{{ strtolower($item->nilai->value) }}">{{ $item->nilai->value }}</span>
                </td>
                <td>{{ $item->guru?->name ?: '-' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="text-center" style="padding: 16px; color: #94a3b8; font-style: italic;">
                    Belum ada catatan baca buku pada rentang tanggal ini.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
@endsection
