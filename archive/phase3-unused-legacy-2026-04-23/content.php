<?php
$module = $_GET['module'] ?? 'home';

// ─────────────────────────────────────────────
// Whitelist of valid modules → file paths
// ─────────────────────────────────────────────
$modules = [
    // Home
    'home'            => 'module/home.php',

    // Siswa
    'siswa'           => 'module/siswa/siswa.php',
    'tampil'          => 'module/siswa/tampil.php',
    'input_siswa'     => 'module/siswa/input_siswa.php',
    'siswa_det'       => 'module/siswa/siswa_det.php',
    'detail_siswa'    => 'module/siswa/detail_siswa.php',

    // Absen & Laporan
    'absen'           => 'module/absen/absen.php',
    'student_attendance' => 'module/student_attendance.php',
    'laporan_absensi_mapel' => 'module/laporan/admin/absensi_mapel.php',
    'rekap_s'         => 'module/laporan/siswa/rekap.php',
    'rekap_g'         => 'module/laporan/guru/rekap.php',

    // User
    'user'            => 'module/user/user.php',
    'input_user'      => 'module/user/input_user.php',

    // Guru
    'guru'            => 'module/guru/guru.php',
    'input_guru'      => 'module/guru/input_guru.php',
    'detail_guru'     => 'module/guru/detail_guru.php',
    'guru_det'        => 'module/guru/guru_det.php',
    'jadwal_mengajar' => 'module/guru/jadwal_mengajar.php',

    // Kelas
    'kelas'           => 'module/kelas/kelas.php',
    'input_kelas'     => 'module/kelas/input_kelas.php',

    // Sekolah
    'sekolah'         => 'module/sekolah/sekolah.php',
    'input_sekolah'   => 'module/sekolah/input_sekolah.php',

    // Mata Pelajaran
    'mata_pelajaran'  => 'module/pelajaran/pelajaran.php',
    'input_pelajaran' => 'module/pelajaran/input_pelajaran.php',

    // Jadwal — all 5 days handled by one file
    'senin'           => 'module/jadwal/senin.php',
    'selasa'          => 'module/jadwal/senin.php',
    'rabu'            => 'module/jadwal/senin.php',
    'kamis'           => 'module/jadwal/senin.php',
    'jumat'           => 'module/jadwal/senin.php',
    'input_jadwal'    => 'module/jadwal/input_jadwal.php',

    // Jadwal Siswa — all 5 days handled by one file
    'siswa_senin'     => 'module/jadwal_siswa/siswa_senin.php',
    'siswa_selasa'    => 'module/jadwal_siswa/siswa_selasa.php',
    'siswa_rabu'      => 'module/jadwal_siswa/siswa_rabu.php',
    'siswa_kamis'     => 'module/jadwal_siswa/siswa_kamis.php',
    'siswa_jumat'     => 'module/jadwal_siswa/siswa_jumat.php',
];

// ─────────────────────────────────────────────
// Route to the correct file or show 404
// ─────────────────────────────────────────────
if (isset($modules[$module])) {
    $file = $modules[$module];
    if (file_exists($file)) {
        include $file;
    } else {
        echo "<div class='alert alert-warning'>Modul <strong>" . htmlspecialchars($module) . "</strong> tidak ditemukan.</div>";
    }
} else {
    echo "<div class='alert alert-danger'>Halaman tidak ditemukan.</div>";
}
?>