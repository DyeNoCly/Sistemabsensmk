<?php
// ─────────────────────────────────────────────
// Determine active day from module name
// ─────────────────────────────────────────────
$moduleHariMap = [
    'senin'  => ['idh' => 1, 'label' => 'Senin'],
    'selasa' => ['idh' => 2, 'label' => 'Selasa'],
    'rabu'   => ['idh' => 3, 'label' => 'Rabu'],
    'kamis'  => ['idh' => 4, 'label' => 'Kamis'],
    'jumat'  => ['idh' => 5, 'label' => "Jum'at"],
];

$currentModule = $_GET['module'] ?? 'senin';
$activeHari    = $moduleHariMap[$currentModule] ?? $moduleHariMap['senin'];

// ─────────────────────────────────────────────
// Fetch jadwal for active day with a single JOIN query
// ─────────────────────────────────────────────
$stmt = $pdo->prepare("
    SELECT
        j.idj,
        h.hari,
        j.jam_mulai,
        j.jam_selesai,
        k.nama  AS nama_kelas,
        g.nama  AS nama_guru,
        mp.nama_mp
    FROM jadwal j
    INNER JOIN hari          h  ON h.idh  = j.idh
    INNER JOIN kelas         k  ON k.idk  = j.idk
    INNER JOIN guru          g  ON g.idg  = j.idg
    INNER JOIN mata_pelajaran mp ON mp.idm = j.idm
    WHERE j.idh = ?
    ORDER BY j.jam_mulai ASC
");
$stmt->execute([$activeHari['idh']]);
$jadwalList = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header"><strong>Data Jadwal</strong></h3>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-primary">

            <!-- ─── Day Tabs ─── -->
            <ul class="nav nav-tabs">
                <?php foreach ($moduleHariMap as $module => $hari): ?>
                    <li role="presentation" <?php echo $module === $currentModule ? 'class="active"' : ''; ?>>
                        <a href="media.php?module=<?php echo $module; ?>">
                            <?php echo htmlspecialchars($hari['label']); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>

            <br>

            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th class="text-center">Hari</th>
                                <th class="text-center">Jam</th>
                                <th class="text-center">Kelas</th>
                                <th class="text-center">Guru</th>
                                <th class="text-center">Mata Pelajaran</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($jadwalList)): ?>
                                <tr>
                                    <td colspan="7" class="text-center">
                                        Tidak ada jadwal untuk hari <?php echo htmlspecialchars($activeHari['label']); ?>.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($jadwalList as $no => $rs): ?>
                                    <tr class="odd gradeX">
                                        <td class="text-center"><?php echo $no + 1; ?></td>
                                        <td><?php echo htmlspecialchars($rs['hari']); ?></td>
                                        <td><?php echo htmlspecialchars($rs['jam_mulai']) . ' - ' . htmlspecialchars($rs['jam_selesai']); ?></td>
                                        <td><?php echo htmlspecialchars($rs['nama_kelas']); ?></td>
                                        <td><?php echo htmlspecialchars($rs['nama_guru']); ?></td>
                                        <td><?php echo htmlspecialchars($rs['nama_mp']); ?></td>
                                        <td class="text-center">
                                            <a href="media.php?module=input_jadwal&act=edit_jadwal&idj=<?php echo urlencode($rs['idj']); ?>">
                                                <button type="button" class="btn btn-info">Edit</button>
                                            </a>
                                            <a href="module/simpan.php?act=hapus_jadwal&idj=<?php echo urlencode($rs['idj']); ?>"
                                               onclick="return confirm('Yakin ingin menghapus jadwal ini?')">
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