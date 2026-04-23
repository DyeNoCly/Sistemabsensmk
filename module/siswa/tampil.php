<?php
// ─────────────────────────────────────────────
// Fetch kelas list based on user level
// ─────────────────────────────────────────────
$isGuru     = ($_SESSION['level'] ?? '') === 'guru';
$sessionIdk = $_SESSION['idk'] ?? '';

if ($isGuru) {
    $stmt = $pdo->prepare("SELECT idk, nama FROM kelas WHERE idk = ? LIMIT 1");
    $stmt->execute([$sessionIdk]);
} else {
    $stmt = $pdo->query("SELECT idk, nama FROM kelas ORDER BY nama ASC");
}
$kelasList = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header"><strong>Data Siswa Per-Kelas</strong></h3>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-primary">

            <div class="panel-heading">
                Pilih Kelas
            </div>

            <div class="panel-body">
                <div class="row">
                    <form method="get" action="media.php">
                        <input type="hidden" name="module" value="siswa">

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="kls">Kelas</label>
                                <select class="form-control" name="kls" id="kls">
                                    <?php if (!$isGuru): ?>
                                        <option value="semua">-- Semua Kelas --</option>
                                    <?php endif; ?>
                                    <?php foreach ($kelasList as $rs): ?>
                                        <option value="<?php echo htmlspecialchars($rs['idk']); ?>">
                                            <?php echo htmlspecialchars($rs['nama']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                    <?php if (empty($kelasList)): ?>
                                        <option disabled>Tidak ada kelas tersedia</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success">Submit</button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>