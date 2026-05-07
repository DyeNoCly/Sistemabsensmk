@extends('layouts.app')

@section('content')
    <div class="card p-3">
        <h4>{{ $isEdit ? 'Edit Kelas' : 'Tambah Kelas' }}</h4>

        @if($errors->any())
            <div class="alert alert-danger py-2">{{ $errors->first() }}</div>
        @endif

        <form method="post" action="{{ $isEdit ? route('classes.update', $classRecord) : route('classes.store') }}">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nama Kelas</label>
                    <input type="text" name="nama" class="form-control" value="{{ old('nama', $classRecord->nama) }}" required>
                </div>
            </div>

            <div class="mt-3 d-flex gap-2">
                <button class="btn btn-primary">Simpan</button>
                <a href="{{ route('classes.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
@endsection