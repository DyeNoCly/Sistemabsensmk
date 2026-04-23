<?php
declare(strict_types=1);

/**
 * Seeder dummy jadwal untuk semua kelas.
 * Membuat 1 jadwal aktif per kelas dari jam sekarang sampai 23:59.
 *
 * Usage: php seed_dummy_jadwal.php
 */

require_once __DIR__ . '/config/conn.php';

try {
    date_default_timezone_set('Asia/Jakarta');

    $currentDay = (int) date('N'); // 1=Monday ... 7=Sunday
    $startTime = date('H:i:s');
    $endTime = '23:59:00';

    if ($currentDay > 5) {
        throw new RuntimeException('Hari ini weekend. Seeder ini hanya untuk Senin-Jumat agar sesuai tabel hari.');
    }

    echo "Menambahkan jadwal aktif untuk hari ke-$currentDay, mulai $startTime sampai $endTime" . PHP_EOL;

    $classes = $pdo->query('SELECT idk, nama FROM kelas ORDER BY idk ASC')->fetchAll(PDO::FETCH_ASSOC);
    $subjects = $pdo->query('SELECT idm, nama_mp FROM mata_pelajaran ORDER BY idm ASC')->fetchAll(PDO::FETCH_ASSOC);
    $teachers = $pdo->query('SELECT idg, nama FROM guru ORDER BY idg ASC')->fetchAll(PDO::FETCH_ASSOC);

    if (count($classes) === 0) {
        throw new RuntimeException('Tidak ada data kelas.');
    }

    if (count($subjects) === 0 || count($teachers) === 0) {
        throw new RuntimeException('Tidak ada data mata pelajaran atau guru.');
    }

    $insertStmt = $pdo->prepare('
        INSERT INTO jadwal (idh, idg, idk, idm, jam_mulai, jam_selesai, aktif)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ');

    $inserted = 0;

    foreach ($classes as $idx => $class) {
        $subject = $subjects[$idx % count($subjects)];
        $teacher = $teachers[$idx % count($teachers)];

        // Hanya nonaktifkan jadwal aktif yang bentrok di kelas & hari yang sama.
        $deactivateStmt = $pdo->prepare('UPDATE jadwal SET aktif = 0 WHERE idh = ? AND idk = ? AND aktif = 1');
        $deactivateStmt->execute([$currentDay, (int) $class['idk']]);

        $insertStmt->execute([
            $currentDay,
            (int) $teacher['idg'],
            (int) $class['idk'],
            (int) $subject['idm'],
            $startTime,
            $endTime,
            1,
        ]);

        $inserted++;

        echo sprintf(
            "[OK] %s -> %s (%s) oleh %s (%s) %s-%s",
            $class['nama'],
            $subject['nama_mp'],
            $subject['idm'],
            $teacher['nama'],
            $teacher['idg'],
            $startTime,
            $endTime
        ) . PHP_EOL;
    }

    echo PHP_EOL . "Selesai. Berhasil menambahkan $inserted jadwal aktif untuk semua kelas." . PHP_EOL;

} catch (Throwable $e) {
    fwrite(STDERR, 'Seeder gagal: ' . $e->getMessage() . PHP_EOL);
    exit(1);
}
