@extends('layouts.app')

@section('content')
    <div class="card p-3">
        <h4>{{ $isEdit ? 'Edit Jadwal' : 'Tambah Jadwal' }}</h4>

        @if($errors->any())
            <div class="alert alert-danger py-2">{{ $errors->first() }}</div>
        @endif

        <form method="post" action="{{ $isEdit ? route('schedules.update', $schedule) : route('schedules.store') }}">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Hari</label>
                    <select name="idh" class="form-select" required>
                        @foreach($days as $day)
                            <option value="{{ $day->idh }}" @selected((string) old('idh', $schedule->idh) === (string) $day->idh)>{{ $day->hari }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Guru</label>
                    <select name="idg" class="form-select" required>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->idg }}" @selected((string) old('idg', $schedule->idg) === (string) $teacher->idg)>{{ $teacher->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Kelas</label>
                    <select name="idk" class="form-select" required>
                        @foreach($classes as $class)
                            <option value="{{ $class->idk }}" @selected((string) old('idk', $schedule->idk) === (string) $class->idk)>{{ $class->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Mapel</label>
                    <select name="idm" class="form-select" required>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->idm }}" @selected((string) old('idm', $schedule->idm) === (string) $subject->idm)>{{ $subject->nama_mp }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Jam Mulai</label>
                    <input type="time" name="jam_mulai" class="form-control" value="{{ old('jam_mulai', $schedule->jam_mulai) }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Jam Selesai</label>
                    <input type="time" name="jam_selesai" class="form-control" value="{{ old('jam_selesai', $schedule->jam_selesai) }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Aktif</label>
                    <select name="aktif" class="form-select" required>
                        <option value="1" @selected((string) old('aktif', $schedule->aktif) === '1')>Ya</option>
                        <option value="0" @selected((string) old('aktif', $schedule->aktif) === '0')>Tidak</option>
                    </select>
                </div>
            </div>

            <div class="mt-3 d-flex gap-2">
                <button class="btn btn-primary">Simpan</button>
                <a href="{{ route('schedules.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
@endsection
