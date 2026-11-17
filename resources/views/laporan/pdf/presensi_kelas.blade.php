@extends('laporan.layout')

@section('content')
<table class="data-table">
    <thead>
        <tr>
            <th style="width: 4%;" class="text-center">No</th>
            <th style="width: 14%;">NIS</th>
            <th>Nama Santri</th>
            <th style="width: 10%;" class="text-center">Hadir</th>
            <th style="width: 10%;" class="text-center">Izin</th>
            <th style="width: 10%;" class="text-center">Sakit</th>
            <th style="width: 10%;" class="text-center">Alfa</th>
            <th style="width: 12%;" class="text-center">Total Hari</th>
            <th style="width: 12%;" class="text-center">% Kehadiran</th>
        </tr>
    </thead>
    <tbody>
        @forelse($rekapData as $idx => $row)
            <tr>
                <td class="text-center font-mono">{{ $idx + 1 }}</td>
                <td class="font-mono">{{ $row->siswa->nis }}</td>
                <td class="font-bold">{{ $row->siswa->nama_siswa }}</td>
                <td class="text-center">{{ $row->hadir }}</td>
                <td class="text-center">{{ $row->izin }}</td>
                <td class="text-center">{{ $row->sakit }}</td>
                <td class="text-center">{{ $row->alfa }}</td>
                <td class="text-center font-mono">{{ $row->total_hari }}</td>
                <td class="text-center font-bold">
                    {{ $row->persentase }}%
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="9" class="text-center" style="padding: 16px; color: #94a3b8; font-style: italic;">
                    Tidak ada santri terdaftar pada kelas ini.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
@endsection
