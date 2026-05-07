<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class TeacherCrudController extends Controller
{
    public function index(Request $request): View
    {
        return view('crud.guru.data-guru', [
            'sessionUser' => $request->session()->get('legacy_user'),
            'teachers' => Teacher::query()->orderBy('nama')->paginate(20),
        ]);
    }

    public function create(Request $request): View
    {
        return view('crud.guru.input-data', [
            'sessionUser' => $request->session()->get('legacy_user'),
            'teacher' => new Teacher(),
            'isEdit' => false,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nip' => ['required', 'string', 'max:50', 'unique:guru,nip'],
            'nama' => ['required', 'string', 'max:100'],
            'jk' => ['required', 'in:L,P'],
            'alamat' => ['required', 'string'],
            'password' => ['required', 'string', 'min:4'],
        ]);

        $data['pass'] = Hash::make($data['password']);
        unset($data['password']);

        Teacher::query()->create($data);

        return redirect()->route('teachers.index')->with('status', 'Guru berhasil ditambahkan.');
    }

    public function edit(Request $request, Teacher $teacher): View
    {
        return view('crud.guru.input-data', [
            'sessionUser' => $request->session()->get('legacy_user'),
            'teacher' => $teacher,
            'isEdit' => true,
        ]);
    }

    public function update(Request $request, Teacher $teacher): RedirectResponse
    {
        $data = $request->validate([
            'nip' => ['required', 'string', 'max:50', 'unique:guru,nip,' . $teacher->idg . ',idg'],
            'nama' => ['required', 'string', 'max:100'],
            'jk' => ['required', 'in:L,P'],
            'alamat' => ['required', 'string'],
            'password' => ['nullable', 'string', 'min:4'],
        ]);

        if (! empty($data['password'])) {
            $data['pass'] = Hash::make($data['password']);
        }
        unset($data['password']);

        $teacher->update($data);

        return redirect()->route('teachers.index')->with('status', 'Guru berhasil diperbarui.');
    }

    public function destroy(Teacher $teacher): RedirectResponse
    {
        $teacher->delete();

        return redirect()->route('teachers.index')->with('status', 'Guru berhasil dihapus.');
    }
}
