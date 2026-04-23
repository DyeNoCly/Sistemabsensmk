@extends('layouts.app')

@section('content')
    @php
        /** @var \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection $classes */
    @endphp
    <div class="card p-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Data Kelas</h4>
            <a href="{{ route('classes.create') }}" class="btn btn-primary btn-sm">Tambah Kelas</a>
        </div>

        @if(session('status'))
            <div class="alert alert-success py-2">{{ session('status') }}</div>
        @endif

        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Nama Kelas</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($classes as $classItem)
                        <tr>
                            <td>{{ $classItem->nama }}</td>
                            <td class="d-flex gap-1">
                                <a href="{{ route('classes.edit', $classItem) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form method="post" action="{{ route('classes.destroy', $classItem) }}" onsubmit="return confirm('Hapus data kelas ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-center text-muted">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $classes->links() }}
    </div>
@endsection
