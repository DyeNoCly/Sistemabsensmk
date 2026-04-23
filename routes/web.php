<?php

use App\Http\Controllers\AdminUserCrudController;
use App\Http\Controllers\AttendanceCrudController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClassCrudController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ScheduleCrudController;
use App\Http\Controllers\SchoolCrudController;
use App\Http\Controllers\StudentCrudController;
use App\Http\Controllers\SubjectCrudController;
use App\Http\Controllers\TeacherCrudController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('legacy.auth')
    ->name('logout');

Route::middleware('legacy.auth')->group(function (): void {
    Route::get('/proof-file/{path}', function (string $path) {
        $relativePath = ltrim($path, '/');

        if (str_contains($relativePath, '..')) {
            abort(404);
        }

        $storageRoot = realpath(base_path('../uploads/photos'));
        $fullPath = realpath(base_path('../' . $relativePath));

        if (! $storageRoot || ! $fullPath || ! str_starts_with($fullPath, $storageRoot) || ! is_file($fullPath)) {
            abort(404);
        }

        return response()->file($fullPath);
    })->where('path', '.*')->name('proof.file')->middleware('legacy.role:admin,guru');

    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/student-attendance', [DashboardController::class, 'studentAttendance'])
        ->name('student-attendance')
        ->middleware('legacy.role:user');
    Route::get('/student-schedule-today', [DashboardController::class, 'studentScheduleToday'])
        ->name('student-schedule-today')
        ->middleware('legacy.role:user');
    Route::post('/student-attendance', [DashboardController::class, 'submitStudentAttendance'])
        ->name('student-attendance.submit')
        ->middleware('legacy.role:user');
    Route::post('/dashboard/student-attendance', [DashboardController::class, 'submitStudentAttendance'])
        ->name('dashboard.student-attendance.submit')
        ->middleware('legacy.role:user');
    Route::resource('students', StudentCrudController::class)->except(['show'])->middleware('legacy.role:admin,guru');
    Route::resource('teachers', TeacherCrudController::class)->except(['show'])->middleware('legacy.role:admin');
    Route::resource('classes', ClassCrudController::class)->except(['show'])->middleware('legacy.role:admin');
    Route::resource('subjects', SubjectCrudController::class)->except(['show'])->middleware('legacy.role:admin,guru');
    Route::resource('schools', SchoolCrudController::class)->except(['show'])->middleware('legacy.role:admin');
    Route::resource('admin-users', AdminUserCrudController::class)->except(['show'])->middleware('legacy.role:admin');
    Route::resource('schedules', ScheduleCrudController::class)->except(['show'])->middleware('legacy.role:admin,guru');
    Route::resource('attendances', AttendanceCrudController::class)->except(['show'])->middleware('legacy.role:admin,guru');
    Route::get('attendances/roster', [AttendanceCrudController::class, 'roster'])->name('attendances.roster')->middleware('legacy.role:admin,guru');
    Route::post('attendances/roster', [AttendanceCrudController::class, 'rosterUpdate'])->name('attendances.roster.update')->middleware('legacy.role:admin,guru');
    Route::get('attendances/export', [AttendanceCrudController::class, 'export'])->name('attendances.export')->middleware('legacy.role:admin,guru');
    Route::get('reports/attendance-by-subject', [ReportController::class, 'attendanceBySubject'])->name('reports.attendance-by-subject')->middleware('legacy.role:admin');
    Route::get('reports/student-recap', [ReportController::class, 'studentRecap'])->name('reports.student-recap')->middleware('legacy.role:user');
    Route::get('reports/teacher-recap', [ReportController::class, 'teacherRecap'])->name('reports.teacher-recap')->middleware('legacy.role:guru');
    Route::get('reports/teacher-recap/pdf', [ReportController::class, 'teacherRecapPdf'])->name('reports.teacher-recap.pdf')->middleware('legacy.role:guru');
    Route::get('/module/{module}', [ModuleController::class, 'show'])->name('module.show');
});
