@extends('layouts.app')

@section('content')
    <div class="card p-3">
        <h4>{{ $isEdit ? 'Edit Mata Pelajaran' : 'Tambah Mata Pelajaran' }}</h4>

        @if($errors->any())
            <div class="alert alert-danger py-2">{{ $errors->first() }}</div>
        @endif

        <form method="post" action="{{ $isEdit ? route('subjects.update', $subject) : route('subjects.store') }}">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Nama Mata Pelajaran</label>
                    <input type="text" name="nama_mp" class="form-control" value="{{ old('nama_mp', $subject->nama_mp) }}" required>
                </div>
            </div>

            <div class="mt-3 d-flex gap-2">
                <button class="btn btn-primary">Simpan</button>
                <a href="{{ route('subjects.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
@endsection
