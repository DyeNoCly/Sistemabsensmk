@extends('layouts.app')

@section('content')
    @php
        /** @var \Illuminate\Pagination\LengthAwarePaginator $students */
        /** @var \Illuminate\Support\Collection<int, \App\Models\Kelas> $classes */
        /** @var int $selectedClassId */
    @endphp
    <style>
        .student-filter-form {
            display: flex;
            align-items: flex-end;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 14px;
        }

        .student-filter-field {
            min-width: 280px;
            max-width: 360px;
            flex: 1 1 320px;
        }

        .student-filter-field .form-label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
        }

        .student-filter-actions {
            display: inline-flex;
            gap: 8px;
            align-items: center;
        }
    </style>

    <div class="card p-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Data Siswa</h4>
            <a href="{{ route('students.create') }}" class="btn btn-primary btn-sm">Tambah Siswa</a>
        </div>

        <form method="get" action="{{ route('students.index') }}" class="student-filter-form">
            <div class="student-filter-field">
                <label for="class_id" class="form-label mb-1">Kategori Kelas</label>
                <select id="class_id" name="class_id" class="form-control">
                    <option value="0">Semua Kelas</option>
                    @foreach($classes as $classItem)
                        <option value="{{ $classItem->idk }}" @selected((int) $selectedClassId === (int) $classItem->idk)>{{ $classItem->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="student-filter-actions">
                <button type="submit" class="btn btn-primary btn-sm">Terapkan</button>
                <a href="{{ route('students.index') }}" class="btn btn-default btn-sm">Reset</a>
            </div>
        </form>

        @if(session('status'))
            <div class="alert alert-success py-2">{{ session('status') }}</div>
        @endif

        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle">
                <thead>
                    <tr>
                        <th>NIS</th>
                        <th>Nama</th>
                        <th>JK</th>
                        <th>Kelas</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                        <tr>
                            <td>{{ $student->nis }}</td>
                            <td>{{ $student->nama }}</td>
                            <td>{{ $student->jk }}</td>
                            <td>{{ $student->kelas?->nama ?? '-' }}</td>
                            <td class="d-flex gap-1">
                                <a href="{{ route('students.edit', $student) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form method="post" action="{{ route('students.destroy', $student) }}" onsubmit="return confirm('Hapus data siswa ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">
                                Tidak ada data{{ (int) $selectedClassId > 0 ? ' pada kategori kelas ini' : '' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $students->links() }}
    </div>
@endsection
