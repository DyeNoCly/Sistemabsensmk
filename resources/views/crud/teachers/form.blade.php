@extends('layouts.app')

@section('content')
    <div class="card p-3">
        <h4>{{ $isEdit ? 'Edit Guru' : 'Tambah Guru' }}</h4>

        @if($errors->any())
            <div class="alert alert-danger py-2">{{ $errors->first() }}</div>
        @endif

        <form method="post" action="{{ $isEdit ? route('teachers.update', $teacher) : route('teachers.store') }}">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">NIP</label>
                    <input type="text" name="nip" class="form-control" value="{{ old('nip', $teacher->nip) }}" required>
                </div>
                <div class="col-md-8">
                    <label class="form-label">Nama</label>
                    <input type="text" name="nama" class="form-control" value="{{ old('nama', $teacher->nama) }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">JK</label>
                    <select name="jk" class="form-select" required>
                        <option value="L" @selected(old('jk', $teacher->jk) === 'L')>L</option>
                        <option value="P" @selected(old('jk', $teacher->jk) === 'P')>P</option>
                    </select>
                </div>
                <div class="col-md-9">
                    <label class="form-label">Alamat</label>
                    <input type="text" name="alamat" class="form-control" value="{{ old('alamat', $teacher->alamat) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Password {{ $isEdit ? '(kosongkan jika tidak diubah)' : '' }}</label>
                    <input type="password" name="password" class="form-control" {{ $isEdit ? '' : 'required' }}>
                </div>
            </div>

            <div class="mt-3 d-flex gap-2">
                <button class="btn btn-primary">Simpan</button>
                <a href="{{ route('teachers.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
@endsection
