<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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

        return view('crud.students.index', [
            'sessionUser' => $request->session()->get('legacy_user'),
            'students' => $studentQuery->paginate(20)->appends($request->query()),
            'classes' => Kelas::query()->orderBy('nama')->get(),
            'selectedClassId' => $selectedClassId,
        ]);
    }

    public function create(Request $request): View
    {
        return view('crud.students.form', [
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
            'nama' => ['required', 'string', 'max:100'],
            'jk' => ['required', 'in:L,P'],
            'alamat' => ['required', 'string'],
            'idk' => ['required', 'integer', 'exists:kelas,idk'],
            'tlp' => ['nullable', 'string', 'max:20'],
            'bapak' => ['nullable', 'string', 'max:50'],
            'k_bapak' => ['nullable', 'string', 'max:50'],
            'ibu' => ['nullable', 'string', 'max:50'],
            'k_ibu' => ['nullable', 'string', 'max:50'],
            'password' => ['required', 'string', 'min:4'],
        ]);

        $data['pass'] = Hash::make($data['password']);
        unset($data['password']);

        Student::query()->create($data);

        return redirect()->route('students.index')->with('status', 'Siswa berhasil ditambahkan.');
    }

    public function edit(Request $request, Student $student): View
    {
        return view('crud.students.form', [
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
            'nama' => ['required', 'string', 'max:100'],
            'jk' => ['required', 'in:L,P'],
            'alamat' => ['required', 'string'],
            'idk' => ['required', 'integer', 'exists:kelas,idk'],
            'tlp' => ['nullable', 'string', 'max:20'],
            'bapak' => ['nullable', 'string', 'max:50'],
            'k_bapak' => ['nullable', 'string', 'max:50'],
            'ibu' => ['nullable', 'string', 'max:50'],
            'k_ibu' => ['nullable', 'string', 'max:50'],
            'password' => ['nullable', 'string', 'min:4'],
        ]);

        if (! empty($data['password'])) {
            $data['pass'] = Hash::make($data['password']);
        }
        unset($data['password']);

        $student->update($data);

        return redirect()->route('students.index')->with('status', 'Siswa berhasil diperbarui.');
    }

    public function destroy(Student $student): RedirectResponse
    {
        $student->delete();

        return redirect()->route('students.index')->with('status', 'Siswa berhasil dihapus.');
    }
}
