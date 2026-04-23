<?php
declare(strict_types=1);

/**
 * Seeder dummy data absensi.
 *
 * Usage (PowerShell/cmd):
 *   php seed_dummy_absensi.php
 *   php seed_dummy_absensi.php --days=14 --per-day=10 --replace
 */

require_once __DIR__ . '/config/conn.php';

$days = 10;
$maxRecordsPerDay = 12;
$replaceForDateRange = false;

foreach ($argv ?? [] as $arg) {
    if (str_starts_with($arg, '--days=')) {
        $days = max(1, (int) substr($arg, 7));
    }

    if (str_starts_with($arg, '--per-day=')) {
        $maxRecordsPerDay = max(1, (int) substr($arg, 10));
    }

    if ($arg === '--replace') {
        $replaceForDateRange = true;
    }
}

try {
    $pdo->beginTransaction();

    $students = $pdo->query('SELECT nis FROM siswa ORDER BY ids ASC')->fetchAll(PDO::FETCH_COLUMN);
    $subjects = $pdo->query('SELECT idm FROM mata_pelajaran ORDER BY idm ASC')->fetchAll(PDO::FETCH_COLUMN);

    if (count($students) === 0 || count($subjects) === 0) {
        throw new RuntimeException('Data siswa atau mata pelajaran kosong.');
    }

    $dates = [];
    for ($i = 0; $i < $days; $i++) {
        $dates[] = date('Y-m-d', strtotime("-{$i} days"));
    }

    if ($replaceForDateRange) {
        $startDate = end($dates);
        $endDate = reset($dates);

        $deleteStmt = $pdo->prepare('DELETE FROM absensi WHERE tanggal BETWEEN ? AND ?');
        $deleteStmt->execute([$startDate, $endDate]);

        echo "Deleted existing absensi in range {$startDate} to {$endDate}\n";
    }

    $checkStmt = $pdo->prepare('SELECT id FROM absensi WHERE nis = ? AND idm = ? AND tanggal = ? LIMIT 1');
    $insertStmt = $pdo->prepare(
        'INSERT INTO absensi (nis, idm, tanggal, status, latitude, longitude, photo_path) VALUES (?, ?, ?, ?, ?, ?, ?)'
    );

    $inserted = 0;
    $skipped = 0;

    foreach ($dates as $date) {
        shuffle($students);
        $take = min($maxRecordsPerDay, count($students));

        for ($i = 0; $i < $take; $i++) {
            $nis = (string) $students[$i];
            $idm = (int) $subjects[array_rand($subjects)];

            $checkStmt->execute([$nis, $idm, $date]);
            if ($checkStmt->fetch()) {
                $skipped++;
                continue;
            }

            $roll = random_int(1, 100);
            if ($roll <= 70) {
                $status = 'H';
            } elseif ($roll <= 90) {
                $status = 'I';
            } else {
                $status = 'A';
            }

            $latitude = null;
            $longitude = null;
            if ($status !== 'A' && random_int(1, 100) <= 75) {
                $latitude = number_format(-6.25 + (random_int(-1500, 1500) / 10000), 7, '.', '');
                $longitude = number_format(106.65 + (random_int(-2000, 2000) / 10000), 7, '.', '');
            }

            $photoPath = null;
            if ($status === 'I') {
                $photoPath = 'uploads/photos/evidence_dummy_' . date('Ymd', strtotime($date)) . '_' . $nis . '.jpg';
            } elseif ($status === 'H' && random_int(1, 100) <= 35) {
                $photoPath = 'uploads/photos/attendance_dummy_' . date('Ymd', strtotime($date)) . '_' . $nis . '.jpg';
            }

            $insertStmt->execute([$nis, $idm, $date, $status, $latitude, $longitude, $photoPath]);
            $inserted++;
        }
    }

    $pdo->commit();

    echo "Dummy absensi berhasil dibuat.\n";
    echo "Inserted: {$inserted}\n";
    echo "Skipped (duplicate): {$skipped}\n";
    echo "Range dates: " . end($dates) . ' s/d ' . reset($dates) . "\n";
    echo "Tips: jalankan lagi dengan --replace untuk regenerate data pada range tanggal yang sama.\n";
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    fwrite(STDERR, 'Seeder gagal: ' . $e->getMessage() . PHP_EOL);
    exit(1);
}
