<?php

namespace Tests\Feature;

use App\Models\AdminUser;
use App\Models\Kelas;
use App\Models\School;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_login_redirects_to_dashboard_and_sets_admin_session(): void
    {
        $school = School::query()->create([
            'kode' => '001',
            'nama' => 'SMK Test',
            'alamat' => 'Alamat Test',
        ]);

        AdminUser::query()->create([
            'nama' => 'Admin Test',
            'pass' => md5('secret123'),
            'level' => 'admin',
            'id' => $school->id,
        ]);

        $response = $this->post(route('login.submit'), [
            'username' => 'Admin Test',
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertSame('admin', session('legacy_user.level'));
        $this->assertSame('admin', session('legacy_user.auth_type'));
        $this->assertSame((string) $school->id, session('legacy_user.identifier'));
        $this->assertSame($school->id, session('legacy_user.id'));
    }

    public function test_student_login_uses_nis_and_last_four_digits_of_nisn(): void
    {
        $school = School::query()->create([
            'kode' => '001',
            'nama' => 'SMK Test',
            'alamat' => 'Alamat Test',
        ]);

        $class = Kelas::query()->create([
            'id' => $school->id,
            'nama' => 'X IPA 1',
        ]);

        $student = Student::query()->create([
            'nis' => '1001',
            'nisn' => '1234567890',
            'nama' => 'Siswa Test',
            'jk' => 'L',
            'idk' => $class->idk,
        ]);

        $response = $this->post(route('login.submit'), [
            'username' => '1001',
            'password' => '7890',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertSame('user', session('legacy_user.level'));
        $this->assertSame('siswa', session('legacy_user.auth_type'));
        $this->assertSame('1001', session('legacy_user.identifier'));
        $this->assertSame($class->idk, session('legacy_user.idk'));
        $this->assertSame($student->ids, session('legacy_user.id'));
    }

    public function test_teacher_login_redirects_to_dashboard_and_sets_guru_session(): void
    {
        $teacher = Teacher::query()->create([
            'nip' => '1987654321',
            'nama' => 'Guru Test',
            'jk' => 'L',
            'alamat' => 'Alamat Test',
            'pass' => md5('teach123'),
        ]);

        $response = $this->post(route('login.submit'), [
            'username' => '1987654321',
            'password' => 'teach123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertSame('guru', session('legacy_user.level'));
        $this->assertSame('guru', session('legacy_user.auth_type'));
        $this->assertSame('1987654321', session('legacy_user.identifier'));
        $this->assertSame($teacher->idg, session('legacy_user.id'));
    }
}