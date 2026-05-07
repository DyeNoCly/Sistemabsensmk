<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\School;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClassCrudController extends Controller
{
    public function index(Request $request): View
    {
        return view('crud.classes.data-kelas', [
            'sessionUser' => $request->session()->get('legacy_user'),
            'classes' => Kelas::query()->with('school')->orderBy('nama')->paginate(20),
        ]);
    }

    public function create(Request $request): View
    {
        $defaultSchoolId = $this->defaultSchoolId();

        return view('crud.classes.input-kelas', [
            'sessionUser' => $request->session()->get('legacy_user'),
            'classRecord' => new Kelas(['id' => $defaultSchoolId]),
            'isEdit' => false,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:50'],
        ]);

        $data['id'] = $this->defaultSchoolId();

        Kelas::query()->create($data);

        return redirect()->route('classes.index')->with('status', 'Kelas berhasil ditambahkan.');
    }

    public function edit(Request $request, Kelas $class): View
    {
        return view('crud.classes.input-kelas', [
            'sessionUser' => $request->session()->get('legacy_user'),
            'classRecord' => $class,
            'isEdit' => true,
        ]);
    }

    public function update(Request $request, Kelas $class): RedirectResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:50'],
        ]);

        $class->update($data);

        return redirect()->route('classes.index')->with('status', 'Kelas berhasil diperbarui.');
    }

    public function destroy(Kelas $class): RedirectResponse
    {
        $class->delete();

        return redirect()->route('classes.index')->with('status', 'Kelas berhasil dihapus.');
    }

    private function defaultSchoolId(): int
    {
        return (int) (School::query()->orderBy('id')->value('id') ?? 0);
    }
}
