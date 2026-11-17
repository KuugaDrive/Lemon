@extends('laporan.layout')

@section('content')
<table class="data-table">
    <thead>
        <tr>
            <th style="width: 5%;" class="text-center">No</th>
            <th style="width: 18%;">Tanggal</th>
            <th style="width: 25%;">Surat Al-Qur'an</th>
            <th style="width: 10%;" class="text-center">Juz</th>
            <th style="width: 15%;" class="text-center">Ayat</th>
            <th style="width: 10%;" class="text-center">Nilai</th>
            <th>Catatan</th>
        </tr>
    </thead>
    <tbody>
        @forelse($items as $idx => $item)
            <tr>
                <td class="text-center font-mono">{{ $idx + 1 }}</td>
                <td>{{ $item->tanggal->translatedFormat('d F Y') }}</td>
                <td class="font-bold">{{ $item->surat->nomor }}. {{ $item->surat->nama_surat }}</td>
                <td class="text-center">{{ $item->juz }}</td>
                <td class="text-center">{{ $item->ayat_mulai }} - {{ $item->ayat_selesai }}</td>
                <td class="text-center">
                    <span class="badge-{{ strtolower($item->nilai->value) }}">{{ $item->nilai->value }}</span>
                </td>
                <td>{{ $item->ket ?: '-' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center" style="padding: 16px; color: #94a3b8; font-style: italic;">
                    Belum ada catatan setoran Al-Qur'an pada rentang tanggal ini.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
@endsection
