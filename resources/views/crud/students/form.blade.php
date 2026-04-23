@extends('layouts.app')

@section('content')
    <div class="card p-3">
        <h4>{{ $isEdit ? 'Edit Siswa' : 'Tambah Siswa' }}</h4>

        @if($errors->any())
            <div class="alert alert-danger py-2">{{ $errors->first() }}</div>
        @endif

        <form method="post" action="{{ $isEdit ? route('students.update', $student) : route('students.store') }}">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">NIS</label>
                    <input type="text" name="nis" class="form-control" value="{{ old('nis', $student->nis) }}" required>
                </div>
                <div class="col-md-8">
                    <label class="form-label">Nama</label>
                    <input type="text" name="nama" class="form-control" value="{{ old('nama', $student->nama) }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">JK</label>
                    <select name="jk" class="form-select" required>
                        <option value="L" @selected(old('jk', $student->jk) === 'L')>L</option>
                        <option value="P" @selected(old('jk', $student->jk) === 'P')>P</option>
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label">Kelas</label>
                    <select name="idk" class="form-select" required>
                        @foreach($classes as $class)
                            <option value="{{ $class->idk }}" @selected((string) old('idk', $student->idk) === (string) $class->idk)>{{ $class->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Telepon</label>
                    <input type="text" name="tlp" class="form-control" value="{{ old('tlp', $student->tlp) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nama Bapak</label>
                    <input type="text" name="bapak" class="form-control" value="{{ old('bapak', $student->bapak) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Pekerjaan Bapak</label>
                    <input type="text" name="k_bapak" class="form-control" value="{{ old('k_bapak', $student->k_bapak) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nama Ibu</label>
                    <input type="text" name="ibu" class="form-control" value="{{ old('ibu', $student->ibu) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Pekerjaan Ibu</label>
                    <input type="text" name="k_ibu" class="form-control" value="{{ old('k_ibu', $student->k_ibu) }}">
                </div>
                <div class="col-12">
                    <label class="form-label">Alamat</label>
                    <textarea name="alamat" class="form-control" rows="2" required>{{ old('alamat', $student->alamat) }}</textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Password {{ $isEdit ? '(kosongkan jika tidak diubah)' : '' }}</label>
                    <input type="password" name="password" class="form-control" {{ $isEdit ? '' : 'required' }}>
                </div>
            </div>

            <div class="mt-3 d-flex gap-2">
                <button class="btn btn-primary">Simpan</button>
                <a href="{{ route('students.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
@endsection
