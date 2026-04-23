<?php
require_once "../config/conn.php";

$act = $_GET['act'] ?? '';

// ─────────────────────────────────────────────
// Helper: redirect with alert
// ─────────────────────────────────────────────
function alertRedirect(string $message, string $url): void {
    $msg = addslashes($message);
    echo "<script>window.alert('$msg'); window.location=('$url');</script>";
    exit;
}

// ─────────────────────────────────────────────
// Helper: hash password
// ─────────────────────────────────────────────
function hashPassword(string $pass): string {
    return password_hash($pass, PASSWORD_DEFAULT);
}

// ═══════════════════════════════════════════════════════
// USER
// ═══════════════════════════════════════════════════════

if ($act === 'input_user') {
    $nama    = trim($_POST['nama'] ?? '');
    $pass    = $_POST['pass'] ?? '';
    $sekolah = trim($_POST['sekolah'] ?? '');

    if (!$nama || !$pass || !$sekolah) {
        alertRedirect('Semua field wajib diisi.', '../media.php?module=user');
    }

    $stmt = $pdo->prepare("INSERT INTO user (nama, pass, level, id) VALUES (?, ?, 'admin_guru', ?)");
    $stmt->execute([$nama, hashPassword($pass), $sekolah]);
    alertRedirect('Data Tersimpan', '../media.php?module=user');
}

if ($act === 'edit_user') {
    $nama    = trim($_POST['nama'] ?? '');
    $pass    = $_POST['pass'] ?? '';
    $sekolah = trim($_POST['sekolah'] ?? '');
    $idu     = trim($_POST['idu'] ?? '');

    if (!empty($pass)) {
        $stmt = $pdo->prepare("UPDATE user SET nama = ?, pass = ?, id = ? WHERE idu = ?");
        $stmt->execute([$nama, hashPassword($pass), $sekolah, $idu]);
    } else {
        $stmt = $pdo->prepare("UPDATE user SET nama = ?, id = ? WHERE idu = ?");
        $stmt->execute([$nama, $sekolah, $idu]);
    }
    alertRedirect('Data Tersimpan', '../media.php?module=user');
}

if ($act === 'hapus_user') {
    $idu = trim($_GET['idu'] ?? '');
    if (!$idu) alertRedirect('ID tidak valid.', '../media.php?module=user');

    $stmt = $pdo->prepare("DELETE FROM user WHERE idu = ?");
    $stmt->execute([$idu]);
    alertRedirect('Data Terhapus', '../media.php?module=user');
}

// ═══════════════════════════════════════════════════════
// SISWA
// ═══════════════════════════════════════════════════════

if ($act === 'input_siswa') {
    $pass = $_POST['k_password'] ?? '';

    $stmt = $pdo->prepare("
        INSERT INTO siswa (nis, nama, jk, alamat, idk, tlp, bapak, k_bapak, ibu, k_ibu, pass)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        trim($_POST['nis']      ?? ''),
        trim($_POST['nama']     ?? ''),
        trim($_POST['jk']       ?? ''),
        trim($_POST['alamat']   ?? ''),
        trim($_POST['kelas']    ?? ''),
        trim($_POST['tlp']      ?? ''),
        trim($_POST['bapak']    ?? ''),
        trim($_POST['k_bapak']  ?? ''),
        trim($_POST['ibu']      ?? ''),
        trim($_POST['k_ibu']    ?? ''),
        hashPassword($pass),
    ]);
    alertRedirect('Data Tersimpan', '../media.php?module=siswa&kls=semua');
}

if ($act === 'edit_siswa') {
    $id   = trim($_POST['id'] ?? '');
    $pass = $_POST['k_password'] ?? '';

    if (!$id) alertRedirect('ID tidak valid.', '../media.php?module=siswa&kls=semua');

    if (!empty($pass)) {
        $stmt = $pdo->prepare("
            UPDATE siswa SET nis=?, nama=?, jk=?, alamat=?, idk=?, tlp=?, bapak=?, k_bapak=?, ibu=?, k_ibu=?, pass=?
            WHERE ids=?
        ");
        $stmt->execute([
            trim($_POST['nis']      ?? ''),
            trim($_POST['nama']     ?? ''),
            trim($_POST['jk']       ?? ''),
            trim($_POST['alamat']   ?? ''),
            trim($_POST['kelas']    ?? ''),
            trim($_POST['tlp']      ?? ''),
            trim($_POST['bapak']    ?? ''),
            trim($_POST['k_bapak']  ?? ''),
            trim($_POST['ibu']      ?? ''),
            trim($_POST['k_ibu']    ?? ''),
            hashPassword($pass),
            $id,
        ]);
    } else {
        $stmt = $pdo->prepare("
            UPDATE siswa SET nis=?, nama=?, jk=?, alamat=?, idk=?, tlp=?, bapak=?, k_bapak=?, ibu=?, k_ibu=?
            WHERE ids=?
        ");
        $stmt->execute([
            trim($_POST['nis']      ?? ''),
            trim($_POST['nama']     ?? ''),
            trim($_POST['jk']       ?? ''),
            trim($_POST['alamat']   ?? ''),
            trim($_POST['kelas']    ?? ''),
            trim($_POST['tlp']      ?? ''),
            trim($_POST['bapak']    ?? ''),
            trim($_POST['k_bapak']  ?? ''),
            trim($_POST['ibu']      ?? ''),
            trim($_POST['k_ibu']    ?? ''),
            $id,
        ]);
    }
    alertRedirect('Data Tersimpan', '../media.php?module=siswa&kls=semua');
}

if ($act === 'siswa_det') {
    $pass = $_POST['pass'] ?? '';
    $id   = trim($_POST['id'] ?? '');

    if (empty($pass)) {
        alertRedirect('Isi Password', '../media.php?module=siswa_det');
    }

    $stmt = $pdo->prepare("UPDATE siswa SET pass = ? WHERE ids = ?");
    $stmt->execute([hashPassword($pass), $id]);
    alertRedirect('Data Tersimpan', '../media.php?module=siswa_det');
}

if ($act === 'hapus') {
    $ids = trim($_GET['ids'] ?? '');
    if (!$ids) alertRedirect('ID tidak valid.', '../media.php?module=siswa&kls=semua');

    $stmt = $pdo->prepare("DELETE FROM siswa WHERE ids = ?");
    $stmt->execute([$ids]);
    alertRedirect('Data Terhapus', '../media.php?module=siswa&kls=semua');
}

// ═══════════════════════════════════════════════════════
// ABSEN
// ═══════════════════════════════════════════════════════

if ($act === 'input_absen') {
    $kelas  = trim($_GET['kelas']   ?? '');
    $tgl    = trim($_GET['tanggal'] ?? '');
    $idj    = trim($_GET['idj']     ?? '');

    if (!$kelas || !$tgl || !$idj) {
        alertRedirect('Parameter absen tidak lengkap.', '../media.php?module=jadwal_mengajar');
    }

    $stmt = $pdo->prepare("SELECT ids FROM siswa WHERE idk = ?");
    $stmt->execute([$kelas]);
    $siswas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmtCheck  = $pdo->prepare("SELECT COUNT(*) FROM absen WHERE ids = ? AND tgl = ? AND idj = ?");
    $stmtInsert = $pdo->prepare("INSERT INTO absen (ids, idj, tgl, ket) VALUES (?, ?, ?, ?)");
    $stmtUpdate = $pdo->prepare("UPDATE absen SET ket = ? WHERE ids = ? AND tgl = ? AND idj = ?");

    foreach ($siswas as $siswa) {
        $ra  = $siswa['ids'];
        $ket = $_POST[$ra] ?? '';

        $stmtCheck->execute([$ra, $tgl, $idj]);
        $exists = $stmtCheck->fetchColumn();

        if (!$exists) {
            $stmtInsert->execute([$ra, $idj, $tgl, $ket]);
        } else {
            $stmtUpdate->execute([$ket, $ra, $tgl, $idj]);
        }
    }
    alertRedirect('Data Tersimpan', '../media.php?module=jadwal_mengajar');
}

if ($act == 'input_absensi') {
    $idm     = $_GET['idm'] ?? '';
    $tanggal = $_GET['tanggal'] ?? '';

    $stmt = $pdo->query("SELECT nis FROM siswa");

    while ($s = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $nis    = $s['nis'];
        $status = $_POST[$nis] ?? '';

        if ($status == '') continue;

        $cek = $pdo->prepare("SELECT id FROM absensi WHERE nis=? AND idm=? AND tanggal=?");
        $cek->execute([$nis, $idm, $tanggal]);

        if ($cek->rowCount() == 0) {
            $insert = $pdo->prepare("INSERT INTO absensi(nis,idm,tanggal,status) VALUES(?,?,?,?)");
            $insert->execute([$nis, $idm, $tanggal, $status]);
        } else {
            $update = $pdo->prepare("UPDATE absensi SET status=? WHERE nis=? AND idm=? AND tanggal=?");
            $update->execute([$status, $nis, $idm, $tanggal]);
        }
    }

    echo "<script>alert('Absensi berhasil disimpan'); window.location='../media.php?module=absen&idm=$idm';</script>";
}

// ═══════════════════════════════════════════════════════
// SEKOLAH
// ═══════════════════════════════════════════════════════

if ($act === 'input_sekolah') {
    $stmt = $pdo->prepare("INSERT INTO sekolah (kode, nama, alamat) VALUES (?, ?, ?)");
    $stmt->execute([
        trim($_POST['kode']   ?? ''),
        trim($_POST['nama']   ?? ''),
        trim($_POST['alamat'] ?? ''),
    ]);
    alertRedirect('Data Tersimpan', '../media.php?module=sekolah');
}

if ($act === 'edit_sekolah') {
    $stmt = $pdo->prepare("UPDATE sekolah SET kode = ?, nama = ?, alamat = ? WHERE id = ?");
    $stmt->execute([
        trim($_POST['kode']   ?? ''),
        trim($_POST['nama']   ?? ''),
        trim($_POST['alamat'] ?? ''),
        trim($_POST['id']     ?? ''),
    ]);
    alertRedirect('Data Tersimpan', '../media.php?module=sekolah');
}

if ($act === 'hapus_sekolah') {
    $id = trim($_GET['id'] ?? '');
    if (!$id) alertRedirect('ID tidak valid.', '../media.php?module=sekolah');

    $stmt = $pdo->prepare("DELETE FROM sekolah WHERE id = ?");
    $stmt->execute([$id]);
    alertRedirect('Data Terhapus', '../media.php?module=sekolah');
}

// ═══════════════════════════════════════════════════════
// KELAS
// ═══════════════════════════════════════════════════════

if ($act === 'input_kelas') {
    $stmt = $pdo->prepare("INSERT INTO kelas (id, nama) VALUES (?, ?)");
    $stmt->execute([
        trim($_POST['id']   ?? ''),
        trim($_POST['nama'] ?? ''),
    ]);
    alertRedirect('Data Tersimpan', '../media.php?module=kelas');
}

if ($act === 'edit_kelas') {
    $stmt = $pdo->prepare("UPDATE kelas SET id = ?, nama = ? WHERE idk = ?");
    $stmt->execute([
        trim($_POST['id']   ?? ''),
        trim($_POST['nama'] ?? ''),
        trim($_POST['idk']  ?? ''),
    ]);
    alertRedirect('Data Tersimpan', '../media.php?module=kelas');
}

if ($act === 'hapus_kelas') {
    $idk = trim($_GET['idk'] ?? '');
    if (!$idk) alertRedirect('ID tidak valid.', '../media.php?module=kelas');

    $stmt = $pdo->prepare("DELETE FROM kelas WHERE idk = ?");
    $stmt->execute([$idk]);
    alertRedirect('Data Terhapus', '../media.php?module=kelas');
}

// ═══════════════════════════════════════════════════════
// MATA PELAJARAN
// ═══════════════════════════════════════════════════════

if ($act === 'input_pelajaran') {
    $nama_mp   = trim($_POST['nama_mp'] ?? '');
    $kelasList = $_POST['kelas'] ?? [];

    if (!$nama_mp) {
        alertRedirect('Nama mata pelajaran tidak boleh kosong.', '../media.php?module=input_pelajaran&act=input');
    }

    // Insert mata pelajaran
    $stmt = $pdo->prepare("INSERT INTO mata_pelajaran (nama_mp) VALUES (?)");
    $stmt->execute([$nama_mp]);
    $newIdm = $pdo->lastInsertId();

    // Link selected kelas via jadwal — fetch first valid idh and idg as placeholders
    if (!empty($kelasList)) {
        $firstHari = $pdo->query("SELECT idh FROM hari ORDER BY idh ASC LIMIT 1")->fetchColumn();
        $firstGuru = $pdo->query("SELECT idg FROM guru ORDER BY idg ASC LIMIT 1")->fetchColumn();

        if ($firstHari && $firstGuru) {
            $stmtJadwal = $pdo->prepare("
                INSERT INTO jadwal (idh, idg, idk, idm, jam_mulai, jam_selesai)
                VALUES (?, ?, ?, ?, '00:00', '00:00')
            ");
            foreach ($kelasList as $idk) {
                $stmtJadwal->execute([$firstHari, $firstGuru, $idk, $newIdm]);
            }
        }
    }

    alertRedirect('Data Tersimpan', '../media.php?module=mata_pelajaran');
}

if ($act === 'edit_pelajaran') {
    $idm       = trim($_POST['idm'] ?? '');
    $nama_mp   = trim($_POST['nama_mp'] ?? '');
    $kelasList = $_POST['kelas'] ?? [];

    if (!$idm) alertRedirect('ID tidak valid.', '../media.php?module=mata_pelajaran');

    // Update nama mata pelajaran
    $stmt = $pdo->prepare("UPDATE mata_pelajaran SET nama_mp = ? WHERE idm = ?");
    $stmt->execute([$nama_mp, $idm]);

    // Update kelas links in jadwal
    if (!empty($kelasList)) {
        // Remove jadwal rows for deselected kelas
        $placeholders = implode(',', array_fill(0, count($kelasList), '?'));
        $stmtDel = $pdo->prepare("DELETE FROM jadwal WHERE idm = ? AND idk NOT IN ($placeholders)");
        $stmtDel->execute(array_merge([$idm], $kelasList));

        // Add jadwal rows for newly selected kelas not yet linked
        $firstHari = $pdo->query("SELECT idh FROM hari ORDER BY idh ASC LIMIT 1")->fetchColumn();
        $firstGuru = $pdo->query("SELECT idg FROM guru ORDER BY idg ASC LIMIT 1")->fetchColumn();

        if ($firstHari && $firstGuru) {
            $stmtCheck  = $pdo->prepare("SELECT COUNT(*) FROM jadwal WHERE idm = ? AND idk = ?");
            $stmtInsert = $pdo->prepare("
                INSERT INTO jadwal (idh, idg, idk, idm, jam_mulai, jam_selesai)
                VALUES (?, ?, ?, ?, '00:00', '00:00')
            ");
            foreach ($kelasList as $idk) {
                $stmtCheck->execute([$idm, $idk]);
                if ($stmtCheck->fetchColumn() == 0) {
                    $stmtInsert->execute([$firstHari, $firstGuru, $idk, $idm]);
                }
            }
        }
    } else {
        // No kelas selected — remove all jadwal for this mata pelajaran
        $stmtDel = $pdo->prepare("DELETE FROM jadwal WHERE idm = ?");
        $stmtDel->execute([$idm]);
    }

    alertRedirect('Data Tersimpan', '../media.php?module=mata_pelajaran');
}

if ($act === 'hapus_pelajaran') {
    $idm = trim($_GET['idm'] ?? '');
    if (!$idm) alertRedirect('ID tidak valid.', '../media.php?module=mata_pelajaran');

    $stmt = $pdo->prepare("DELETE FROM mata_pelajaran WHERE idm = ?");
    $stmt->execute([$idm]);
    alertRedirect('Data Terhapus', '../media.php?module=mata_pelajaran');
}

// ═══════════════════════════════════════════════════════
// JADWAL
// ═══════════════════════════════════════════════════════

if ($act === 'input_jadwal') {
    $stmt = $pdo->prepare("
        INSERT INTO jadwal (idh, idg, idk, idm, jam_mulai, jam_selesai)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        trim($_POST['hari']        ?? ''),
        trim($_POST['guru']        ?? ''),
        trim($_POST['kelas']       ?? ''),
        trim($_POST['pelajaran']   ?? ''),
        trim($_POST['jam_mulai']   ?? ''),
        trim($_POST['jam_selesai'] ?? ''),
    ]);
    alertRedirect('Data Tersimpan', '../media.php?module=senin');
}

if ($act === 'edit_jadwal') {
    $stmt = $pdo->prepare("
        UPDATE jadwal SET idh=?, idg=?, idk=?, idm=?, jam_mulai=?, jam_selesai=?
        WHERE idj=?
    ");
    $stmt->execute([
        trim($_POST['hari']        ?? ''),
        trim($_POST['guru']        ?? ''),
        trim($_POST['kelas']       ?? ''),
        trim($_POST['pelajaran']   ?? ''),
        trim($_POST['jam_mulai']   ?? ''),
        trim($_POST['jam_selesai'] ?? ''),
        trim($_POST['idj']         ?? ''),
    ]);
    alertRedirect('Data Tersimpan', '../media.php?module=senin');
}

if ($act === 'hapus_jadwal') {
    $idj = trim($_GET['idj'] ?? '');
    if (!$idj) alertRedirect('ID tidak valid.', '../media.php?module=senin');

    $stmt = $pdo->prepare("DELETE FROM jadwal WHERE idj = ?");
    $stmt->execute([$idj]);
    alertRedirect('Data Terhapus', '../media.php?module=senin');
}

// ═══════════════════════════════════════════════════════
// GURU
// ═══════════════════════════════════════════════════════

if ($act === 'input_guru') {
    $pass = $_POST['k_password'] ?? '';

    $stmt = $pdo->prepare("INSERT INTO guru (nip, nama, jk, alamat, pass) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        trim($_POST['nip']    ?? ''),
        trim($_POST['nama']   ?? ''),
        trim($_POST['jk']     ?? ''),
        trim($_POST['alamat'] ?? ''),
        hashPassword($pass),
    ]);
    alertRedirect('Data Tersimpan', '../media.php?module=guru&kls=semua');
}

if ($act === 'edit_guru') {
    $idg  = trim($_POST['idg'] ?? '');
    $pass = $_POST['k_password'] ?? '';

    if (!empty($pass)) {
        $stmt = $pdo->prepare("UPDATE guru SET nip=?, nama=?, jk=?, alamat=?, pass=? WHERE idg=?");
        $stmt->execute([
            trim($_POST['nip']    ?? ''),
            trim($_POST['nama']   ?? ''),
            trim($_POST['jk']     ?? ''),
            trim($_POST['alamat'] ?? ''),
            hashPassword($pass),
            $idg,
        ]);
    } else {
        $stmt = $pdo->prepare("UPDATE guru SET nip=?, nama=?, jk=?, alamat=? WHERE idg=?");
        $stmt->execute([
            trim($_POST['nip']    ?? ''),
            trim($_POST['nama']   ?? ''),
            trim($_POST['jk']     ?? ''),
            trim($_POST['alamat'] ?? ''),
            $idg,
        ]);
    }
    alertRedirect('Data Tersimpan', '../media.php?module=guru&kls=semua');
}

if ($act === 'hapus_guru') {
    $idg = trim($_GET['idg'] ?? '');
    if (!$idg) alertRedirect('ID tidak valid.', '../media.php?module=guru&kls=semua');

    $stmt = $pdo->prepare("DELETE FROM guru WHERE idg = ?");
    $stmt->execute([$idg]);
    alertRedirect('Data Guru Sudah Terhapus', '../media.php?module=guru&kls=semua');
}

if ($act === 'guru_det') {
    $pass = $_POST['pass'] ?? '';
    $idg  = trim($_POST['idg'] ?? '');

    if (!empty($pass)) {
        $stmt = $pdo->prepare("UPDATE guru SET nama=?, jk=?, alamat=?, pass=? WHERE idg=?");
        $stmt->execute([
            trim($_POST['nama']   ?? ''),
            trim($_POST['jk']     ?? ''),
            trim($_POST['alamat'] ?? ''),
            hashPassword($pass),
            $idg,
        ]);
    } else {
        $stmt = $pdo->prepare("UPDATE guru SET nama=?, jk=?, alamat=? WHERE idg=?");
        $stmt->execute([
            trim($_POST['nama']   ?? ''),
            trim($_POST['jk']     ?? ''),
            trim($_POST['alamat'] ?? ''),
            $idg,
        ]);
    }
    alertRedirect('Data Tersimpan', '../media.php?module=guru_det');
}
?>