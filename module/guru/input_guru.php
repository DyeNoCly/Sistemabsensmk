<?php
$act = $_GET['act'] ?? '';
?>

<?php if ($act === 'input'): ?>
<!-- ═══════════════════════════════════════════
     INPUT DATA GURU
═══════════════════════════════════════════ -->
<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header"><i class="fa fa-user-plus"></i> <strong>Input Data Guru</strong></h3>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <i class="fa fa-user-tie"></i> Input Data Guru
            </div>
            <div class="card-body">
                <div class="row">
                    <form method="post" role="form" action="module/simpan.php?act=input_guru">

                        <!-- ─── Left Column ─── -->
                        <div class="col-lg-6">

                            <div class="form-group">
                                <label for="nip">NIP</label>
                                <input class="form-control" id="nip" placeholder="NIP" name="nip" required>
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

                        </div>
                        <!-- /.col-lg-6 -->

                        <!-- ─── Right Column ─── -->
                        <div class="col-lg-6">

                            <div class="form-group">
                                <label for="alamat">Alamat</label>
                                <textarea class="form-control" id="alamat" placeholder="Alamat" name="alamat" rows="3"></textarea>
                            </div>

                            <div class="form-group">
                                <label for="k_password">Password</label>
                                <input class="form-control" id="k_password" placeholder="Password"
                                    name="k_password" type="password" required>
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

<?php elseif ($act === 'edit_guru'): ?>
<!-- ═══════════════════════════════════════════
     EDIT DATA GURU
═══════════════════════════════════════════ -->
<?php
$idg = trim($_GET['idg'] ?? '');

if ($idg === '') {
    echo "<div class='alert alert-danger'>ID guru tidak valid.</div>";
    return;
}

$stmt = $pdo->prepare("SELECT * FROM guru WHERE idg = ? LIMIT 1");
$stmt->execute([$idg]);
$rs = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$rs) {
    echo "<div class='alert alert-danger'>Data guru tidak ditemukan.</div>";
    return;
}
?>

<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header"><strong>Edit Data Guru</strong></h3>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-primary">
            <div class="panel-heading">Edit Data Guru</div>
            <div class="panel-body">
                <div class="row">
                    <form method="post" role="form" action="module/simpan.php?act=edit_guru">
                        <input type="hidden" name="idg" value="<?php echo htmlspecialchars($idg); ?>">

                        <!-- ─── Left Column ─── -->
                        <div class="col-lg-6">

                            <div class="form-group">
                                <label for="nip">NIP</label>
                                <input class="form-control" id="nip" placeholder="NIP" name="nip" required
                                    value="<?php echo htmlspecialchars($rs['nip']); ?>">
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

                        </div>
                        <!-- /.col-lg-6 -->

                        <!-- ─── Right Column ─── -->
                        <div class="col-lg-6">

                            <div class="form-group">
                                <label for="alamat">Alamat</label>
                                <textarea class="form-control" id="alamat" placeholder="Alamat" name="alamat" rows="3"><?php
                                    echo htmlspecialchars($rs['alamat']);
                                ?></textarea>
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