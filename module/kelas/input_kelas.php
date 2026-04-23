<?php
$act         = $_GET['act'] ?? '';
$isAdminGuru = ($_SESSION['level'] ?? '') === 'admin_guru';
$sessionId   = $_SESSION['id'] ?? '';

// ─────────────────────────────────────────────
// Fetch sekolah list based on level
// ─────────────────────────────────────────────
if ($isAdminGuru) {
    $stmtSekolah = $pdo->prepare("SELECT id, nama FROM sekolah WHERE id = ? LIMIT 1");
    $stmtSekolah->execute([$sessionId]);
} else {
    $stmtSekolah = $pdo->query("SELECT id, nama FROM sekolah ORDER BY nama ASC");
}
$sekolahList = $stmtSekolah->fetchAll(PDO::FETCH_ASSOC);

// Helper: render sekolah <option> list
function renderSekolahOptions(array $sekolahList, string $selectedId = ''): void {
    if (empty($sekolahList)) {
        echo "<option disabled>Tidak ada sekolah tersedia</option>";
        return;
    }
    foreach ($sekolahList as $s) {
        $id       = htmlspecialchars($s['id']);
        $nama     = htmlspecialchars($s['nama']);
        $selected = $selectedId === (string)$s['id'] ? 'selected' : '';
        echo "<option value=\"$id\" $selected>$nama</option>";
    }
}
?>

<?php if ($act === 'input'): ?>
<!-- ═══════════════════════════════════════════
     INPUT DATA KELAS
═══════════════════════════════════════════ -->
<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header"><strong>Input Data Kelas</strong></h3>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-primary">
            <div class="panel-heading">Input Data Kelas</div>
            <div class="panel-body">
                <div class="row">
                    <form method="post" role="form" action="module/simpan.php?act=input_kelas">

                        <div class="col-lg-6">
                          <!--
                            <div class="form-group">
                                <label for="id">Nama Sekolah</label>
                                <select class="form-control" id="id" name="id">
                                    <?php renderSekolahOptions($sekolahList); ?>
                                </select>
                            </div>
                          -->
                            <div class="form-group">
                                <label for="nama">Nama Kelas</label>
                                <input class="form-control" id="nama" placeholder="Kelas" name="nama" required>
                            </div>

                            <button type="submit" class="btn btn-success">Simpan</button>

                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php elseif ($act === 'edit_kelas'): ?>
<!-- ═══════════════════════════════════════════
     EDIT DATA KELAS
═══════════════════════════════════════════ -->
<?php
$idk = trim($_GET['idk'] ?? '');

if ($idk === '') {
    echo "<div class='alert alert-danger'>ID kelas tidak valid.</div>";
    return;
}

$stmt = $pdo->prepare("SELECT * FROM kelas WHERE idk = ? LIMIT 1");
$stmt->execute([$idk]);
$rs = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$rs) {
    echo "<div class='alert alert-danger'>Data kelas tidak ditemukan.</div>";
    return;
}
?>

<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header"><strong>Edit Data Kelas</strong></h3>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-primary">
            <div class="panel-heading">Edit Data Kelas</div>
            <div class="panel-body">
                <div class="row">
                    <form method="post" role="form" action="module/simpan.php?act=edit_kelas">
                        <input type="hidden" name="idk" value="<?php echo htmlspecialchars($idk); ?>">

                        <div class="col-lg-6">
<!--
                            <div class="form-group">
                                <label for="id">Nama Sekolah</label>
                                <select class="form-control" id="id" name="id">
                                    <?php renderSekolahOptions($sekolahList, (string)$rs['id']); ?>
                                </select>
                            </div>
-->
                            <div class="form-group">
                                <label for="nama">Nama Kelas</label>
                                <input class="form-control" id="nama" placeholder="Kelas" name="nama" required
                                    value="<?php echo htmlspecialchars($rs['nama']); ?>">
                            </div>

                            <button type="submit" class="btn btn-success">Simpan</button>

                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php endif; ?>