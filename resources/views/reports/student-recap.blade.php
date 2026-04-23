@extends('layouts.app')

@section('content')
    <div class="card p-3">
        <h4 class="mb-2">Rekap Absensi Siswa</h4>
        <div class="text-muted mb-3">{{ $student->nama }} | NIS {{ $student->nis }} | Kelas {{ $student->kelas?->nama ?? '-' }}</div>

        <div class="table-responsive mb-4">
            <table class="table table-bordered table-striped align-middle">
                <thead>
                    <tr>
                        <th>Jadwal</th>
                        <th>Mata Pelajaran</th>
                        <th>Hari</th>
                        @foreach($dates as $date)
                            <th>{{ $date }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($schedule as $item)
                        <tr>
                            <td>{{ $item->jam_mulai }} - {{ $item->jam_selesai }}</td>
                            <td>{{ $item->subject?->nama_mp ?? '-' }}</td>
                            <td>{{ $item->day?->hari ?? '-' }}</td>
                            @foreach($dates as $date)
                                @php
                                    $status = $attendances[$item->idm]->firstWhere('tanggal', $date)?->status ?? '-';
                                @endphp
                                <td class="text-center">{{ $status }}</td>
                            @endforeach
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center text-muted">Belum ada jadwal untuk kelas ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="alert alert-info mb-0">
            <strong>Keterangan:</strong> H = Hadir, I = Izin, A = Alpha
        </div>
    </div>
@endsection
