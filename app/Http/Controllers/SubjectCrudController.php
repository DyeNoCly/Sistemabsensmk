<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubjectCrudController extends Controller
{
    public function index(Request $request): View
    {
        return view('crud.mata-pelajaran.data-matpel', [
            'sessionUser' => $request->session()->get('legacy_user'),
            'subjects' => Subject::query()->orderBy('nama_mp')->paginate(20),
        ]);
    }

    public function create(Request $request): View
    {
        return view('crud.mata-pelajaran.input-matpel', [
            'sessionUser' => $request->session()->get('legacy_user'),
            'subject' => new Subject(),
            'isEdit' => false,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama_mp' => ['required', 'string', 'max:100'],
        ]);

        Subject::query()->create($data);

        return redirect()->route('subjects.index')->with('status', 'Mata pelajaran berhasil ditambahkan.');
    }

    public function edit(Request $request, Subject $subject): View
    {
        return view('crud.mata-pelajaran.input-matpel', [
            'sessionUser' => $request->session()->get('legacy_user'),
            'subject' => $subject,
            'isEdit' => true,
        ]);
    }

    public function update(Request $request, Subject $subject): RedirectResponse
    {
        $data = $request->validate([
            'nama_mp' => ['required', 'string', 'max:100'],
        ]);

        $subject->update($data);

        return redirect()->route('subjects.index')->with('status', 'Mata pelajaran berhasil diperbarui.');
    }

    public function destroy(Subject $subject): RedirectResponse
    {
        $subject->delete();

        return redirect()->route('subjects.index')->with('status', 'Mata pelajaran berhasil dihapus.');
    }
}
