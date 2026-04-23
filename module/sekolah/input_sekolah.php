<?php
$act = $_GET['act'] ?? '';
?>

<?php if ($act === 'edit_sekolah'): ?>
<!-- ═══════════════════════════════════════════
     EDIT DATA SEKOLAH
═══════════════════════════════════════════ -->
<?php
$id = trim($_GET['id'] ?? '');

if ($id === '') {
    echo "<div class='alert alert-danger'>ID sekolah tidak valid.</div>";
    return;
}

$stmt = $pdo->prepare("SELECT * FROM sekolah WHERE id = ? LIMIT 1");
$stmt->execute([$id]);
$rs = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$rs) {
    echo "<div class='alert alert-danger'>Data sekolah tidak ditemukan.</div>";
    return;
}
?>

<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header"><strong>Edit Data Sekolah</strong></h3>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-primary">
            <div class="panel-heading">Edit Data Sekolah</div>
            <div class="panel-body">
                <div class="row">
                    <form method="post" role="form" action="module/simpan.php?act=edit_sekolah">
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">

                        <div class="col-lg-6">

                            <div class="form-group">
                                <label for="kode">Kode Sekolah</label>
                                <input class="form-control" id="kode" placeholder="Kode" name="kode" required
                                    value="<?php echo htmlspecialchars($rs['kode']); ?>">
                            </div>

                            <div class="form-group">
                                <label for="nama">Nama Sekolah</label>
                                <input class="form-control" id="nama" placeholder="Nama Sekolah" name="nama" required
                                    value="<?php echo htmlspecialchars($rs['nama']); ?>">
                            </div>

                            <div class="form-group">
                                <label for="alamat">Alamat</label>
                                <textarea class="form-control" id="alamat" placeholder="Alamat" name="alamat" rows="3"><?php
                                    echo htmlspecialchars($rs['alamat']);
                                ?></textarea>
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