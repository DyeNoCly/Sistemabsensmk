<?php

namespace App\Http\Controllers;

use App\Models\Day;
use App\Models\Kelas;
use App\Models\Schedule;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScheduleCrudController extends Controller
{
    public function index(Request $request): View
    {
        $query = Schedule::query()->with(['day', 'teacher', 'kelas', 'subject']);

        if ($request->filled('day')) {
            $query->where('idh', $request->integer('day'));
        }

        if ($request->filled('teacher')) {
            $query->where('idg', $request->integer('teacher'));
        }

        if ($request->filled('class')) {
            $query->where('idk', $request->integer('class'));
        }

        if ($request->filled('subject')) {
            $query->where('idm', $request->integer('subject'));
        }

        if ($request->filled('aktif')) {
            $query->where('aktif', $request->integer('aktif'));
        }

        return view('crud.jadwal.data-jadwal', [
            'sessionUser' => $request->session()->get('legacy_user'),
            'days' => Day::query()->orderBy('idh')->get(),
            'teachers' => Teacher::query()->orderBy('nama')->get(),
            'classes' => Kelas::query()->orderBy('nama')->get(),
            'subjects' => Subject::query()->orderBy('nama_mp')->get(),
            'filters' => $request->only(['day', 'teacher', 'class', 'subject', 'aktif']),
            'schedules' => $query->orderBy('idh')->orderBy('jam_mulai')->paginate(20)->appends($request->query()),
        ]);
    }

    public function create(Request $request): View
    {
        return view('crud.jadwal.input-jadwal', [
            'sessionUser' => $request->session()->get('legacy_user'),
            'schedule' => new Schedule(),
            'days' => Day::query()->orderBy('idh')->get(),
            'teachers' => Teacher::query()->orderBy('nama')->get(),
            'classes' => Kelas::query()->orderBy('nama')->get(),
            'subjects' => Subject::query()->orderBy('nama_mp')->get(),
            'isEdit' => false,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'idh' => ['required', 'integer', 'exists:hari,idh'],
            'idg' => ['required', 'integer', 'exists:guru,idg'],
            'idk' => ['required', 'integer', 'exists:kelas,idk'],
            'idm' => ['required', 'integer', 'exists:mata_pelajaran,idm'],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
            'aktif' => ['required', 'integer', 'in:0,1'],
        ]);

        Schedule::query()->create($data);

        return redirect()->route('schedules.index')->with('status', 'Jadwal berhasil ditambahkan.');
    }

    public function edit(Request $request, Schedule $schedule): View
    {
        return view('crud.jadwal.input-jadwal', [
            'sessionUser' => $request->session()->get('legacy_user'),
            'schedule' => $schedule,
            'days' => Day::query()->orderBy('idh')->get(),
            'teachers' => Teacher::query()->orderBy('nama')->get(),
            'classes' => Kelas::query()->orderBy('nama')->get(),
            'subjects' => Subject::query()->orderBy('nama_mp')->get(),
            'isEdit' => true,
        ]);
    }

    public function update(Request $request, Schedule $schedule): RedirectResponse
    {
        $data = $request->validate([
            'idh' => ['required', 'integer', 'exists:hari,idh'],
            'idg' => ['required', 'integer', 'exists:guru,idg'],
            'idk' => ['required', 'integer', 'exists:kelas,idk'],
            'idm' => ['required', 'integer', 'exists:mata_pelajaran,idm'],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
            'aktif' => ['required', 'integer', 'in:0,1'],
        ]);

        $schedule->update($data);

        return redirect()->route('schedules.index')->with('status', 'Jadwal berhasil diperbarui.');
    }

    public function destroy(Schedule $schedule): RedirectResponse
    {
        $schedule->delete();

        return redirect()->route('schedules.index')->with('status', 'Jadwal berhasil dihapus.');
    }
}
