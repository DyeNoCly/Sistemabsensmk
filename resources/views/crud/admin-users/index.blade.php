@extends('layouts.app')

@section('content')
    <div class="card p-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Data Admin User</h4>
            <a href="{{ route('admin-users.create') }}" class="btn btn-primary btn-sm">Tambah User</a>
        </div>

        @if(session('status'))
            <div class="alert alert-success py-2">{{ session('status') }}</div>
        @endif

        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Level</th>
                        <th>Sekolah ID</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>{{ $user->nama }}</td>
                            <td>{{ $user->level }}</td>
                            <td>{{ $user->id }}</td>
                            <td class="d-flex gap-1">
                                <a href="{{ route('admin-users.edit', $user) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form method="post" action="{{ route('admin-users.destroy', $user) }}" onsubmit="return confirm('Hapus user ini?')">
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

        {{ $users->links() }}
    </div>
@endsection
