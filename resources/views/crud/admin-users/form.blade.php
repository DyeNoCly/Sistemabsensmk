@extends('layouts.app')

@section('content')
    <div class="card p-3">
        <h4>{{ $isEdit ? 'Edit Admin User' : 'Tambah Admin User' }}</h4>

        @if($errors->any())
            <div class="alert alert-danger py-2">{{ $errors->first() }}</div>
        @endif

        <form method="post" action="{{ $isEdit ? route('admin-users.update', $adminUser) : route('admin-users.store') }}">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Username</label>
                    <input type="text" name="nama" class="form-control" value="{{ old('nama', $adminUser->nama) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Level</label>
                    <select name="level" class="form-select" required>
                        <option value="admin" @selected(old('level', $adminUser->level) === 'admin')>admin</option>
                        <option value="guru" @selected(old('level', $adminUser->level) === 'guru')>guru</option>
                        <option value="user" @selected(old('level', $adminUser->level) === 'user')>user</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Sekolah</label>
                    <select name="id" class="form-select" required>
                        @foreach($schools as $school)
                            <option value="{{ $school->id }}" @selected((string) old('id', $adminUser->id) === (string) $school->id)>{{ $school->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Password {{ $isEdit ? '(kosongkan jika tidak diubah)' : '' }}</label>
                    <input type="password" name="password" class="form-control" {{ $isEdit ? '' : 'required' }}>
                </div>
            </div>

            <div class="mt-3 d-flex gap-2">
                <button class="btn btn-primary">Simpan</button>
                <a href="{{ route('admin-users.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
@endsection
