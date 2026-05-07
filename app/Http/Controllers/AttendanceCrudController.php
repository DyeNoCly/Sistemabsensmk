<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Kelas;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceCrudController extends Controller
{
    public function index(Request $request): View
    {
        $query = Attendance::query()->with(['student.kelas', 'subject']);

        if ($request->filled('tanggal_from')) {
            $query->whereDate('tanggal', '>=', $request->date('tanggal_from'));
        }

        if ($request->filled('tanggal_to')) {
            $query->whereDate('tanggal', '<=', $request->date('tanggal_to'));
        }

        if ($request->filled('kelas')) {
            $query->whereHas('student', fn ($studentQuery) => $studentQuery->where('idk', $request->integer('kelas')));
        }

        if ($request->filled('student')) {
            $query->where('nis', $request->string('student'));
        }

        if ($request->filled('subject')) {
            $query->where('idm', $request->integer('subject'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        return view('crud.absensi.data-absensi', [
            'sessionUser' => $request->session()->get('legacy_user'),
            'classes' => Kelas::query()->orderBy('nama')->get(),
            'students' => Student::query()->orderBy('nama')->get(),
            'subjects' => Subject::query()->orderBy('nama_mp')->get(),
            'filters' => $request->only(['tanggal_from', 'tanggal_to', 'kelas', 'student', 'subject', 'status']),
            'attendances' => $query->orderByDesc('tanggal')->paginate(20)->appends($request->query()),
        ]);
    }

    public function roster(Request $request): View
    {
        $scheduleId = $request->integer('idj');
        $date = $request->filled('tanggal') ? $request->date('tanggal')->toDateString() : now()->toDateString();

        $schedule = Schedule::query()
            ->with(['day', 'kelas', 'subject', 'teacher'])
            ->find($scheduleId);

        abort_unless((bool) $schedule, 404);
        $this->assertCanManageSchedule($request, $schedule);

        $students = Student::query()
            ->where('idk', $schedule->idk)
            ->orderBy('nama')
            ->get();

        $attendanceMap = Attendance::query()
            ->where('idm', $schedule->idm)
            ->whereDate('tanggal', $date)
            ->pluck('status', 'nis');

        return view('crud.absensi.roster', [
            'sessionUser' => $request->session()->get('legacy_user'),
            'schedule' => $schedule,
            'students' => $students,
            'attendanceMap' => $attendanceMap,
            'date' => $date,
        ]);
    }

    public function rosterUpdate(Request $request)
    {
        $data = $request->validate([
            'idj' => ['required', 'integer', 'exists:jadwal,idj'],
            'nis' => ['required', 'string', 'exists:siswa,nis'],
            'status' => ['required', 'in:H,I,A'],
            'tanggal' => ['required', 'date'],
        ]);

        $schedule = Schedule::query()->findOrFail($data['idj']);
    $this->assertCanManageSchedule($request, $schedule);

        $attendance = Attendance::query()->where('nis', $data['nis'])->where('idm', $schedule->idm)->whereDate('tanggal', $data['tanggal'])->first();

        if ($attendance) {
            $attendance->update([
                'status' => $data['status'],
            ]);
        } else {
            Attendance::query()->create([
                'nis' => $data['nis'],
                'idm' => $schedule->idm,
                'tanggal' => $data['tanggal'],
                'status' => $data['status'],
            ]);
        }

        if ($request->boolean('ajax')) {
            return response()->noContent();
        }

        return back()->with('status', 'Data absensi berhasil disimpan.');
    }

    public function export(Request $request)
    {
        $query = Attendance::query()->with(['student.kelas', 'subject']);

        if ($request->filled('tanggal_from')) {
            $query->whereDate('tanggal', '>=', $request->date('tanggal_from'));
        }

        if ($request->filled('tanggal_to')) {
            $query->whereDate('tanggal', '<=', $request->date('tanggal_to'));
        }

        if ($request->filled('kelas')) {
            $query->whereHas('student', fn ($studentQuery) => $studentQuery->where('idk', $request->integer('kelas')));
        }

        if ($request->filled('student')) {
            $query->where('nis', $request->string('student'));
        }

        if ($request->filled('subject')) {
            $query->where('idm', $request->integer('subject'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $rows = $query->orderByDesc('tanggal')->get();

        return response()->streamDownload(function () use ($rows): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Tanggal', 'NIS', 'Siswa', 'Kelas', 'Mapel', 'Status', 'Lokasi', 'Photo']);

            foreach ($rows as $row) {
                $location = $row->location_name ?? '';
                if (!$location && $row->latitude && $row->longitude) {
                    $location = $row->latitude . ', ' . $row->longitude;
                }

                fputcsv($handle, [
                    optional($row->tanggal)->format('Y-m-d'),
                    $row->nis,
                    $row->student?->nama,
                    $row->student?->kelas?->nama,
                    $row->subject?->nama_mp,
                    $row->status,
                    $location,
                    $row->photo_path,
                ]);
            }

            fclose($handle);
        }, 'absensi.csv', ['Content-Type' => 'text/csv']);
    }

    public function create(Request $request): View
    {
        $selectedClass = $request->integer('idk');
        $selectedSubject = $request->integer('idm');

        $students = Student::query()
            ->when($selectedClass > 0, fn ($query) => $query->where('idk', $selectedClass))
            ->orderBy('nama')
            ->get();

        $attendance = new Attendance([
            'nis' => (string) $request->string('nis'),
            'idm' => $selectedSubject > 0 ? $selectedSubject : null,
            'tanggal' => $request->filled('tanggal') ? $request->date('tanggal') : now()->toDateString(),
            'status' => $request->string('status')->toString() ?: 'H',
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude'),
            'photo_path' => $request->string('photo_path')->toString(),
        ]);

        return view('crud.absensi.input-absensi', [
            'sessionUser' => $request->session()->get('legacy_user'),
            'attendance' => $attendance,
            'students' => $students,
            'subjects' => Subject::query()->orderBy('nama_mp')->get(),
            'isEdit' => false,
            'selectedClass' => $selectedClass,
            'selectedSubject' => $selectedSubject,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nis' => ['required', 'string', 'exists:siswa,nis'],
            'idm' => ['required', 'integer', 'exists:mata_pelajaran,idm'],
            'tanggal' => ['required', 'date'],
            'status' => ['required', 'in:H,I,A'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'photo_path' => ['nullable', 'string', 'max:255'],
        ]);

        $this->assertCanManageAttendance($request, $data['nis'], (int) $data['idm']);

        Attendance::query()->create($data);

        return redirect()->route('attendances.index')->with('status', 'Absensi berhasil ditambahkan.');
    }

    public function edit(Request $request, Attendance $attendance): View
    {
        return view('crud.absensi.input-absensi', [
            'sessionUser' => $request->session()->get('legacy_user'),
            'attendance' => $attendance,
            'students' => Student::query()->orderBy('nama')->get(),
            'subjects' => Subject::query()->orderBy('nama_mp')->get(),
            'isEdit' => true,
        ]);
    }

    public function update(Request $request, Attendance $attendance): RedirectResponse
    {
        $data = $request->validate([
            'nis' => ['required', 'string', 'exists:siswa,nis'],
            'idm' => ['required', 'integer', 'exists:mata_pelajaran,idm'],
            'tanggal' => ['required', 'date'],
            'status' => ['required', 'in:H,I,A'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'photo_path' => ['nullable', 'string', 'max:255'],
        ]);

        $this->assertCanManageAttendance($request, $data['nis'], (int) $data['idm']);

        $attendance->update($data);

        return redirect()->route('attendances.index')->with('status', 'Absensi berhasil diperbarui.');
    }

    public function destroy(Request $request, Attendance $attendance): RedirectResponse
    {
        $this->assertCanManageAttendance($request, $attendance->nis, (int) $attendance->idm);

        $attendance->delete();

        return redirect()->route('attendances.index')->with('status', 'Absensi berhasil dihapus.');
    }

    private function assertCanManageSchedule(Request $request, Schedule $schedule): void
    {
        $user = $request->session()->get('legacy_user');

        abort_unless(is_array($user), 403);

        if (($user['level'] ?? '') === 'admin') {
            return;
        }

        abort_unless(($user['level'] ?? '') === 'guru', 403);

        $teacher = Teacher::query()->where('nip', (string) ($user['identifier'] ?? ''))->first();
        abort_unless((bool) $teacher, 403);
        abort_unless((int) $schedule->idg === (int) $teacher->idg, 403, 'Anda tidak memiliki akses ke jadwal ini.');
    }

    private function assertCanManageAttendance(Request $request, string $nis, int $idm): void
    {
        $user = $request->session()->get('legacy_user');

        abort_unless(is_array($user), 403);

        if (($user['level'] ?? '') === 'admin') {
            return;
        }

        abort_unless(($user['level'] ?? '') === 'guru', 403);

        $teacher = Teacher::query()->where('nip', (string) ($user['identifier'] ?? ''))->first();
        abort_unless((bool) $teacher, 403);

        $student = Student::query()->where('nis', $nis)->first();
        abort_unless((bool) $student, 404);

        $allowed = Schedule::query()
            ->where('idg', $teacher->idg)
            ->where('idk', $student->idk)
            ->where('idm', $idm)
            ->exists();

        abort_unless($allowed, 403, 'Anda tidak memiliki akses untuk mengubah absensi ini.');
    }
}
