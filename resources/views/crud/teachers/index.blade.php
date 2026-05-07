@extends('layouts.app')

@section('content')
    <div class="card p-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Data Guru</h4>
            <a href="{{ route('teachers.create') }}" class="btn btn-primary btn-sm">Tambah Guru</a>
        </div>

        @if(session('status'))
            <div class="alert alert-success py-2">{{ session('status') }}</div>
        @endif

        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle">
                <thead>
                    <tr>
                        <th>NIP</th>
                        <th>Nama</th>
                        <th>JK</th>
                        <th>Alamat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($teachers as $teacher)
                        <tr>
                            <td>{{ $teacher->nip }}</td>
                            <td>{{ $teacher->nama }}</td>
                            <td>{{ $teacher->jk }}</td>
                            <td>{{ $teacher->alamat }}</td>
                            <td class="d-flex gap-1">
                                <a href="{{ route('teachers.edit', $teacher) }}" class="btn btn-primary btn-sm">Edit</a>
                                <form method="post" action="{{ route('teachers.destroy', $teacher) }}" onsubmit="return confirm('Hapus data guru ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-primary btn-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $teachers->links() }}
    </div>
@endsection
