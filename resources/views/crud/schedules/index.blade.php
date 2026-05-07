@extends('layouts.app')

@section('content')
    <div class="card p-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Data Jadwal</h4>
            <a href="{{ route('schedules.create') }}" class="btn btn-primary btn-sm">Tambah Jadwal</a>
        </div>

        @if(session('status'))
            <div class="alert alert-success py-2">{{ session('status') }}</div>
        @endif

        <form class="row g-2 mb-3" method="get">
            <div class="col-md-2">
                <select class="form-select" name="day">
                    <option value="">Hari</option>
                    @foreach($days as $day)
                        <option value="{{ $day->idh }}" @selected((string)($filters['day'] ?? '') === (string) $day->idh)>{{ $day->hari }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" name="teacher">
                    <option value="">Guru</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->idg }}" @selected((string)($filters['teacher'] ?? '') === (string) $teacher->idg)>{{ $teacher->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" name="class">
                    <option value="">Kelas</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->idk }}" @selected((string)($filters['class'] ?? '') === (string) $class->idk)>{{ $class->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" name="subject">
                    <option value="">Mapel</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->idm }}" @selected((string)($filters['subject'] ?? '') === (string) $subject->idm)>{{ $subject->nama_mp }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" name="aktif">
                    <option value="">Aktif</option>
                    <option value="1" @selected((string)($filters['aktif'] ?? '') === '1')>Ya</option>
                    <option value="0" @selected((string)($filters['aktif'] ?? '') === '0')>Tidak</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button class="btn btn-outline-primary w-100">Filter</button>
                <a href="{{ route('schedules.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Hari</th>
                        <th>Guru</th>
                        <th>Kelas</th>
                        <th>Mapel</th>
                        <th>Jam</th>
                        <th>Aktif</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($schedules as $schedule)
                        <tr>
                            <td>{{ $schedule->day?->hari ?? '-' }}</td>
                            <td>{{ $schedule->teacher?->nama ?? '-' }}</td>
                            <td>{{ $schedule->kelas?->nama ?? '-' }}</td>
                            <td>{{ $schedule->subject?->nama_mp ?? '-' }}</td>
                            <td>{{ $schedule->jam_mulai }} - {{ $schedule->jam_selesai }}</td>
                            <td>{{ (int) $schedule->aktif === 1 ? 'Ya' : 'Tidak' }}</td>
                            <td class="d-flex gap-1">
                                <a href="{{ route('schedules.edit', $schedule) }}" class="btn btn-primary btn-sm">Edit</a>
                                <form method="post" action="{{ route('schedules.destroy', $schedule) }}" onsubmit="return confirm('Hapus jadwal ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-primary btn-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $schedules->links() }}
    </div>
@endsection
