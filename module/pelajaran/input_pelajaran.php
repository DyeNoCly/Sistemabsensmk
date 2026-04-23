<?php
$act = $_GET['act'] ?? '';

// Fetch all kelas (shared by both forms)
$allKelas = $pdo->query("SELECT idk, nama FROM kelas ORDER BY nama ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<?php if ($act === 'input'): ?>
<!-- ═══════════════════════════════════════════
     INPUT DATA MATA PELAJARAN
═══════════════════════════════════════════ -->
<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header"><strong>Input Data Mata Pelajaran</strong></h3>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-primary">
            <div class="panel-heading">Input Data Mata Pelajaran</div>
            <div class="panel-body">
                <div class="row">
                    <form method="post" role="form" action="module/simpan.php?act=input_pelajaran">

                        <!-- ─── Left: Nama Mata Pelajaran ─── -->
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="nama_mp">Mata Pelajaran</label>
                                <input type="text" class="form-control" id="nama_mp"
                                    placeholder="Mata Pelajaran" name="nama_mp" required>
                            </div>
                        </div>

                        <!-- ─── Right: Kelas Multiselect ─── -->
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Kelas <small class="text-muted">(pilih satu atau lebih)</small></label>
                                <?php if (empty($allKelas)): ?>
                                    <p class="text-muted">Tidak ada kelas tersedia.</p>
                                <?php else: ?>
                                    <select class="form-control" name="kelas[]" multiple
                                        size="<?php echo min(6, count($allKelas)); ?>">
                                        <?php foreach ($allKelas as $kelas): ?>
                                            <option value="<?php echo htmlspecialchars($kelas['idk']); ?>">
                                                <?php echo htmlspecialchars($kelas['nama']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <span class="help-block">
                                        <i class="fa fa-info-circle"></i>
                                        Tahan <kbd>Ctrl</kbd> (Windows) atau <kbd>Cmd</kbd> (Mac) untuk memilih beberapa kelas.
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- ─── Buttons ─── -->
                        <div class="col-lg-12">
                            <button type="submit" class="btn btn-success">Simpan</button>
                            <a href="media.php?module=mata_pelajaran" class="btn btn-default">Batal</a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php elseif ($act === 'edit_pelajaran'): ?>
<!-- ═══════════════════════════════════════════
     EDIT DATA MATA PELAJARAN
═══════════════════════════════════════════ -->
<?php
$idm = trim($_GET['idm'] ?? '');

if ($idm === '') {
    echo "<div class='alert alert-danger'>ID mata pelajaran tidak valid.</div>";
    return;
}

$stmt = $pdo->prepare("SELECT * FROM mata_pelajaran WHERE idm = ? LIMIT 1");
$stmt->execute([$idm]);
$rs = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$rs) {
    echo "<div class='alert alert-danger'>Data mata pelajaran tidak ditemukan.</div>";
    return;
}

// Fetch kelas already linked via jadwal
$stmtAssigned = $pdo->prepare("SELECT DISTINCT idk FROM jadwal WHERE idm = ?");
$stmtAssigned->execute([$idm]);
$assignedIdk = array_column($stmtAssigned->fetchAll(PDO::FETCH_ASSOC), 'idk');
?>

<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header"><strong>Edit Data Mata Pelajaran</strong></h3>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-primary">
            <div class="panel-heading">Edit Data Mata Pelajaran</div>
            <div class="panel-body">
                <div class="row">
                    <form method="post" role="form" action="module/simpan.php?act=edit_pelajaran">
                        <input type="hidden" name="idm" value="<?php echo htmlspecialchars($idm); ?>">

                        <!-- ─── Left: Nama Mata Pelajaran ─── -->
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="nama_mp">Mata Pelajaran</label>
                                <input class="form-control" id="nama_mp" placeholder="Mata Pelajaran"
                                    name="nama_mp" required
                                    value="<?php echo htmlspecialchars($rs['nama_mp']); ?>">
                            </div>
                        </div>

                        <!-- ─── Right: Kelas Multiselect ─── -->
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Kelas <small class="text-muted">(pilih satu atau lebih)</small></label>
                                <?php if (empty($allKelas)): ?>
                                    <p class="text-muted">Tidak ada kelas tersedia.</p>
                                <?php else: ?>
                                    <select class="form-control" name="kelas[]" multiple
                                        size="<?php echo min(6, count($allKelas)); ?>">
                                        <?php foreach ($allKelas as $kelas): ?>
                                            <option value="<?php echo htmlspecialchars($kelas['idk']); ?>"
                                                <?php echo in_array($kelas['idk'], $assignedIdk) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($kelas['nama']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <span class="help-block">
                                        <i class="fa fa-info-circle"></i>
                                        Tahan <kbd>Ctrl</kbd> (Windows) atau <kbd>Cmd</kbd> (Mac) untuk memilih beberapa kelas.
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- ─── Buttons ─── -->
                        <div class="col-lg-12">
                            <button type="submit" class="btn btn-success">Simpan</button>
                            <a href="media.php?module=mata_pelajaran" class="btn btn-default">Batal</a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php endif; ?>