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
use Illuminate\View\View;

class ModuleController extends Controller
{
    public function show(Request $request, string $module): View|RedirectResponse
    {
        if ($module === 'laporan_absensi_mapel') {
            return redirect()->route('reports.attendance-by-subject');
        }

        if ($module === 'rekap_s') {
            return redirect()->route('reports.student-recap');
        }

        if ($module === 'rekap_g') {
            return redirect()->route('reports.teacher-recap');
        }

        $title = $this->moduleTitle($module);
        $columns = [];
        $rows = collect();

        switch ($module) {
            case 'home':
                $columns = ['Info'];
                $rows = collect([['Modul home sudah dimigrasikan ke dashboard Laravel.']]);
                break;
            case 'siswa':
            case 'tampil':
            case 'input_siswa':
            case 'siswa_det':
            case 'detail_siswa':
                $columns = ['NIS', 'Nama', 'JK', 'Kelas'];
                $rows = Student::query()
                    ->with('kelas')
                    ->orderBy('nama')
                    ->get()
                    ->map(fn (Student $s) => [
                        $s->nis,
                        $s->nama,
                        $s->jk,
                        $s->kelas?->nama ?? '-',
                    ]);
                break;
            case 'guru':
            case 'input_guru':
            case 'detail_guru':
            case 'guru_det':
            case 'jadwal_mengajar':
                $columns = ['NIP', 'Nama', 'JK', 'Alamat'];
                $rows = Teacher::query()->orderBy('nama')->get()->map(fn (Teacher $t) => [
                    $t->nip,
                    $t->nama,
                    $t->jk,
                    $t->alamat,
                ]);
                break;
            case 'kelas':
            case 'input_kelas':
                $columns = ['Kelas', 'Sekolah'];
                $rows = Kelas::query()->with('school')->orderBy('nama')->get()->map(fn (Kelas $k) => [
                    $k->nama,
                    $k->school?->nama ?? '-',
                ]);
                break;
            case 'mata_pelajaran':
            case 'input_pelajaran':
                $columns = ['Kode', 'Mata Pelajaran'];
                $rows = Subject::query()->orderBy('nama_mp')->get()->map(fn (Subject $m) => [
                    $m->idm,
                    $m->nama_mp,
                ]);
                break;
            case 'sekolah':
            case 'input_sekolah':
                $columns = ['Kode', 'Nama', 'Alamat'];
                $rows = School::query()->orderBy('nama')->get()->map(fn (School $s) => [
                    $s->kode,
                    $s->nama,
                    $s->alamat,
                ]);
                break;
            case 'absen':
            case 'student_attendance':
                $columns = ['Tanggal', 'NIS', 'Siswa', 'Mapel', 'Status'];
                $rows = Attendance::query()
                    ->with(['student', 'subject'])
                    ->orderByDesc('tanggal')
                    ->limit(100)
                    ->get()
                    ->map(fn (Attendance $a) => [
                        optional($a->tanggal)->format('Y-m-d') ?? '-',
                        $a->nis,
                        $a->student?->nama ?? '-',
                        $a->subject?->nama_mp ?? '-',
                        $a->status,
                    ]);
                break;
            case 'user':
            case 'input_user':
                $columns = ['Username', 'Level', 'Sekolah ID'];
                $rows = AdminUser::query()->orderBy('nama')->get()->map(fn (AdminUser $u) => [
                    $u->nama,
                    $u->level,
                    $u->id,
                ]);
                break;
            default:
                $columns = ['Hari', 'Guru', 'Kelas', 'Mapel', 'Jam', 'Aktif'];
                $rows = Schedule::query()
                    ->with(['day', 'teacher', 'kelas', 'subject'])
                    ->orderBy('idh')
                    ->orderBy('jam_mulai')
                    ->get()
                    ->map(fn (Schedule $j) => [
                        $j->day?->hari ?? '-',
                        $j->teacher?->nama ?? '-',
                        $j->kelas?->nama ?? '-',
                        $j->subject?->nama_mp ?? '-',
                        trim(($j->jam_mulai ?? '-') . ' - ' . ($j->jam_selesai ?? '-')),
                        ((int) $j->aktif) === 1 ? 'Ya' : 'Tidak',
                    ]);
                break;
        }

        return view('module.index', [
            'sessionUser' => $request->session()->get('legacy_user'),
            'module' => $module,
            'title' => $title,
            'columns' => $columns,
            'rows' => $rows,
        ]);
    }

    private function moduleTitle(string $module): string
    {
        return match ($module) {
            'home' => 'Beranda',
            'siswa', 'tampil', 'input_siswa', 'siswa_det', 'detail_siswa' => 'Manajemen Siswa',
            'absen', 'student_attendance' => 'Absensi dan Laporan',
            'user', 'input_user' => 'Manajemen Admin',
            'guru', 'input_guru', 'detail_guru', 'guru_det', 'jadwal_mengajar' => 'Manajemen Guru',
            'kelas', 'input_kelas' => 'Manajemen Kelas',
            'sekolah', 'input_sekolah' => 'Profil Sekolah',
            'mata_pelajaran', 'input_pelajaran' => 'Mata Pelajaran',
            default => 'Jadwal',
        };
    }
}
