<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Kelas;
use App\Models\Schedule;
use App\Models\Subject;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function attendanceBySubject(Request $request): View
    {
        $this->requireRole($request, ['admin']);

        $subjects = Subject::query()->orderBy('nama_mp')->get();
        $classes = Kelas::query()->orderBy('nama')->get();
        $selectedSubject = $request->integer('idm');
        $selectedClass = $request->integer('idk');
        $startDate = $request->string('start_date')->toString();
        $endDate = $request->string('end_date')->toString();

        $rows = collect();
        $summary = ['H' => 0, 'I' => 0, 'A' => 0];

        if ($selectedSubject > 0) {
            $query = Attendance::query()
                ->with(['student.kelas', 'subject'])
                ->where('idm', $selectedSubject);

            if ($selectedClass > 0) {
                $query->whereHas('student', fn ($studentQuery) => $studentQuery->where('idk', $selectedClass));
            }

            if ($startDate !== '') {
                $query->whereDate('tanggal', '>=', $startDate);
            }

            if ($endDate !== '') {
                $query->whereDate('tanggal', '<=', $endDate);
            }

            $rows = $query->orderByDesc('tanggal')->orderBy('nis')->get();

            foreach ($rows as $row) {
                if (isset($summary[$row->status])) {
                    $summary[$row->status]++;
                }
            }
        }

        return view('reports.attendance-by-subject', [
            'sessionUser' => $request->session()->get('legacy_user'),
            'subjects' => $subjects,
            'classes' => $classes,
            'selectedSubject' => $selectedSubject,
            'selectedClass' => $selectedClass,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'rows' => $rows,
            'summary' => $summary,
        ]);
    }

    public function studentRecap(Request $request): View
    {
        $user = $this->requireRole($request, ['user']);
        $student = $this->currentStudent($request);

        $schedule = Schedule::query()->with(['day', 'subject'])
            ->where('idk', $student->idk)
            ->orderBy('idh')
            ->orderBy('jam_mulai')
            ->get();

        $dates = Attendance::query()
            ->where('nis', $student->nis)
            ->distinct()
            ->orderBy('tanggal')
            ->pluck('tanggal');

        $attendances = Attendance::query()
            ->where('nis', $student->nis)
            ->get()
            ->groupBy(fn (Attendance $attendance) => $attendance->idm);

        return view('reports.student-recap', [
            'sessionUser' => $user,
            'student' => $student,
            'schedule' => $schedule,
            'dates' => $dates,
            'attendances' => $attendances,
        ]);
    }

    public function teacherRecap(Request $request): View
    {
        $user = $this->requireRole($request, ['guru']);
        $teacher = $this->currentTeacher($request);

        $schedules = Schedule::query()->with(['day', 'kelas', 'subject'])
            ->where('idg', $teacher->idg)
            ->orderBy('idh')
            ->orderBy('jam_mulai')
            ->get();

        return view('reports.teacher-recap', [
            'sessionUser' => $user,
            'teacher' => $teacher,
            'schedules' => $schedules,
        ]);
    }

    public function teacherRecapPdf(Request $request): Response
    {
        $user = $this->requireRole($request, ['guru']);
        $teacher = $this->currentTeacher($request);

        $schedules = Schedule::query()->with(['day', 'kelas', 'subject'])
            ->where('idg', $teacher->idg)
            ->orderBy('idh')
            ->orderBy('jam_mulai')
            ->get();

        require_once base_path('config/html2pdf/html2pdf.class.php');

        $html = view('reports.teacher-recap-pdf', [
            'teacher' => $teacher,
            'schedules' => $schedules,
        ])->render();

        $pdf = new \HTML2PDF('P', 'A4', 'en', true);
        $pdf->setDefaultFont('Arial');
        $pdf->writeHTML($html);

        $content = $pdf->Output('rekap_guru.pdf', 'S');

        return response($content, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="rekap_guru.pdf"',
        ]);
    }

    private function requireRole(Request $request, array $roles): array
    {
        $user = $request->session()->get('legacy_user');

        abort_unless(is_array($user) && in_array($user['level'] ?? '', $roles, true), 403);

        return $user;
    }

    private function currentStudent(Request $request)
    {
        $sessionUser = $request->session()->get('legacy_user');
        $student = \App\Models\Student::query()->where('nis', $sessionUser['identifier'] ?? '')->first();

        abort_unless((bool) $student, 404);

        return $student;
    }

    private function currentTeacher(Request $request)
    {
        $sessionUser = $request->session()->get('legacy_user');
        $teacher = \App\Models\Teacher::query()->where('nip', $sessionUser['identifier'] ?? '')->first();

        abort_unless((bool) $teacher, 404);

        return $teacher;
    }
}
