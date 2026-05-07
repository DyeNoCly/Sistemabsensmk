# Sistem Absensi SMK

Sistem Absensi SMK adalah aplikasi web berbasis Laravel untuk mengelola absensi sekolah dengan dukungan peran **Admin**, **Guru**, dan **Siswa**.

## Fitur Utama

- Autentikasi multi-peran:
  - Admin (berdasarkan tabel admin legacy)
  - Guru (berdasarkan NIP)
  - Siswa (berdasarkan NIS, password 4 digit terakhir NISN)
- Dashboard sesuai peran pengguna
- Manajemen data master:
  - Sekolah
  - Kelas
  - Guru
  - Siswa
  - Mata pelajaran
  - Jadwal
  - Admin user
- Manajemen absensi:
  - Input absensi manual
  - Roster absensi per jadwal
  - Filter absensi
  - Export CSV
- Absensi mandiri siswa:
  - Status hadir/izin
  - Dukungan upload foto/file bukti
  - Penyimpanan koordinat dan nama lokasi
- Laporan:
  - Rekap absensi per mata pelajaran (admin)
  - Rekap siswa (siswa)
  - Rekap guru + export PDF (guru)

## Teknologi

- PHP 8.2+
- Laravel 12
- MySQL/SQLite (konfigurasi default `.env.example` menggunakan SQLite)
- Vite (frontend build)

## Struktur Singkat

- `app/Http/Controllers` → logika autentikasi, dashboard, CRUD, dan laporan
- `app/Models` → model data legacy (siswa, guru, jadwal, absensi, dll.)
- `routes/web.php` → definisi seluruh route web aplikasi
- `resources/views` → Blade template untuk dashboard, form, dan laporan
- `database/migrations` → skema database
- `tests/Feature` → pengujian fitur utama (termasuk login)

## Cara Menjalankan (Local Development)

1. Clone repository ini.
2. Masuk ke direktori project:
   ```bash
   cd /home/runner/work/Sistemabsensmk/Sistemabsensmk
   ```
3. Jalankan setup otomatis:
   ```bash
   composer setup
   ```

Perintah `composer setup` akan:
- install dependency PHP
- menyalin `.env` dari `.env.example` (jika belum ada)
- generate app key
- migrate database
- install dependency frontend
- build asset frontend

Untuk mode development:

```bash
composer dev
```

Mode ini menjalankan server Laravel, queue listener, log tailing, dan Vite secara bersamaan.

## Menjalankan Test

```bash
composer test
```

## Catatan Login

- **Admin**: username mengikuti kolom `nama` pada data admin.
- **Guru**: username menggunakan `nip`, password mengikuti data `pass`.
- **Siswa**: username menggunakan `nis`, password adalah 4 digit terakhir dari `nisn`.

> Pastikan data user sudah tersedia di database agar proses login berhasil.

## Catatan Tambahan

- Timezone aplikasi diatur ke `Asia/Jakarta`.
- Akses route dibatasi middleware otorisasi berbasis peran (`legacy.auth` dan `legacy.role`).
- Endpoint root (`/`) diarahkan ke `/dashboard`.
