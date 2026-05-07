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

            @php
                $jamMulai = old('jam_mulai', $schedule->jam_mulai ? substr((string) $schedule->jam_mulai, 0, 5) : '');
                $jamSelesai = old('jam_selesai', $schedule->jam_selesai ? substr((string) $schedule->jam_selesai, 0, 5) : '');
            @endphp

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
                    <input
                        type="text"
                        name="jam_mulai"
                        class="form-control"
                        value="{{ $jamMulai }}"
                        placeholder="08:00"
                        maxlength="5"
                        inputmode="numeric"
                        pattern="^(?:[01]\d|2[0-3]):[0-5]\d$"
                        title="Gunakan format 24 jam, contoh 08:00 atau 13:30"
                        required
                    >
                </div>
                <div class="col-md-3">
                    <label class="form-label">Jam Selesai</label>
                    <input
                        type="text"
                        name="jam_selesai"
                        class="form-control"
                        value="{{ $jamSelesai }}"
                        placeholder="17:00"
                        maxlength="5"
                        inputmode="numeric"
                        pattern="^(?:[01]\d|2[0-3]):[0-5]\d$"
                        title="Gunakan format 24 jam, contoh 08:00 atau 13:30"
                        required
                    >
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

        <!-- Flatpickr 24-hour time picker -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                try {
                    flatpickr("input[name='jam_mulai'], input[name='jam_selesai']", {
                        enableTime: true,
                        noCalendar: true,
                        dateFormat: 'H:i',
                        time_24hr: true,
                        minuteIncrement: 5,
                        allowInput: true
                    });
                } catch (e) {
                    // If flatpickr fails to load, fallback to native/text validation (already present)
                    console.warn('Flatpickr init failed', e);
                }
            });
        </script>
    </div>
@endsection