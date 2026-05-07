<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('sekolah')) {
            Schema::create('sekolah', function (Blueprint $table): void {
                $table->increments('id');
                $table->string('kode', 50);
                $table->string('nama', 100);
                $table->text('alamat');
            });
        }

        if (! Schema::hasTable('kelas')) {
            Schema::create('kelas', function (Blueprint $table): void {
                $table->increments('idk');
                $table->integer('id');
                $table->string('nama', 50);
            });
        }

        if (! Schema::hasTable('hari')) {
            Schema::create('hari', function (Blueprint $table): void {
                $table->increments('idh');
                $table->string('hari', 15);
            });
        }

        if (! Schema::hasTable('guru')) {
            Schema::create('guru', function (Blueprint $table): void {
                $table->increments('idg');
                $table->string('nip', 50);
                $table->string('nama', 100);
                $table->string('jk', 3);
                $table->text('alamat');
                $table->text('pass');
            });
        }

        if (! Schema::hasTable('siswa')) {
            Schema::create('siswa', function (Blueprint $table): void {
                $table->increments('ids');
                $table->string('nis', 50);
                $table->string('nama', 100);
                $table->string('jk', 2);
                $table->integer('idk');
            });
        }

        if (! Schema::hasTable('mata_pelajaran')) {
            Schema::create('mata_pelajaran', function (Blueprint $table): void {
                $table->increments('idm');
                $table->string('nama_mp', 100);
            });
        }

        if (! Schema::hasTable('jadwal')) {
            Schema::create('jadwal', function (Blueprint $table): void {
                $table->increments('idj');
                $table->integer('idh');
                $table->integer('idg');
                $table->integer('idk');
                $table->integer('idm');
                $table->time('jam_mulai');
                $table->time('jam_selesai');
                $table->integer('aktif');
            });
        }

        if (! Schema::hasTable('absensi')) {
            Schema::create('absensi', function (Blueprint $table): void {
                $table->increments('id');
                $table->string('nis', 20)->nullable();
                $table->integer('idm')->nullable();
                $table->date('tanggal')->nullable();
                $table->enum('status', ['H', 'I', 'A'])->nullable();
                $table->decimal('latitude', 10, 7)->nullable();
                $table->decimal('longitude', 10, 7)->nullable();
                $table->string('photo_path')->nullable();
            });
        }

        if (! Schema::hasTable('user')) {
            Schema::create('user', function (Blueprint $table): void {
                $table->increments('idu');
                $table->string('nama', 100);
                $table->text('pass');
                $table->string('level', 50);
                $table->integer('id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('user');
        Schema::dropIfExists('absensi');
        Schema::dropIfExists('jadwal');
        Schema::dropIfExists('mata_pelajaran');
        Schema::dropIfExists('siswa');
        Schema::dropIfExists('guru');
        Schema::dropIfExists('hari');
        Schema::dropIfExists('kelas');
        Schema::dropIfExists('sekolah');
    }
};
