@extends('layouts.app')

@section('content')
    <div class="card p-3">
        <h4>{{ $isEdit ? 'Edit Absensi' : 'Tambah Absensi' }}</h4>
        @php
            $statusLabels = [
                'H' => 'Hadir',
                'I' => 'Izin',
                'A' => 'Alpha',
            ];
        @endphp

        @if($errors->any())
            <div class="alert alert-danger py-2">{{ $errors->first() }}</div>
        @endif

        <form method="post" action="{{ $isEdit ? route('attendances.update', $attendance) : route('attendances.store') }}">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            @if(!$isEdit)
                <div class="alert alert-info py-2">
                    Form ini bisa dibuka dari dashboard guru dengan kelas dan mata pelajaran yang sudah dipilih.
                </div>
            @endif

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Siswa</label>
                    <select name="nis" class="form-select" required>
                        <option value="">Pilih siswa</option>
                        @foreach($students as $student)
                            <option value="{{ $student->nis }}" @selected((string) old('nis', $attendance->nis) === (string) $student->nis)>{{ $student->nama }} ({{ $student->nis }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Mata Pelajaran</label>
                    <select name="idm" class="form-select" required>
                        <option value="">Pilih mapel</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->idm }}" @selected((string) old('idm', $attendance->idm) === (string) $subject->idm)>{{ $subject->nama_mp }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tanggal</label>
                    <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal', optional($attendance->tanggal)->format('Y-m-d')) }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" required>
                        <option value="H" @selected(old('status', $attendance->status) === 'H')>Hadir</option>
                        <option value="I" @selected(old('status', $attendance->status) === 'I')>Izin</option>
                        <option value="A" @selected(old('status', $attendance->status) === 'A')>Alpha</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Latitude</label>
                    <input type="text" name="latitude" class="form-control" value="{{ old('latitude', $attendance->latitude) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Longitude</label>
                    <input type="text" name="longitude" class="form-control" value="{{ old('longitude', $attendance->longitude) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Photo Path</label>
                    <input type="text" name="photo_path" class="form-control" value="{{ old('photo_path', $attendance->photo_path) }}">
                </div>
            </div>

            <div class="mt-3 d-flex gap-2">
                <button class="btn btn-primary">Simpan</button>
                <a href="{{ route('attendances.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
@endsection