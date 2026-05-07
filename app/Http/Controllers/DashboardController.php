<?php

namespace App\Http\Controllers;

use App\Models\AdminUser;
use App\Models\Attendance;
use App\Models\Kelas;
use App\Models\Schedule;
use App\Models\School;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $sessionUser = $request->session()->get('legacy_user', []);
        $role = (string) ($sessionUser['level'] ?? '');

        return view('dashboard.index', [
            'sessionUser' => $sessionUser,
            'role' => $role,
            'stats' => [
                'sekolah' => School::count(),
                'kelas' => Kelas::count(),
                'guru' => Teacher::count(),
                'siswa' => Student::count(),
                'mapel' => Subject::count(),
                'jadwal' => Schedule::count(),
                'absensi' => Attendance::count(),
                'admin' => AdminUser::count(),
            ],
            'dashboard' => $this->buildDashboardData($sessionUser),
        ]);
    }

    public function submitStudentAttendance(Request $request): RedirectResponse
    {
        $sessionUser = $request->session()->get('legacy_user', []);

        abort_unless(($sessionUser['level'] ?? '') === 'user', 403);

        $student = Student::query()
            ->where('nis', (string) ($sessionUser['identifier'] ?? ''))
            ->first();

        if (! $student) {
            return redirect()->route('student-attendance')->with('dashboard_error', 'Data siswa tidak ditemukan.');
        }

        $schedule = $this->getCurrentScheduleForClass((int) $student->idk);
        if (! $schedule) {
            return redirect()->route('student-attendance')->with('dashboard_error', 'Tidak ada jadwal pelajaran aktif saat ini.');
        }

        $today = now()->toDateString();

        $alreadySubmitted = Attendance::query()
            ->where('nis', $student->nis)
            ->where('idm', $schedule->idm)
            ->whereDate('tanggal', $today)
            ->exists();

        if ($alreadySubmitted) {
            return redirect()->route('student-attendance')->with('dashboard_error', 'Anda sudah melakukan absensi untuk pelajaran ini hari ini.');
        }

        $validated = $request->validate([
            'attendance_status' => ['required', 'in:H,I'],
            'location' => ['nullable', 'string', 'max:120'],
            'manual_location' => ['nullable', 'string', 'max:120'],
            'photo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'photo_data' => ['nullable', 'string'],
            'evidence_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf,doc,docx', 'max:5120'],
        ]);

        $status = (string) $validated['attendance_status'];

        if ($status === 'I' && ! $request->hasFile('evidence_file')) {
            return redirect()->route('student-attendance')->with('dashboard_error', 'Untuk status Izin, file bukti wajib diupload.');
        }

        $locationValue = trim((string) ($validated['location'] ?? ''));
        if ($locationValue === '') {
            $locationValue = trim((string) ($validated['manual_location'] ?? ''));
        }

        [$latitude, $longitude] = $this->parseLocation($locationValue);

        if ($status === 'H') {
            $hasLivePhoto = trim((string) ($validated['photo_data'] ?? '')) !== '';
            $hasFallbackPhoto = $request->hasFile('photo');

            if ($locationValue === '') {
                return redirect()->route('student-attendance')->with('dashboard_error', 'Untuk status Hadir, silakan ambil lokasi terlebih dahulu.');
            }

            if (! $hasLivePhoto && ! $hasFallbackPhoto) {
                return redirect()->route('student-attendance')->with('dashboard_error', 'Untuk status Hadir, silakan ambil foto live atau pilih foto fallback terlebih dahulu.');
            }
        }

        $photoPath = null;
        if (trim((string) ($validated['photo_data'] ?? '')) !== '') {
            $photoPath = $this->storeAttendanceBase64Image((string) $validated['photo_data'], 'attendance');
        } elseif ($request->hasFile('evidence_file')) {
            $photoPath = $this->storeAttendanceFile($request->file('evidence_file'), 'evidence');
        } elseif ($request->hasFile('photo')) {
            $photoPath = $this->storeAttendanceFile($request->file('photo'), 'attendance');
        }

        $locationName = null;
        if ($latitude !== null && $longitude !== null) {
            $locationName = $this->reverseGeocodeCoordinates($latitude, $longitude);
        }

        Attendance::query()->create([
            'nis' => $student->nis,
            'idm' => $schedule->idm,
            'tanggal' => $today,
            'status' => $status,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'location_name' => $locationName,
            'photo_path' => $photoPath,
        ]);

        return redirect()->route('student-attendance')->with('status', 'Absensi berhasil dikirim.');
    }

    public function studentAttendance(Request $request): View
    {
        $sessionUser = $request->session()->get('legacy_user', []);

        abort_unless(($sessionUser['level'] ?? '') === 'user', 403);

        $student = Student::query()
            ->where('nis', (string) ($sessionUser['identifier'] ?? ''))
            ->first();

        $attendanceForm = [
            'can_submit' => false,
            'message' => 'Data siswa tidak ditemukan.',
            'subject_name' => null,
            'teacher_name' => null,
            'time_range' => null,
        ];

        $todaySchedules = collect();
        if ($student) {
            $attendanceForm = $this->buildStudentAttendanceForm($student);
            $todaySchedules = $this->getTodaySchedulesForClass((int) $student->idk);
        }

        return view('student-attendance.index', [
            'sessionUser' => $sessionUser,
            'role' => 'user',
            'attendanceForm' => $attendanceForm,
            'todaySchedules' => $todaySchedules,
        ]);
    }

    public function studentScheduleToday(Request $request): View
    {
        $sessionUser = $request->session()->get('legacy_user', []);

        abort_unless(($sessionUser['level'] ?? '') === 'user', 403);

        $student = Student::query()
            ->with('kelas')
            ->where('nis', (string) ($sessionUser['identifier'] ?? ''))
            ->first();

        $todaySchedules = collect();
        if ($student) {
            $todaySchedules = $this->getTodaySchedulesForClass((int) $student->idk);
        }

        return view('student-schedule-today.index', [
            'sessionUser' => $sessionUser,
            'role' => 'user',
            'student' => $student,
            'todaySchedules' => $todaySchedules,
        ]);
    }

    private function buildDashboardData(array $sessionUser): array
    {
        $role = (string) ($sessionUser['level'] ?? '');

        if ($role === 'guru') {
            return $this->buildTeacherDashboard($sessionUser);
        }

        if ($role === 'user') {
            return $this->buildStudentDashboard($sessionUser);
        }

        return $this->buildAdminDashboard();
    }

    private function buildTeacherDashboard(array $sessionUser): array
    {
        $teacher = Teacher::query()
            ->where('nip', (string) ($sessionUser['identifier'] ?? ''))
            ->first();

        if (! $teacher) {
            return $this->emptyDashboard('Data guru tidak ditemukan.');
        }

        $teacherSchedule = Schedule::query()->where('idg', $teacher->idg);
        $scheduleRows = Schedule::query()
            ->with(['day', 'kelas', 'subject'])
            ->where('idg', $teacher->idg)
            ->orderBy('idh')
            ->orderBy('jam_mulai')
            ->get();

        $subjectIds = $teacherSchedule->clone()->distinct()->pluck('idm');
        $classIds = $teacherSchedule->clone()->distinct()->pluck('idk');
        $todaySchedules = $scheduleRows->filter(fn (Schedule $schedule) => (int) $schedule->idh === (int) now()->isoWeekday())->values();
        $currentSchedule = $this->getCurrentScheduleForTeacher((int) $teacher->idg);

        $statusCounts = Attendance::query()
            ->whereIn('idm', $subjectIds)
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $hadir = (int) ($statusCounts['H'] ?? 0);
        $izin = (int) ($statusCounts['I'] ?? 0);
        $alpha = (int) ($statusCounts['A'] ?? 0);
        $totalAbsensi = $hadir + $izin + $alpha;
        $attendanceRate = $totalAbsensi > 0 ? (int) round(($hadir / $totalAbsensi) * 100) : 0;

        $subjectPerformance = Attendance::query()
            ->join('mata_pelajaran', 'mata_pelajaran.idm', '=', 'absensi.idm')
            ->whereIn('absensi.idm', $subjectIds)
            ->select(
                'mata_pelajaran.nama_mp as subject_name',
                DB::raw("SUM(CASE WHEN absensi.status = 'H' THEN 1 ELSE 0 END) as hadir"),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('mata_pelajaran.idm', 'mata_pelajaran.nama_mp')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->map(function ($row): array {
                $hadir = (int) $row->hadir;
                $total = (int) $row->total;

                return [
                    'label' => (string) $row->subject_name,
                    'hadir' => $hadir,
                    'total' => $total,
                    'percent' => $total > 0 ? (int) round(($hadir / $total) * 100) : 0,
                ];
            });

        $recentAttendance = Attendance::query()
            ->with(['student', 'subject'])
            ->whereIn('idm', $subjectIds)
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->limit(6)
            ->get();

        $monthlyAttendance = $this->buildMonthlyTrend(
            Attendance::query()->whereIn('idm', $subjectIds)
        );

        return [
            'headline' => 'Ringkasan Kinerja Mengajar',
            'subhead' => 'Pantau performa absensi siswa per mapel yang Anda ampu.',
            'cards' => [
                [
                    'label' => 'Jadwal Mengajar',
                    'value' => (int) $teacherSchedule->count(),
                    'delta' => 'Total slot aktif & non-aktif',
                    'icon' => 'fa-calendar',
                ],
                [
                    'label' => 'Kelas Diampu',
                    'value' => (int) $classIds->count(),
                    'delta' => 'Kelas unik',
                    'icon' => 'fa-graduation-cap',
                ],
                [
                    'label' => 'Absensi Tercatat',
                    'value' => $totalAbsensi,
                    'delta' => 'Data absensi mapel Anda',
                    'icon' => 'fa-check-square-o',
                ],
                [
                    'label' => 'Kehadiran',
                    'value' => $attendanceRate . '%',
                    'delta' => 'Rata-rata hadir',
                    'icon' => 'fa-bar-chart-o',
                ],
            ],
            'status' => [
                'Hadir' => $hadir,
                'Izin' => $izin,
                'Alpha' => $alpha,
            ],
            'trend' => $monthlyAttendance,
            'performance' => $subjectPerformance,
            'tableTitle' => 'Absensi Terbaru pada Mapel Anda',
            'tableRows' => $recentAttendance,
            'scheduleRows' => $scheduleRows,
            'todaySchedules' => $todaySchedules,
            'currentSchedule' => $currentSchedule,
            'mode' => 'guru',
        ];
    }

    private function buildStudentDashboard(array $sessionUser): array
    {
        $student = Student::query()
            ->where('nis', (string) ($sessionUser['identifier'] ?? ''))
            ->first();

        if (! $student) {
            return $this->emptyDashboard('Data siswa tidak ditemukan.');
        }

        $attendanceQuery = Attendance::query()->where('nis', $student->nis);

        $statusCounts = $attendanceQuery->clone()
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $hadir = (int) ($statusCounts['H'] ?? 0);
        $izin = (int) ($statusCounts['I'] ?? 0);
        $alpha = (int) ($statusCounts['A'] ?? 0);
        $totalAbsensi = $hadir + $izin + $alpha;
        $attendanceRate = $totalAbsensi > 0 ? (int) round(($hadir / $totalAbsensi) * 100) : 0;

        $subjectDistribution = Attendance::query()
            ->join('mata_pelajaran', 'mata_pelajaran.idm', '=', 'absensi.idm')
            ->where('absensi.nis', $student->nis)
            ->select('mata_pelajaran.nama_mp as subject_name', DB::raw('COUNT(*) as total'))
            ->groupBy('mata_pelajaran.idm', 'mata_pelajaran.nama_mp')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $recentAttendance = Attendance::query()
            ->with('subject')
            ->where('nis', $student->nis)
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->limit(6)
            ->get();

        $scheduleCount = Schedule::query()->where('idk', $student->idk)->count();
        $subjectCount = Schedule::query()->where('idk', $student->idk)->distinct('idm')->count('idm');
        $activeSchedule = $this->getCurrentScheduleForClass((int) $student->idk);

        $monthlyAttendance = $this->buildMonthlyTrend(
            Attendance::query()->where('nis', $student->nis)
        );

        return [
            'headline' => 'Ringkasan Kehadiran Pribadi',
            'subhead' => 'Progress absensi Anda berdasarkan data terbaru di database.',
            'cards' => [
                [
                    'label' => 'Total Absensi',
                    'value' => $totalAbsensi,
                    'delta' => 'Riwayat tercatat',
                    'icon' => 'fa-clipboard',
                ],
                [
                    'label' => 'Kehadiran',
                    'value' => $attendanceRate . '%',
                    'delta' => 'Persentase hadir',
                    'icon' => 'fa-check-circle',
                ],
                [
                    'label' => 'Jadwal Kelas',
                    'value' => $scheduleCount,
                    'delta' => 'Total jadwal kelas Anda',
                    'icon' => 'fa-calendar-o',
                ],
                [
                    'label' => 'Mapel Aktif',
                    'value' => $subjectCount,
                    'delta' => 'Mata pelajaran di kelas Anda',
                    'icon' => 'fa-book',
                ],
            ],
            'status' => [
                'Hadir' => $hadir,
                'Izin' => $izin,
                'Alpha' => $alpha,
            ],
            'trend' => $monthlyAttendance,
            'performance' => $subjectDistribution->map(function ($row): array {
                return [
                    'label' => (string) $row->subject_name,
                    'hadir' => (int) $row->total,
                    'total' => (int) $row->total,
                    'percent' => 0,
                ];
            }),
            'tableTitle' => 'Riwayat Absensi Terbaru',
            'tableRows' => $recentAttendance,
            'mode' => 'siswa',
        ];
    }

    private function buildStudentAttendanceForm(Student $student): array
    {
        $activeSchedule = $this->getCurrentScheduleForClass((int) $student->idk);

        $attendanceForm = [
            'can_submit' => false,
            'message' => 'Tidak ada jadwal pelajaran aktif saat ini. Pilih jadwal hari ini di bawah untuk absensi.',
            'subject_name' => null,
            'teacher_name' => null,
            'time_range' => null,
        ];

        if (! $activeSchedule) {
            return $attendanceForm;
        }

        $alreadySubmitted = Attendance::query()
            ->where('nis', $student->nis)
            ->where('idm', $activeSchedule->idm)
            ->whereDate('tanggal', now()->toDateString())
            ->exists();

        return [
            'can_submit' => ! $alreadySubmitted,
            'message' => $alreadySubmitted
                ? 'Anda sudah melakukan absensi untuk pelajaran ini hari ini.'
                : 'Silakan lakukan absensi untuk jadwal yang sedang berlangsung.',
            'subject_name' => $activeSchedule->subject->nama_mp ?? '-',
            'teacher_name' => $activeSchedule->teacher->nama ?? '-',
            'time_range' => substr((string) $activeSchedule->jam_mulai, 0, 5) . ' - ' . substr((string) $activeSchedule->jam_selesai, 0, 5),
        ];
    }

    private function getTodaySchedulesForClass(int $classId): object
    {
        $currentDay = (int) now()->isoWeekday();

        if ($currentDay > 5) {
            return collect();
        }

        return Schedule::query()
            ->with(['day', 'kelas', 'subject', 'teacher'])
            ->where('idk', $classId)
            ->where('idh', $currentDay)
            ->orderBy('jam_mulai')
            ->get();
    }

    private function buildAdminDashboard(): array
    {
        $statusCounts = Attendance::query()
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $hadir = (int) ($statusCounts['H'] ?? 0);
        $izin = (int) ($statusCounts['I'] ?? 0);
        $alpha = (int) ($statusCounts['A'] ?? 0);
        $totalAbsensi = $hadir + $izin + $alpha;
        $attendanceRate = $totalAbsensi > 0 ? (int) round(($hadir / $totalAbsensi) * 100) : 0;

        $monthlyAttendance = $this->buildMonthlyTrend(Attendance::query());

        $subjectPerformance = Attendance::query()
            ->join('mata_pelajaran', 'mata_pelajaran.idm', '=', 'absensi.idm')
            ->select(
                'mata_pelajaran.nama_mp as subject_name',
                DB::raw("SUM(CASE WHEN absensi.status = 'H' THEN 1 ELSE 0 END) as hadir"),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('mata_pelajaran.idm', 'mata_pelajaran.nama_mp')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->map(function ($row): array {
                $hadir = (int) $row->hadir;
                $total = (int) $row->total;

                return [
                    'label' => (string) $row->subject_name,
                    'hadir' => $hadir,
                    'total' => $total,
                    'percent' => $total > 0 ? (int) round(($hadir / $total) * 100) : 0,
                ];
            });

        $recentAttendance = Attendance::query()
            ->with(['student', 'subject'])
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->limit(6)
            ->get();

        return [
            'headline' => 'Ringkasan Operasional Sekolah',
            'subhead' => 'Monitoring keseluruhan aktivitas absensi dan jadwal.',
            'cards' => [
                ['label' => 'Total Siswa', 'value' => Student::count(), 'delta' => 'Siswa aktif', 'icon' => 'fa-users', 'url' => route('students.index')],
                ['label' => 'Total Guru', 'value' => Teacher::count(), 'delta' => 'Guru terdaftar', 'icon' => 'fa-user', 'url' => route('teachers.index')],
                ['label' => 'Total Jadwal', 'value' => Schedule::count(), 'delta' => 'Slot pembelajaran', 'icon' => 'fa-calendar', 'url' => route('schedules.index')],
                ['label' => 'Kehadiran', 'value' => $attendanceRate . '%', 'delta' => 'Rata-rata hadir', 'icon' => 'fa-bar-chart-o', 'url' => route('attendances.index')],
            ],
            'status' => ['Hadir' => $hadir, 'Izin' => $izin, 'Alpha' => $alpha],
            'trend' => $monthlyAttendance,
            'performance' => $subjectPerformance,
            'tableTitle' => 'Absensi Terbaru Seluruh Sekolah',
            'tableRows' => $recentAttendance,
            'mode' => 'admin',
        ];
    }

    private function buildMonthlyTrend($query): array
    {
        $monthMap = collect(range(0, 4))->reverse()->mapWithKeys(function (int $index): array {
            $month = now()->subMonths($index);

            return [$month->format('Y-m') => $month->translatedFormat('M')];
        });

        $rows = $query
            ->whereDate('tanggal', '>=', now()->subMonths(4)->startOfMonth()->toDateString())
            ->selectRaw("DATE_FORMAT(tanggal, '%Y-%m') as ym")
            ->selectRaw("SUM(CASE WHEN status = 'H' THEN 1 ELSE 0 END) as hadir")
            ->selectRaw("SUM(CASE WHEN status = 'I' THEN 1 ELSE 0 END) as izin")
            ->selectRaw("SUM(CASE WHEN status = 'A' THEN 1 ELSE 0 END) as alpha")
            ->groupBy('ym')
            ->orderBy('ym')
            ->get()
            ->keyBy('ym');

        $labels = [];
        $hadir = [];
        $izin = [];
        $alpha = [];

        foreach ($monthMap as $ym => $label) {
            $labels[] = $label;
            $hadir[] = (int) ($rows[$ym]->hadir ?? 0);
            $izin[] = (int) ($rows[$ym]->izin ?? 0);
            $alpha[] = (int) ($rows[$ym]->alpha ?? 0);
        }

        return [
            'labels' => $labels,
            'hadir' => $hadir,
            'izin' => $izin,
            'alpha' => $alpha,
        ];
    }

    private function emptyDashboard(string $message): array
    {
        return [
            'headline' => 'Dashboard',
            'subhead' => $message,
            'cards' => [],
            'status' => ['Hadir' => 0, 'Izin' => 0, 'Alpha' => 0],
            'trend' => ['labels' => [], 'hadir' => [], 'izin' => [], 'alpha' => []],
            'performance' => collect(),
            'tableTitle' => 'Data Terbaru',
            'tableRows' => collect(),
            'attendanceForm' => [
                'can_submit' => false,
                'message' => 'Data absensi tidak tersedia.',
                'subject_name' => null,
                'teacher_name' => null,
                'time_range' => null,
            ],
            'mode' => 'empty',
        ];
    }

    private function getCurrentScheduleForClass(int $classId): ?Schedule
    {
        $currentTime = now()->format('H:i:s');
        $currentDay = (int) now()->isoWeekday();

        if ($currentDay > 5) {
            return null;
        }

        $activeSchedule = Schedule::query()
            ->with(['subject', 'teacher'])
            ->where('idk', $classId)
            ->where('idh', $currentDay)
            ->where('aktif', 1)
            ->where(function ($query) use ($currentTime): void {
                $query->whereRaw(
                    '(jam_mulai <= jam_selesai AND ? BETWEEN jam_mulai AND jam_selesai)',
                    [$currentTime]
                )->orWhereRaw(
                    '(jam_mulai > jam_selesai AND (? >= jam_mulai OR ? <= jam_selesai))',
                    [$currentTime, $currentTime]
                );
            })
            ->orderByDesc('jam_mulai')
            ->first();

        if ($activeSchedule instanceof Schedule) {
            return $activeSchedule;
        }

        return Schedule::query()
            ->with(['subject', 'teacher'])
            ->where('idk', $classId)
            ->where('idh', $currentDay)
            ->where(function ($query) use ($currentTime): void {
                $query->whereRaw(
                    '(jam_mulai <= jam_selesai AND ? BETWEEN jam_mulai AND jam_selesai)',
                    [$currentTime]
                )->orWhereRaw(
                    '(jam_mulai > jam_selesai AND (? >= jam_mulai OR ? <= jam_selesai))',
                    [$currentTime, $currentTime]
                );
            })
            ->orderBy('jam_mulai')
            ->first();
    }

    private function getCurrentScheduleForTeacher(int $teacherId): ?Schedule
    {
        $currentTime = now()->format('H:i:s');
        $currentDay = (int) now()->isoWeekday();

        if ($currentDay > 5) {
            return null;
        }

        return Schedule::query()
            ->with(['day', 'kelas', 'subject'])
            ->where('idg', $teacherId)
            ->where('idh', $currentDay)
            ->where('aktif', 1)
            ->where(function ($query) use ($currentTime): void {
                $query->whereRaw(
                    '(jam_mulai <= jam_selesai AND ? BETWEEN jam_mulai AND jam_selesai)',
                    [$currentTime]
                )->orWhereRaw(
                    '(jam_mulai > jam_selesai AND (? >= jam_mulai OR ? <= jam_selesai))',
                    [$currentTime, $currentTime]
                );
            })
            ->orderBy('jam_mulai')
            ->first();
    }

    private function parseLocation(string $location): array
    {
        if ($location === '') {
            return [null, null];
        }

        $parts = array_map('trim', explode(',', $location));
        if (count($parts) !== 2 || ! is_numeric($parts[0]) || ! is_numeric($parts[1])) {
            return [null, null];
        }

        return [(float) $parts[0], (float) $parts[1]];
    }

    private function storeAttendanceFile(UploadedFile $file, string $prefix): string
    {
        $uploadDir = base_path('../uploads/photos');
        if (! is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'dat');
        $fileName = sprintf('%s_%s_%s.%s', $prefix, now()->format('Ymd_His'), uniqid(), $extension);

        $file->move($uploadDir, $fileName);

        return 'uploads/photos/' . $fileName;
    }

    private function storeAttendanceBase64Image(string $photoData, string $prefix): string
    {
        if (! preg_match('/^data:image\/(png|jpe?g|webp);base64,/', $photoData, $matches)) {
            return '';
        }

        $extension = strtolower($matches[1]) === 'jpeg' ? 'jpg' : strtolower($matches[1]);
        $binary = base64_decode(substr($photoData, strpos($photoData, ',') + 1), true);

        if ($binary === false) {
            return '';
        }

        $uploadDir = base_path('../uploads/photos');
        if (! is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileName = sprintf('%s_%s_%s.%s', $prefix, now()->format('Ymd_His'), uniqid(), $extension);
        $targetPath = $uploadDir . DIRECTORY_SEPARATOR . $fileName;

        file_put_contents($targetPath, $binary);

        return 'uploads/photos/' . $fileName;
    }

    private function reverseGeocodeCoordinates(float $latitude, float $longitude): ?string
    {
        try {
            $response = Http::timeout(3)->get('https://nominatim.openstreetmap.org/reverse', [
                'format' => 'jsonv2',
                'lat' => $latitude,
                'lon' => $longitude,
                'zoom' => 18,
                'addressdetails' => 1,
            ]);

            if (! $response->successful()) {
                return null;
            }

            $data = $response->json();
            if (isset($data['display_name'])) {
                return $data['display_name'];
            }

            $address = $data['address'] ?? [];
            $parts = array_filter([
                $address['road'] ?? null,
                $address['suburb'] ?? null,
                $address['village'] ?? $address['town'] ?? $address['city'] ?? null,
                $address['state'] ?? null,
            ]);

            return ! empty($parts) ? implode(', ', $parts) : null;
        } catch (\Exception $e) {
            return null;
        }
    }
}
