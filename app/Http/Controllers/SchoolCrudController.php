<?php

namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SchoolCrudController extends Controller
{
    public function index(Request $request): View
    {
        return view('crud.schools.index', [
            'sessionUser' => $request->session()->get('legacy_user'),
            'schools' => School::query()->orderBy('nama')->paginate(20),
        ]);
    }

    public function create(Request $request): View
    {
        return view('crud.schools.form', [
            'sessionUser' => $request->session()->get('legacy_user'),
            'school' => new School(),
            'isEdit' => false,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'kode' => ['required', 'string', 'max:50'],
            'nama' => ['required', 'string', 'max:100'],
            'alamat' => ['required', 'string'],
        ]);

        School::query()->create($data);

        return redirect()->route('schools.index')->with('status', 'Sekolah berhasil ditambahkan.');
    }

    public function edit(Request $request, School $school): View
    {
        return view('crud.schools.form', [
            'sessionUser' => $request->session()->get('legacy_user'),
            'school' => $school,
            'isEdit' => true,
        ]);
    }

    public function update(Request $request, School $school): RedirectResponse
    {
        $data = $request->validate([
            'kode' => ['required', 'string', 'max:50'],
            'nama' => ['required', 'string', 'max:100'],
            'alamat' => ['required', 'string'],
        ]);

        $school->update($data);

        return redirect()->route('schools.index')->with('status', 'Sekolah berhasil diperbarui.');
    }

    public function destroy(School $school): RedirectResponse
    {
        $school->delete();

        return redirect()->route('schools.index')->with('status', 'Sekolah berhasil dihapus.');
    }
}
