<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasColumn('siswa', 'nisn')) {
            Schema::table('siswa', function (Blueprint $table): void {
                $table->string('nisn', 50)->nullable()->after('nis');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('siswa', 'nisn')) {
            Schema::table('siswa', function (Blueprint $table): void {
                $table->dropColumn('nisn');
            });
        }
    }
};