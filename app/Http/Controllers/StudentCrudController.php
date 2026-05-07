<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentCrudController extends Controller
{
    public function index(Request $request): View
    {
        $selectedClassId = $request->integer('class_id');

        $studentQuery = Student::query()->with('kelas')->orderBy('nama');

        if ($selectedClassId > 0) {
            $studentQuery->where('idk', $selectedClassId);
        }

        return view('crud.siswa.data-siswa', [
            'sessionUser' => $request->session()->get('legacy_user'),
            'students' => $studentQuery->paginate(20)->appends($request->query()),
            'classes' => Kelas::query()->orderBy('nama')->get(),
            'selectedClassId' => $selectedClassId,
        ]);
    }

    public function create(Request $request): View
    {
        return view('crud.siswa.input-siswa', [
            'sessionUser' => $request->session()->get('legacy_user'),
            'student' => new Student(),
            'classes' => Kelas::query()->orderBy('nama')->get(),
            'isEdit' => false,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nis' => ['required', 'string', 'max:50', 'unique:siswa,nis'],
            'nisn' => ['nullable', 'string', 'max:50'],
            'nama' => ['required', 'string', 'max:100'],
            'jk' => ['required', 'in:L,P'],
            'idk' => ['required', 'integer', 'exists:kelas,idk'],
        ]);

        Student::query()->create($data);

        return redirect()->route('students.index')->with('status', 'Siswa berhasil ditambahkan.');
    }

    public function edit(Request $request, Student $student): View
    {
        return view('crud.siswa.input-siswa', [
            'sessionUser' => $request->session()->get('legacy_user'),
            'student' => $student,
            'classes' => Kelas::query()->orderBy('nama')->get(),
            'isEdit' => true,
        ]);
    }

    public function update(Request $request, Student $student): RedirectResponse
    {
        $data = $request->validate([
            'nis' => ['required', 'string', 'max:50', 'unique:siswa,nis,' . $student->ids . ',ids'],
            'nisn' => ['nullable', 'string', 'max:50'],
            'nama' => ['required', 'string', 'max:100'],
            'jk' => ['required', 'in:L,P'],
            'idk' => ['required', 'integer', 'exists:kelas,idk'],
        ]);

        $student->update($data);

        return redirect()->route('students.index')->with('status', 'Siswa berhasil diperbarui.');
    }

    public function destroy(Student $student): RedirectResponse
    {
        $student->delete();

        return redirect()->route('students.index')->with('status', 'Siswa berhasil dihapus.');
    }
}
