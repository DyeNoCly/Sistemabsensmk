<?php
// ─────────────────────────────────────────────
// Validate & fetch student data
// ─────────────────────────────────────────────
$ids = trim($_GET['ids'] ?? '');

if ($ids === '') {
    echo "<div class='alert alert-danger'>ID siswa tidak valid.</div>";
    return;
}

$stmt = $pdo->prepare("SELECT * FROM siswa WHERE ids = ? LIMIT 1");
$stmt->execute([$ids]);
$rs = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$rs) {
    echo "<div class='alert alert-danger'>Data siswa tidak ditemukan.</div>";
    return;
}

// ─────────────────────────────────────────────
// Fetch kelas list
// ─────────────────────────────────────────────
$isAdminGuru = ($_SESSION['level'] ?? '') === 'admin_guru';
$sessionId   = $_SESSION['id'] ?? '';

if ($isAdminGuru) {
    $stmtKelas = $pdo->prepare("
        SELECT k.idk, k.nama
        FROM kelas k
        INNER JOIN sekolah s ON s.id = k.id
        WHERE s.id = ?
        ORDER BY k.nama ASC
    ");
    $stmtKelas->execute([$sessionId]);
} else {
    $stmtKelas = $pdo->query("SELECT idk, nama FROM kelas ORDER BY nama ASC");
}
$kelasList = $stmtKelas->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header">
            <strong>Detail Siswa: <?php echo htmlspecialchars($rs['nama']); ?></strong>
        </h3>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-primary">

            <div class="panel-heading">Data Siswa</div>

            <div class="panel-body">
                <div class="row">

                    <!-- ─── Left Column ─── -->
                    <div class="col-lg-6">
                        <fieldset disabled>

                            <div class="form-group">
                                <label>NIS</label>
                                <input class="form-control" placeholder="NIS" name="nis"
                                    value="<?php echo htmlspecialchars($rs['nis']); ?>">
                            </div>

                            <div class="form-group">
                                <label>Nama</label>
                                <input class="form-control" placeholder="Nama" name="nama"
                                    value="<?php echo htmlspecialchars($rs['nama']); ?>">
                            </div>

                            <div class="form-group">
                                <label>Jenis Kelamin</label>
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="jk" value="L"
                                            <?php echo $rs['jk'] === 'L' ? 'checked' : ''; ?>>
                                        Laki - Laki
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="jk" value="P"
                                            <?php echo $rs['jk'] === 'P' ? 'checked' : ''; ?>>
                                        Perempuan
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Alamat</label>
                                <textarea class="form-control" placeholder="Alamat" name="alamat" rows="3"><?php
                                    echo htmlspecialchars($rs['alamat']);
                                ?></textarea>
                            </div>

                            <div class="form-group">
                                <label>Kelas</label>
                                <select class="form-control" name="kelas">
                                    <?php if (empty($kelasList)): ?>
                                        <option disabled>Tidak ada kelas tersedia</option>
                                    <?php else: ?>
                                        <?php foreach ($kelasList as $kelas): ?>
                                            <option value="<?php echo htmlspecialchars($kelas['idk']); ?>"
                                                <?php echo $rs['idk'] === $kelas['idk'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($kelas['nama']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <div class="form-group input-group">
                                <span class="input-group-addon">+62</span>
                                <input type="text" class="form-control" placeholder="No Telepon" name="tlp"
                                    value="<?php echo htmlspecialchars($rs['tlp']); ?>">
                            </div>

                        </fieldset>
                    </div>
                    <!-- /.col-lg-6 -->

                    <!-- ─── Right Column ─── -->
                    <div class="col-lg-6">
                        <fieldset disabled>

                            <div class="form-group">
                                <label>Nama Ayah</label>
                                <input class="form-control" placeholder="Nama Ayah" name="bapak"
                                    value="<?php echo htmlspecialchars($rs['bapak']); ?>">
                            </div>

                            <div class="form-group">
                                <label>Pekerjaan Ayah</label>
                                <input class="form-control" placeholder="Pekerjaan" name="k_bapak"
                                    value="<?php echo htmlspecialchars($rs['k_bapak']); ?>">
                            </div>

                            <div class="form-group">
                                <label>Nama Ibu</label>
                                <input class="form-control" placeholder="Nama Ibu" name="ibu"
                                    value="<?php echo htmlspecialchars($rs['ibu']); ?>">
                            </div>

                            <div class="form-group">
                                <label>Pekerjaan Ibu</label>
                                <input class="form-control" placeholder="Pekerjaan" name="k_ibu"
                                    value="<?php echo htmlspecialchars($rs['k_ibu']); ?>">
                            </div>

                        </fieldset>
                    </div>
                    <!-- /.col-lg-6 -->

                </div>
                <!-- /.row -->
            </div>
            <!-- /.panel-body -->

        </div>
        <!-- /.panel -->
    </div>
</div>