<?php
session_start();
require_once '../config/conn.php';
require_once '../config/fungsi.php';

header('Content-Type: text/plain; charset=UTF-8');

// Check if student is logged in
if (!isset($_SESSION['level']) || $_SESSION['level'] !== 'user') {
    http_response_code(403);
    echo 'Unauthorized';
    exit;
}

$student_id = $_SESSION['idu']; // NIS
$class_id = $_SESSION['idk'];

// Get student ids from nis
$stmt = $pdo->prepare("SELECT ids FROM siswa WHERE nis = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$student) {
    echo 'Student not found';
    exit;
}
$ids = $student['ids'];

$current_date = date('Y-m-d');
$current_time = date('H:i:s');
$current_day = date('N');

// Find current schedule
$stmt = $pdo->prepare("
    SELECT j.idj, j.idm
    FROM jadwal j
    WHERE j.idk = ? AND j.idh = ?
    AND (
        (j.jam_mulai <= j.jam_selesai AND ? BETWEEN j.jam_mulai AND j.jam_selesai)
        OR (j.jam_mulai > j.jam_selesai AND (? >= j.jam_mulai OR ? <= j.jam_selesai))
    )
    LIMIT 1
");
$stmt->execute([$class_id, $current_day, $current_time, $current_time, $current_time]);
$schedule = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$schedule) {
    echo 'Tidak ada jadwal aktif untuk waktu ini. Periksa ulang jam pelajaran dan status aktif jadwal.';
    exit;
}

$idj = $schedule['idj'];

// Check if already attended
$stmt_check = $pdo->prepare("SELECT id FROM absensi WHERE nis = ? AND idm = ? AND tanggal = ?");
$stmt_check->execute([$student_id, $schedule['idm'], $current_date]);
if ($stmt_check->fetch()) {
    echo 'Already attended for this class today.';
    exit;
}

function absensiColumnExists(PDO $pdo, string $columnName): bool
{
    static $cache = [];

    if (!array_key_exists($columnName, $cache)) {
        $stmt = $pdo->prepare("SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'absensi' AND COLUMN_NAME = ? LIMIT 1");
        $stmt->execute([$columnName]);
        $cache[$columnName] = (bool) $stmt->fetchColumn();
    }

    return $cache[$columnName];
}

// Handle photo upload from live camera or traditional file upload
$photo_path = null;
$photo_data = trim($_POST['photo_data'] ?? '');

if ($photo_data !== '') {
    if (preg_match('/^data:image\/(png|jpe?g|webp);base64,/', $photo_data, $matches)) {
        $extension = strtolower($matches[1]) === 'jpeg' ? 'jpg' : strtolower($matches[1]);
        $base64 = substr($photo_data, strpos($photo_data, ',') + 1);
        $binary = base64_decode($base64, true);

        if ($binary === false) {
            echo 'Invalid photo data.';
            exit;
        }

        $upload_dir = '../uploads/photos/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $file_name = 'attendance_' . date('Ymd_His') . '_' . uniqid() . '.' . $extension;
        $target_file = $upload_dir . $file_name;

        if (file_put_contents($target_file, $binary) === false) {
            echo 'Failed to save photo.';
            exit;
        }

        $photo_path = 'uploads/photos/' . $file_name;
    } else {
        echo 'Format foto tidak valid.';
        exit;
    }
} elseif (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
    $upload_dir = '../uploads/photos/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    $file_name = uniqid() . '_' . basename($_FILES['photo']['name']);
    $target_file = $upload_dir . $file_name;
    if (move_uploaded_file($_FILES['photo']['tmp_name'], $target_file)) {
        $photo_path = 'uploads/photos/' . $file_name;
    } else {
        echo 'Failed to upload photo.';
        exit;
    }
}

// Handle location
$latitude = null;
$longitude = null;
if (isset($_POST['location']) && !empty($_POST['location'])) {
    $locationParts = array_map('trim', explode(',', $_POST['location']));
    if (count($locationParts) === 2 && is_numeric($locationParts[0]) && is_numeric($locationParts[1])) {
        $latitude = $locationParts[0];
        $longitude = $locationParts[1];
    }
}

// Insert attendance, using extra columns only when the database supports them
$insertColumns = ['nis', 'idm', 'tanggal', 'status'];
$insertValues  = [$student_id, $schedule['idm'], $current_date, 'H'];

if ($photo_path !== null && absensiColumnExists($pdo, 'photo_path')) {
    $insertColumns[] = 'photo_path';
    $insertValues[] = $photo_path;
}

if ($latitude !== null && absensiColumnExists($pdo, 'latitude')) {
    $insertColumns[] = 'latitude';
    $insertValues[] = $latitude;
}

if ($longitude !== null && absensiColumnExists($pdo, 'longitude')) {
    $insertColumns[] = 'longitude';
    $insertValues[] = $longitude;
}

$placeholders = implode(', ', array_fill(0, count($insertColumns), '?'));
$sql = 'INSERT INTO absensi (' . implode(', ', $insertColumns) . ') VALUES (' . $placeholders . ')';
$stmt_insert = $pdo->prepare($sql);

if ($stmt_insert->execute($insertValues)) {
    echo 'Absensi berhasil dikirim!';
} else {
    echo 'Failed to save attendance.';
}
?>