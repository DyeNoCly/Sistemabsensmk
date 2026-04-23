<?php
$act = $_GET['act'] ?? '';

// ─────────────────────────────────────────────
// Fetch all dropdown data upfront (shared by both forms)
// ─────────────────────────────────────────────
$hariList      = $pdo->query("SELECT idh, hari FROM hari ORDER BY idh ASC")->fetchAll(PDO::FETCH_ASSOC);
$pelajaranList = $pdo->query("SELECT idm, nama_mp FROM mata_pelajaran ORDER BY nama_mp ASC")->fetchAll(PDO::FETCH_ASSOC);
$kelasList     = $pdo->query("SELECT idk, nama FROM kelas ORDER BY nama ASC")->fetchAll(PDO::FETCH_ASSOC);
$guruList      = $pdo->query("SELECT idg, nama FROM guru ORDER BY nama ASC")->fetchAll(PDO::FETCH_ASSOC);

// ─────────────────────────────────────────────
// Helper: render <option> list
// ─────────────────────────────────────────────
function renderOptions(array $items, string $valueKey, string $labelKey, string $selectedVal = ''): void {
    foreach ($items as $item) {
        $val      = htmlspecialchars($item[$valueKey]);
        $label    = htmlspecialchars($item[$labelKey]);
        $selected = (string)$item[$valueKey] === (string)$selectedVal ? 'selected' : '';
        echo "<option value=\"$val\" $selected>$label</option>\n";
    }
}
?>

<?php if ($act === 'input'): ?>
<!-- ═══════════════════════════════════════════
     INPUT DATA JADWAL
═══════════════════════════════════════════ -->
<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header"><strong>Input Data Jadwal</strong></h3>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-primary">
            <div class="panel-heading">Input Data Jadwal</div>
            <div class="panel-body">
                <form method="post" role="form" action="module/simpan.php?act=input_jadwal">
                    <div class="row">

                        <!-- ─── Left Column ─── -->
                        <div class="col-lg-6">

                            <div class="form-group">
                                <label for="hari">Hari</label>
                                <select class="form-control" id="hari" name="hari" required>
                                    <option value="" disabled selected>-- Pilih Hari --</option>
                                    <?php renderOptions($hariList, 'idh', 'hari'); ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="jam_mulai">Jam Mulai</label>
                                <input type="time" class="form-control" id="jam_mulai" name="jam_mulai" required>
                            </div>

                            <div class="form-group">
                                <label for="jam_selesai">Jam Selesai</label>
                                <input type="time" class="form-control" id="jam_selesai" name="jam_selesai" required>
                            </div>

                        </div>
                        <!-- /.col-lg-6 -->

                        <!-- ─── Right Column ─── -->
                        <div class="col-lg-6">

                            <div class="form-group">
                                <label for="pelajaran">Mata Pelajaran</label>
                                <select class="form-control" id="pelajaran" name="pelajaran" required>
                                    <option value="" disabled selected>-- Pilih Mata Pelajaran --</option>
                                    <?php renderOptions($pelajaranList, 'idm', 'nama_mp'); ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="kelas">Kelas</label>
                                <select class="form-control" id="kelas" name="kelas" required>
                                    <option value="" disabled selected>-- Pilih Kelas --</option>
                                    <?php renderOptions($kelasList, 'idk', 'nama'); ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="guru">Guru</label>
                                <select class="form-control" id="guru" name="guru" required>
                                    <option value="" disabled selected>-- Pilih Guru --</option>
                                    <?php renderOptions($guruList, 'idg', 'nama'); ?>
                                </select>
                            </div>

                        </div>
                        <!-- /.col-lg-6 -->

                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-center">
                            <button type="submit" class="btn btn-success">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php elseif ($act === 'edit_jadwal'): ?>
<!-- ═══════════════════════════════════════════
     EDIT DATA JADWAL
═══════════════════════════════════════════ -->
<?php
$idj = trim($_GET['idj'] ?? '');

if ($idj === '') {
    echo "<div class='alert alert-danger'>ID jadwal tidak valid.</div>";
    return;
}

$stmt = $pdo->prepare("SELECT * FROM jadwal WHERE idj = ? LIMIT 1");
$stmt->execute([$idj]);
$rsx = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$rsx) {
    echo "<div class='alert alert-danger'>Data jadwal tidak ditemukan.</div>";
    return;
}
?>

<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header"><strong>Edit Data Jadwal</strong></h3>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-primary">
            <div class="panel-heading">Edit Data Jadwal</div>
            <div class="panel-body">
                <form method="post" role="form" action="module/simpan.php?act=edit_jadwal">
                    <input type="hidden" name="idj" value="<?php echo htmlspecialchars($idj); ?>">

                    <div class="row">

                        <!-- ─── Left Column ─── -->
                        <div class="col-lg-6">

                            <div class="form-group">
                                <label for="hari">Hari</label>
                                <select class="form-control" id="hari" name="hari" required>
                                    <option value="" disabled>-- Pilih Hari --</option>
                                    <?php renderOptions($hariList, 'idh', 'hari', $rsx['idh']); ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="jam_mulai">Jam Mulai</label>
                                <input type="time" class="form-control" id="jam_mulai" name="jam_mulai" required
                                    value="<?php echo htmlspecialchars($rsx['jam_mulai']); ?>">
                            </div>

                            <div class="form-group">
                                <label for="jam_selesai">Jam Selesai</label>
                                <input type="time" class="form-control" id="jam_selesai" name="jam_selesai" required
                                    value="<?php echo htmlspecialchars($rsx['jam_selesai']); ?>">
                            </div>

                        </div>
                        <!-- /.col-lg-6 -->

                        <!-- ─── Right Column ─── -->
                        <div class="col-lg-6">

                            <div class="form-group">
                                <label for="pelajaran">Mata Pelajaran</label>
                                <select class="form-control" id="pelajaran" name="pelajaran" required>
                                    <option value="" disabled>-- Pilih Mata Pelajaran --</option>
                                    <?php renderOptions($pelajaranList, 'idm', 'nama_mp', $rsx['idm']); ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="kelas">Kelas</label>
                                <select class="form-control" id="kelas" name="kelas" required>
                                    <option value="" disabled>-- Pilih Kelas --</option>
                                    <?php renderOptions($kelasList, 'idk', 'nama', $rsx['idk']); ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="guru">Guru</label>
                                <select class="form-control" id="guru" name="guru" required>
                                    <option value="" disabled>-- Pilih Guru --</option>
                                    <?php renderOptions($guruList, 'idg', 'nama', $rsx['idg']); ?>
                                </select>
                            </div>

                        </div>
                        <!-- /.col-lg-6 -->

                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-center">
                            <button type="submit" class="btn btn-success">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php endif; ?>