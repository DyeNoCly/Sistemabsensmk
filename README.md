## Website Sistem Informasi Absensi Siswa SMP ##

Nama dan file database : sisabsi.sql
<br><br>Website ini memakai php 5.6 dan mysql

## Seeder Dummy Data Absensi ##

Untuk generate data dummy absensi (status H/I/A, lokasi, dan photo path):

```bash
php seed_dummy_absensi.php
```

Opsi:

```bash
php seed_dummy_absensi.php --days=14 --per-day=14 --replace
```

- `--days` = jumlah hari ke belakang yang digenerate
- `--per-day` = maksimal jumlah absen per tanggal
- `--replace` = hapus data absensi pada range tanggal tersebut lalu generate ulang
