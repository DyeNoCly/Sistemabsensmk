@extends('layouts.app')

@section('content')
    <div class="card p-3">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
                <h4 class="mb-1">Rekap Absensi Guru</h4>
                <div class="text-muted">{{ $teacher->nama }} | NIP {{ $teacher->nip }}</div>
            </div>
            <a href="{{ route('reports.teacher-recap.pdf') }}" class="btn btn-success btn-sm">Cetak PDF</a>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead>
                    <tr>
                        <th>Hari</th>
                        <th>Kelas</th>
                        <th>Mata Pelajaran</th>
                        <th>Jam</th>
                        <th>Aktif</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($schedules as $schedule)
                        <tr>
                            <td>{{ $schedule->day?->hari ?? '-' }}</td>
                            <td>{{ $schedule->kelas?->nama ?? '-' }}</td>
                            <td>{{ $schedule->subject?->nama_mp ?? '-' }}</td>
                            <td>{{ $schedule->jam_mulai }} - {{ $schedule->jam_selesai }}</td>
                            <td>{{ (int) $schedule->aktif === 1 ? 'Ya' : 'Tidak' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted">Belum ada jadwal mengajar.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
