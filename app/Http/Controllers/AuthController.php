<?php

namespace App\Http\Controllers;

use App\Models\AdminUser;
use App\Models\School;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        $school = School::query()->find(2);

        return view('login.index', [
            'schoolName' => $school?->nama ?? 'Sekolah',
        ]);
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $username = trim($credentials['username']);
        $password = $credentials['password'];

        $sessionUser = $this->authenticateAdmin($username, $password)
            ?? $this->authenticateStudent($username, $password)
            ?? $this->authenticateTeacher($username, $password);

        if (! $sessionUser) {
            return back()->withErrors([
                'username' => 'Mohon periksa kembali Username dan Password Anda.',
            ])->onlyInput('username');
        }

        $request->session()->regenerate();
        $request->session()->put('legacy_user', $sessionUser);

        return redirect()->route('dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('legacy_user');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function authenticateAdmin(string $username, string $password): ?array
    {
        $user = AdminUser::query()->where('nama', $username)->first();

        if (! $user || ! $this->isLegacyPasswordValid($password, (string) $user->pass)) {
            return null;
        }

        return [
            'auth_type' => 'admin',
            'identifier' => (string) $user->idu,
            'nama' => (string) $user->nama,
            'level' => 'admin',
            'idk' => null,
            'id' => (int) $user->id,
            'ortu' => null,
        ];
    }

    private function authenticateStudent(string $username, string $password): ?array
    {
        $student = Student::query()
            ->where('nis', $username)
            ->first();

        if (! $student) {
            return null;
        }

        $nisn = preg_replace('/\D+/', '', (string) ($student->nisn ?? ''));
        if ($nisn === '') {
            return null;
        }

        $expectedPassword = strlen($nisn) >= 4 ? substr($nisn, -4) : $nisn;

        if (! hash_equals($expectedPassword, $password)) {
            return null;
        }

        return [
            'auth_type' => 'siswa',
            'identifier' => (string) $student->nis,
            'nama' => (string) $student->nama,
            'level' => 'user',
            'idk' => (int) ($student->idk ?? $student->kelas_id ?? 0),
            'id' => (int) $student->ids,
            'ortu' => $expectedPassword,
        ];
    }

    private function authenticateTeacher(string $username, string $password): ?array
    {
        $teacher = Teacher::query()->where('nip', $username)->first();

        if (! $teacher || ! $this->isLegacyPasswordValid($password, (string) $teacher->pass)) {
            return null;
        }

        return [
            'auth_type' => 'guru',
            'identifier' => (string) $teacher->nip,
            'nama' => (string) $teacher->nama,
            'level' => 'guru',
            'idk' => null,
            'id' => (int) $teacher->idg,
            'ortu' => null,
        ];
    }

    private function isLegacyPasswordValid(string $input, string $stored): bool
    {
        if ($stored === '') {
            return $input === '';
        }

        if ($stored === $input) {
            return true;
        }

        if ($stored === md5($input)) {
            return true;
        }

        if (str_starts_with($stored, '$2y$') || str_starts_with($stored, '$argon2')) {
            return password_verify($input, $stored);
        }

        return false;
    }
}
