@extends('laporan.layout')

@section('content')
<table class="data-table">
    <thead>
        <tr>
            <th style="width: 4%;" class="text-center">No</th>
            <th style="width: 12%;">Tanggal</th>
            <th style="width: 12%;">NIS</th>
            <th>Nama Santri</th>
            <th>Wali Santri</th>
            <th style="width: 16%;">Surat</th>
            <th style="width: 6%;" class="text-center">Juz</th>
            <th style="width: 10%;" class="text-center">Ayat</th>
            <th style="width: 8%;" class="text-center">Nilai</th>
            <th style="width: 12%;">Penguji</th>
        </tr>
    </thead>
    <tbody>
        @forelse($items as $idx => $item)
            <tr>
                <td class="text-center font-mono">{{ $idx + 1 }}</td>
                <td>{{ $item->tanggal->translatedFormat('d M Y') }}</td>
                <td class="font-mono">{{ $item->siswa->nis }}</td>
                <td class="font-bold">{{ $item->siswa->nama_siswa }}</td>
                <td>{{ $item->siswa->wali_tampil }}</td>
                <td>{{ $item->surat->nama_surat }}</td>
                <td class="text-center">{{ $item->juz }}</td>
                <td class="text-center">{{ $item->ayat_mulai }}-{{ $item->ayat_selesai }}</td>
                <td class="text-center">
                    <span class="badge-{{ strtolower($item->nilai->value) }}">{{ $item->nilai->value }}</span>
                </td>
                <td>{{ $item->guru?->name ?: '-' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="10" class="text-center" style="padding: 16px; color: #94a3b8; font-style: italic;">
                    Belum ada catatan setoran Al-Qur'an pada rentang tanggal ini.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
@endsection
