<?php
// ─────────────────────────────────────────────
// Shared: fetch kelas list based on level
// ─────────────────────────────────────────────
$isAdminGuru = ($_SESSION['level'] ?? '') === 'admin_guru';
$sessionId   = $_SESSION['id'] ?? '';
$act         = $_GET['act'] ?? '';

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

// Helper: render kelas <option> list
function renderKelasOptions(array $kelasList, string $selectedIdk = ''): void {
    if (empty($kelasList)) {
        echo "<option disabled>Tidak ada kelas tersedia</option>";
        return;
    }
    foreach ($kelasList as $kelas) {
        $idk      = htmlspecialchars($kelas['idk']);
        $nama     = htmlspecialchars($kelas['nama']);
        $selected = $selectedIdk === $kelas['idk'] ? 'selected' : '';
        echo "<option value=\"$idk\" $selected>$nama</option>";
    }
}
?>

<?php if ($act === 'input'): ?>
<!-- ═══════════════════════════════════════════
     INPUT DATA SISWA
═══════════════════════════════════════════ -->
<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header"><i class="fa fa-plus-circle"></i> <strong>Input Data Siswa</strong></h3>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <i class="fa fa-user-plus"></i> Input Data Siswa
            </div>
            <div class="card-body">
                <div class="row">
                    <form method="post" role="form" action="module/simpan.php?act=input_siswa">

                        <!-- ─── Left Column ─── -->
                        <div class="col-lg-6">

                            <div class="form-group">
                                <label for="nis">NIS</label>
                                <input class="form-control" id="nis" placeholder="NIS" name="nis" required>
                            </div>

                            <div class="form-group">
                                <label for="nama">Nama</label>
                                <input class="form-control" id="nama" placeholder="Nama" name="nama" required>
                            </div>

                            <div class="form-group">
                                <label>Jenis Kelamin</label>
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="jk" value="L" checked> Laki - Laki
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="jk" value="P"> Perempuan
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="alamat">Alamat</label>
                                <textarea class="form-control" id="alamat" placeholder="Alamat" name="alamat" rows="3"></textarea>
                            </div>

                            <div class="form-group">
                                <label for="kelas">Kelas</label>
                                <select class="form-control" id="kelas" name="kelas">
                                    <?php renderKelasOptions($kelasList); ?>
                                </select>
                            </div>

                            <div class="form-group input-group">
                                <span class="input-group-addon">+62</span>
                                <input type="text" class="form-control" placeholder="No Telepon" name="tlp">
                            </div>

                        </div>
                        <!-- /.col-lg-6 -->

                        <!-- ─── Right Column ─── -->
                        <div class="col-lg-6">

                            <div class="form-group">
                                <label for="bapak">Nama Ayah</label>
                                <input class="form-control" id="bapak" placeholder="Nama Ayah" name="bapak">
                            </div>

                            <div class="form-group">
                                <label for="k_bapak">Pekerjaan Ayah</label>
                                <input class="form-control" id="k_bapak" placeholder="Pekerjaan" name="k_bapak">
                            </div>

                            <div class="form-group">
                                <label for="ibu">Nama Ibu</label>
                                <input class="form-control" id="ibu" placeholder="Nama Ibu" name="ibu">
                            </div>

                            <div class="form-group">
                                <label for="k_ibu">Pekerjaan Ibu</label>
                                <input class="form-control" id="k_ibu" placeholder="Pekerjaan" name="k_ibu">
                            </div>

                            <div class="form-group">
                                <label for="k_password">Password</label>
                                <input class="form-control" id="k_password" placeholder="Password"
                                    name="k_password" type="password" required>
                            </div>

                            <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Simpan</button>

                        </div>
                        <!-- /.col-lg-6 -->

                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
    <div class="col-lg-12" style="margin-top: 8px; margin-bottom: 24px;">
        <a href="media.php?module=tampil" class="btn btn-default">
            <i class="fa fa-arrow-left"></i> Kembali ke Data Siswa
        </a>
    </div>
</div>
</div>

<?php elseif ($act === 'edit'): ?>
<!-- ═══════════════════════════════════════════
     EDIT DATA SISWA
═══════════════════════════════════════════ -->
<?php
// Validate & fetch student
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
?>

<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header"><strong>Edit Data Siswa</strong></h3>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-primary">
            <div class="panel-heading">Edit Data Siswa</div>
            <div class="panel-body">
                <div class="row">
                    <form method="post" role="form" action="module/simpan.php?act=edit_siswa">
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($ids); ?>">

                        <!-- ─── Left Column ─── -->
                        <div class="col-lg-6">

                            <div class="form-group">
                                <label for="nis">NIS</label>
                                <input class="form-control" id="nis" placeholder="NIS" name="nis" required
                                    value="<?php echo htmlspecialchars($rs['nis']); ?>">
                            </div>

                            <div class="form-group">
                                <label for="nama">Nama</label>
                                <input class="form-control" id="nama" placeholder="Nama" name="nama" required
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
                                <label for="alamat">Alamat</label>
                                <textarea class="form-control" id="alamat" placeholder="Alamat" name="alamat" rows="3"><?php
                                    echo htmlspecialchars($rs['alamat']);
                                ?></textarea>
                            </div>

                            <div class="form-group">
                                <label for="kelas">Kelas</label>
                                <select class="form-control" id="kelas" name="kelas">
                                    <?php renderKelasOptions($kelasList, $rs['idk']); ?>
                                </select>
                            </div>

                            <div class="form-group input-group">
                                <span class="input-group-addon">+62</span>
                                <input type="text" class="form-control" placeholder="No Telepon" name="tlp"
                                    value="<?php echo htmlspecialchars($rs['tlp']); ?>">
                            </div>

                        </div>
                        <!-- /.col-lg-6 -->

                        <!-- ─── Right Column ─── -->
                        <div class="col-lg-6">

                            <div class="form-group">
                                <label for="bapak">Nama Ayah</label>
                                <input class="form-control" id="bapak" placeholder="Nama Ayah" name="bapak"
                                    value="<?php echo htmlspecialchars($rs['bapak']); ?>">
                            </div>

                            <div class="form-group">
                                <label for="k_bapak">Pekerjaan Ayah</label>
                                <input class="form-control" id="k_bapak" placeholder="Pekerjaan" name="k_bapak"
                                    value="<?php echo htmlspecialchars($rs['k_bapak']); ?>">
                            </div>

                            <div class="form-group">
                                <label for="ibu">Nama Ibu</label>
                                <input class="form-control" id="ibu" placeholder="Nama Ibu" name="ibu"
                                    value="<?php echo htmlspecialchars($rs['ibu']); ?>">
                            </div>

                            <div class="form-group">
                                <label for="k_ibu">Pekerjaan Ibu</label>
                                <input class="form-control" id="k_ibu" placeholder="Pekerjaan" name="k_ibu"
                                    value="<?php echo htmlspecialchars($rs['k_ibu']); ?>">
                            </div>

                            <div class="form-group">
                                <label for="k_password">Password <small class="text-muted">(kosongkan jika tidak diubah)</small></label>
                                <input class="form-control" id="k_password" placeholder="Password baru"
                                    name="k_password" type="password">
                            </div>

                            <button type="submit" class="btn btn-success">Simpan</button>

                        </div>
                        <!-- /.col-lg-6 -->

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php endif; ?>