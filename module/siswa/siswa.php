<?php
// ─────────────────────────────────────────────
// Get & validate filter parameter
// ─────────────────────────────────────────────
$klas = trim($_GET['kls'] ?? 'semua');

// Fetch class name for panel heading
$panel_heading = 'Data Semua Siswa';
if ($klas !== 'semua') {
    $stmt = $pdo->prepare("SELECT nama FROM kelas WHERE idk = ? LIMIT 1");
    $stmt->execute([$klas]);
    $click = $stmt->fetch(PDO::FETCH_ASSOC);
    $panel_heading = 'Data Siswa Kelas ' . htmlspecialchars($click['nama'] ?? '');
}

// Fetch students
if ($klas === 'semua') {
    $stmt = $pdo->query("SELECT * FROM siswa");
} else {
    $stmt = $pdo->prepare("SELECT * FROM siswa WHERE idk = ?");
    $stmt->execute([$klas]);
}
$siswas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Pre-fetch all kelas into a lookup array to avoid N+1 queries
$kelasLookup = [];
$stmtKelas = $pdo->query("SELECT idk, nama, id FROM kelas");
foreach ($stmtKelas->fetchAll(PDO::FETCH_ASSOC) as $k) {
    $kelasLookup[$k['idk']] = $k;
}

$isAdminGuru = ($_SESSION['level'] ?? '') === 'admin_guru';
$sessionId   = $_SESSION['id'] ?? '';
?>

<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header"><i class="fa fa-users"></i> <strong>Data Siswa</strong></h3>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <i class="fa fa-list"></i> <?php echo $panel_heading; ?>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                        <thead>
                            <tr>
                                <th class="text-center"><i class="fa fa-id-card"></i> NIS</th>
                                <th class="text-center" width="30%"><i class="fa fa-user"></i> Nama</th>
                                <th class="text-center"><i class="fa fa-venus-mars"></i> Gender</th>
                                <th class="text-center"><i class="fa fa-school"></i> Kelas</th>
                                <th class="text-center"><i class="fa fa-phone"></i> No Telepon</th>
                                <th class="text-center"><i class="fa fa-cogs"></i> Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($siswas)): ?>
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada data siswa.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($siswas as $rs):
                                    $kelasData  = $kelasLookup[$rs['idk']] ?? [];
                                    $kelasNama  = htmlspecialchars($kelasData['nama'] ?? '-');
                                    $kelasId    = $kelasData['id'] ?? '';
                                    $jk         = $rs['jk'] === 'L' ? 'Laki - Laki' : 'Perempuan';
                                    $ids        = urlencode($rs['ids']);

                                    // Skip if admin_guru and school doesn't match
                                    if ($isAdminGuru && $kelasId != $sessionId) continue;
                                ?>
                                    <tr class="odd gradeX">
                                        <td><?php echo htmlspecialchars($rs['nis']); ?></td>
                                        <td><?php echo htmlspecialchars($rs['nama']); ?></td>
                                        <td class="text-center"><?php echo $jk; ?></td>
                                        <td><?php echo $isAdminGuru ? htmlspecialchars($rs['idk']) : $kelasNama; ?></td>
                                        <td><?php echo htmlspecialchars($rs['tlp']); ?></td>
                                        <td class="text-center">
                                            <?php if (!$isAdminGuru): ?>
                                                <a href="media.php?module=detail_siswa&act=details&ids=<?php echo $ids; ?>">
                                                    <button type="button" class="btn btn-warning">Detail</button>
                                                </a>
                                            <?php endif; ?>
                                            <a href="media.php?module=input_siswa&act=edit&ids=<?php echo $ids; ?>">
                                                <button type="button" class="btn btn-info">Edit</button>
                                            </a>
                                            <a href="module/simpan.php?act=hapus&ids=<?php echo $ids; ?>"
                                               onclick="return confirm('Yakin ingin menghapus siswa ini?')">
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