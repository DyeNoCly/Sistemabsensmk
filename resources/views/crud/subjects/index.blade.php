@extends('layouts.app')

@section('content')
    <div class="card p-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Data Mata Pelajaran</h4>
            <a href="{{ route('subjects.create') }}" class="btn btn-primary btn-sm">Tambah Mata Pelajaran</a>
        </div>

        @if(session('status'))
            <div class="alert alert-success py-2">{{ session('status') }}</div>
        @endif

        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Mata Pelajaran</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subjects as $subject)
                        <tr>
                            <td>{{ $subject->idm }}</td>
                            <td>{{ $subject->nama_mp }}</td>
                            <td class="d-flex gap-1">
                                <a href="{{ route('subjects.edit', $subject) }}" class="btn btn-primary btn-sm">Edit</a>
                                <form method="post" action="{{ route('subjects.destroy', $subject) }}" onsubmit="return confirm('Hapus data mapel ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-primary btn-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $subjects->links() }}
    </div>
@endsection
