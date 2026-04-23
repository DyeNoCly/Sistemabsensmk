<?php
// ─────────────────────────────────────────────
// Fetch kelas only
// ─────────────────────────────────────────────
$isAdminGuru = ($_SESSION['level'] ?? '') === 'admin_guru';
$sessionId   = $_SESSION['id'] ?? '';

if ($isAdminGuru) {
    $stmt = $pdo->prepare("
        SELECT idk, nama AS kelas_nama
        FROM kelas
        WHERE id = ?
        ORDER BY nama ASC
    ");
    $stmt->execute([$sessionId]);
} else {
    $stmt = $pdo->query("
        SELECT idk, nama AS kelas_nama
        FROM kelas
        ORDER BY nama ASC
    ");
}

$kelasList = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header"><i class="fa fa-school"></i> <strong>Data Kelas</strong></h3>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <i class="fa fa-list"></i> Data Kelas
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                        <thead>
                            <tr>
                                <th class="text-center"><i class="fa fa-graduation-cap"></i> Kelas</th>
                                <th class="text-center"><i class="fa fa-cogs"></i> Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($kelasList)): ?>
                                <tr>
                                    <td colspan="2" class="text-center">Tidak ada data kelas.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($kelasList as $rs): ?>
                                    <tr class="odd gradeX">
                                        <td class="text-center"><?php echo htmlspecialchars($rs['kelas_nama']); ?></td>
                                        <td class="text-center">
                                            <a href="media.php?module=input_kelas&act=edit_kelas&idk=<?php echo urlencode($rs['idk']); ?>">
                                                <button type="button" class="btn btn-info">Edit</button>
                                            </a>
                                            <a href="module/simpan.php?act=hapus_kelas&idk=<?php echo urlencode($rs['idk']); ?>"
                                               onclick="return confirm('Yakin ingin menghapus kelas ini?')">
                                                <button type="button" class="btn btn-danger">Hapus</button>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>