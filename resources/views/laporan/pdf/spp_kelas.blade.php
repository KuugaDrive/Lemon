@extends('laporan.layout')

@section('content')
@php
    $totalNominal = $items->sum('nominal');
@endphp

<div style="margin-bottom: 12px; padding: 8px; background-color: #ecfdf5; border: 1px solid #a7f3d0; text-align: right; font-size: 9pt;">
    Total Penerimaan Kelas: <strong style="font-size: 11pt; color: #065f46;">Rp {{ number_format($totalNominal, 0, ',', '.') }}</strong> ({{ $items->count() }} transaksi)
</div>

<table class="data-table">
    <thead>
        <tr>
            <th style="width: 4%;" class="text-center">No</th>
            <th style="width: 14%;">Tgl Bayar</th>
            <th style="width: 14%;">NIS</th>
            <th>Nama Santri</th>
            <th style="width: 18%;">Periode SPP</th>
            <th style="width: 15%;" class="text-right">Nominal</th>
            <th>Keterangan</th>
            <th style="width: 14%;">Petugas</th>
        </tr>
    </thead>
    <tbody>
        @forelse($items as $idx => $item)
            <tr>
                <td class="text-center font-mono">{{ $idx + 1 }}</td>
                <td>{{ $item->tgl_bayar->translatedFormat('d M Y') }}</td>
                <td class="font-mono">{{ $item->siswa->nis }}</td>
                <td class="font-bold">{{ $item->siswa->nama_siswa }}</td>
                <td>{{ $item->periode_label }}</td>
                <td class="text-right font-mono">{{ $item->nominal_rupiah }}</td>
                <td>{{ $item->ket ?: '-' }}</td>
                <td>{{ $item->pencatat?->name ?: '-' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="text-center" style="padding: 16px; color: #94a3b8; font-style: italic;">
                    Belum ada catatan pembayaran SPP pada rentang tanggal ini.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
@endsection
