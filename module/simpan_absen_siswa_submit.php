<?php
session_start();
require_once '../config/conn.php';
require_once '../config/fungsi.php';

header('Content-Type: text/plain; charset=UTF-8');

if (!isset($_SESSION['level']) || $_SESSION['level'] !== 'user') {
    http_response_code(403);
    echo 'Unauthorized';
    exit;
}

$student_id = $_SESSION['idu'];
$class_id = $_SESSION['idk'];

$stmt = $pdo->prepare("SELECT ids FROM siswa WHERE nis = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$student) {
    echo 'Student not found';
    exit;
}

$current_date = date('Y-m-d');
$current_time = date('H:i:s');
$current_day = date('N');

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
    echo 'Tidak ada jadwal aktif untuk waktu ini. Periksa ulang jam pelajaran.';
    exit;
}

$stmt_check = $pdo->prepare("SELECT id FROM absensi WHERE nis = ? AND idm = ? AND tanggal = ?");
$stmt_check->execute([$student_id, $schedule['idm'], $current_date]);
if ($stmt_check->fetch()) {
    echo 'Already attended for this class today.';
    exit;
}

$attendance_status = strtoupper(trim($_POST['attendance_status'] ?? 'H'));
if (!in_array($attendance_status, ['H', 'I'], true)) {
    $attendance_status = 'H';
}

$photo_path = null;
$photo_data = trim($_POST['photo_data'] ?? '');

if ($photo_data !== '') {
    if (!preg_match('/^data:image\/(png|jpe?g|webp);base64,/', $photo_data, $matches)) {
        echo 'Format foto tidak valid.';
        exit;
    }

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
} elseif (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
    $upload_dir = '../uploads/photos/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $file_name = uniqid() . '_' . basename($_FILES['photo']['name']);
    $target_file = $upload_dir . $file_name;

    if (!move_uploaded_file($_FILES['photo']['tmp_name'], $target_file)) {
        echo 'Failed to upload photo.';
        exit;
    }

    $photo_path = 'uploads/photos/' . $file_name;
}

if (isset($_FILES['evidence_file']) && $_FILES['evidence_file']['error'] === 0) {
    $upload_dir = '../uploads/photos/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $originalName = $_FILES['evidence_file']['name'];
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'pdf', 'doc', 'docx'];

    if (!in_array($extension, $allowedExtensions, true)) {
        echo 'Format file bukti tidak didukung.';
        exit;
    }

    $file_name = 'evidence_' . date('Ymd_His') . '_' . uniqid() . '.' . $extension;
    $target_file = $upload_dir . $file_name;

    if (!move_uploaded_file($_FILES['evidence_file']['tmp_name'], $target_file)) {
        echo 'Gagal upload file bukti.';
        exit;
    }

    $photo_path = 'uploads/photos/' . $file_name;
}

$latitude = null;
$longitude = null;
if (!empty($_POST['location'])) {
    $locationParts = array_map('trim', explode(',', $_POST['location']));
    if (count($locationParts) === 2 && is_numeric($locationParts[0]) && is_numeric($locationParts[1])) {
        $latitude = $locationParts[0];
        $longitude = $locationParts[1];
    }
}

if ($attendance_status === 'I' && $photo_path === null) {
    echo 'Untuk status Izin, file bukti wajib diupload.';
    exit;
}

$stmt_insert = $pdo->prepare("INSERT INTO absensi (nis, idm, tanggal, status, latitude, longitude, photo_path) VALUES (?, ?, ?, ?, ?, ?, ?)");
if ($stmt_insert->execute([$student_id, $schedule['idm'], $current_date, $attendance_status, $latitude, $longitude, $photo_path])) {
    echo 'Absensi berhasil dikirim!';
} else {
    echo 'Failed to save attendance.';
}
