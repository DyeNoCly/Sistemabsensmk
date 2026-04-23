<?php
// ─────────────────────────────────────────────
// Validate & fetch guru data
// ─────────────────────────────────────────────
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
        <h3 class="page-header">
            <strong>Data Guru: <?php echo htmlspecialchars($rs['nama']); ?></strong>
        </h3>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-primary">

            <div class="panel-heading">Data Guru</div>

            <div class="panel-body">
                <div class="row">

                    <!-- ─── Left Column ─── -->
                    <div class="col-lg-6">
                        <fieldset disabled>

                            <div class="form-group">
                                <label>NIP</label>
                                <input class="form-control" value="<?php echo htmlspecialchars($rs['nip']); ?>">
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

                        </fieldset>
                    </div>
                    <!-- /.col-lg-6 -->

                    <!-- ─── Right Column ─── -->
                    <div class="col-lg-6">
                        <fieldset disabled>

                            <div class="form-group">
                                <label>Alamat</label>
                                <textarea class="form-control" placeholder="Alamat" name="alamat" rows="3"><?php
                                    echo htmlspecialchars($rs['alamat']);
                                ?></textarea>
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