@extends('layouts.app')

@section('content')
    <div class="card p-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Data Sekolah</h4>
            <a href="{{ route('schools.create') }}" class="btn btn-primary btn-sm">Tambah Sekolah</a>
        </div>

        @if(session('status'))
            <div class="alert alert-success py-2">{{ session('status') }}</div>
        @endif

        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Alamat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($schools as $school)
                        <tr>
                            <td>{{ $school->kode }}</td>
                            <td>{{ $school->nama }}</td>
                            <td>{{ $school->alamat }}</td>
                            <td class="d-flex gap-1">
                                <a href="{{ route('schools.edit', $school) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form method="post" action="{{ route('schools.destroy', $school) }}" onsubmit="return confirm('Hapus data sekolah ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $schools->links() }}
    </div>
@endsection
