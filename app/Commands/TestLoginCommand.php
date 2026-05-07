<?php

namespace App\Commands;

use App\Models\Student;
use Illuminate\Console\Command;

class TestLoginCommand extends Command
{
    protected $signature = 'test:login {--class=1 : Class ID to test}';

    protected $description = 'Test student login for a specific class';

    public function handle(): void
    {
        $classId = $this->option('class');
        
        $this->info("=== DEBUGGING STUDENT LOGIN ===\n");

        // Check total students
        $totalStudents = Student::count();
        $this->info("Total students in database: $totalStudents\n");

        // Check students in specified class
        $this->info("=== STUDENTS IN CLASS ID $classId ===");
        $students = Student::where('kelas_id', $classId)->get();
        $this->info("Count: " . $students->count());
        
        foreach ($students as $student) {
            $this->line("ID: {$student->id} | NIS: {$student->nis} | NISN: {$student->nisn} | Nama: {$student->nama}");
        }

        if ($students->count() > 0) {
            $this->info("\n=== TEST LOGIN FOR FIRST STUDENT ===");
            $student = $students->first();
            $this->line("Student: {$student->nama}");
            $this->line("NIS: {$student->nis}");
            $this->line("NISN: {$student->nisn}");
            
            // Test with NIS
            $testNIS = Student::where('nis', $student->nis)->first();
            $this->line("Login with NIS: " . ($testNIS ? "SUCCESS" : "FAILED"));
            
            // Test with NISN
            $testNISN = Student::where('nisn', $student->nisn)->first();
            $this->line("Login with NISN: " . ($testNISN ? "SUCCESS" : "FAILED"));
            
            // Test password logic
            $fallbackPassword = (string) ($student->nisn ?? $student->nis ?? '');
            $this->line("Password would be: $fallbackPassword");
        }

        // Check for students with empty credentials
        $this->info("\n=== STUDENTS WITH EMPTY CREDENTIALS ===");
        $emptyNIS = Student::whereNull('nis')->orWhere('nis', '')->count();
        $emptyNISN = Student::whereNull('nisn')->orWhere('nisn', '')->count();
        $this->line("Students with empty NIS: $emptyNIS");
        $this->line("Students with empty NISN: $emptyNISN");
    }
}
