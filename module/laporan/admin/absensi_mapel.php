<?php
if (!isset($_SESSION['level']) || $_SESSION['level'] !== 'admin') {
    echo "<div class='alert alert-danger'>Akses ditolak.</div>";
    return;
}

$selectedIdm = isset($_GET['idm']) ? (int) $_GET['idm'] : 0;
$selectedIdk = isset($_GET['idk']) ? (int) $_GET['idk'] : 0;
$startDate = trim($_GET['start_date'] ?? '');
$endDate = trim($_GET['end_date'] ?? '');

$mapelStmt = $pdo->query("SELECT idm, nama_mp FROM mata_pelajaran ORDER BY nama_mp ASC");
$mapelList = $mapelStmt->fetchAll(PDO::FETCH_ASSOC);

$kelasStmt = $pdo->query("SELECT idk, nama FROM kelas ORDER BY nama ASC");
$kelasList = $kelasStmt->fetchAll(PDO::FETCH_ASSOC);

$attendanceRows = [];
$summary = ['H' => 0, 'I' => 0, 'A' => 0];

if ($selectedIdm > 0) {
    $where = ["a.idm = ?"];
    $params = [$selectedIdm];

    if ($selectedIdk > 0) {
        $where[] = "s.idk = ?";
        $params[] = $selectedIdk;
    }

    if ($startDate !== '') {
        $where[] = "a.tanggal >= ?";
        $params[] = $startDate;
    }

    if ($endDate !== '') {
        $where[] = "a.tanggal <= ?";
        $params[] = $endDate;
    }

    $sql = "
        SELECT
            a.tanggal,
            a.nis,
            s.nama AS nama_siswa,
            k.nama AS nama_kelas,
            mp.nama_mp,
            a.status,
            a.latitude,
            a.longitude,
            a.photo_path
        FROM absensi a
        INNER JOIN siswa s ON s.nis = a.nis
        LEFT JOIN kelas k ON k.idk = s.idk
        INNER JOIN mata_pelajaran mp ON mp.idm = a.idm
        WHERE " . implode(' AND ', $where) . "
        ORDER BY a.tanggal DESC, s.nama ASC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $attendanceRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $summaryStmt = $pdo->prepare(
        "SELECT a.status, COUNT(*) AS total
         FROM absensi a
         INNER JOIN siswa s ON s.nis = a.nis
         WHERE " . implode(' AND ', $where) . "
         GROUP BY a.status"
    );
    $summaryStmt->execute($params);

    while ($row = $summaryStmt->fetch(PDO::FETCH_ASSOC)) {
        $status = $row['status'];
        if (isset($summary[$status])) {
            $summary[$status] = (int) $row['total'];
        }
    }
}

$totalData = $summary['H'] + $summary['I'] + $summary['A'];
?>

<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header"><strong>Laporan Absensi Siswa per Mata Pelajaran</strong></h3>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-primary">
            <div class="panel-heading">Filter Laporan</div>
            <div class="panel-body">
                <form method="get" action="media.php" class="form-inline">
                    <input type="hidden" name="module" value="laporan_absensi_mapel">

                    <div class="form-group" style="margin-right: 10px; margin-bottom: 10px;">
                        <label for="idm" style="margin-right: 8px;">Mata Pelajaran</label>
                        <select class="form-control" id="idm" name="idm" required>
                            <option value="">-- Pilih Mata Pelajaran --</option>
                            <?php foreach ($mapelList as $mapel): ?>
                                <option value="<?php echo (int) $mapel['idm']; ?>" <?php echo $selectedIdm === (int) $mapel['idm'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($mapel['nama_mp']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group" style="margin-right: 10px; margin-bottom: 10px;">
                        <label for="idk" style="margin-right: 8px;">Kelas</label>
                        <select class="form-control" id="idk" name="idk">
                            <option value="">-- Semua Kelas --</option>
                            <?php foreach ($kelasList as $kelas): ?>
                                <option value="<?php echo (int) $kelas['idk']; ?>" <?php echo $selectedIdk === (int) $kelas['idk'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($kelas['nama']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group" style="margin-right: 10px; margin-bottom: 10px;">
                        <label for="start_date" style="margin-right: 8px;">Dari Tanggal</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo htmlspecialchars($startDate); ?>">
                    </div>

                    <div class="form-group" style="margin-right: 10px; margin-bottom: 10px;">
                        <label for="end_date" style="margin-right: 8px;">Sampai Tanggal</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo htmlspecialchars($endDate); ?>">
                    </div>

                    <button type="submit" class="btn btn-success" style="margin-bottom: 10px;">
                        <i class="fa fa-search"></i> Tampilkan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php if ($selectedIdm > 0): ?>
<div class="row" style="margin-bottom: 16px;">
    <div class="col-sm-3">
        <div class="well" style="margin-bottom: 0;">
            <strong>Total Data:</strong><br><?php echo $totalData; ?>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="well" style="margin-bottom: 0; border-left: 5px solid #2ecc71;">
            <strong>Hadir (H):</strong><br><?php echo $summary['H']; ?>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="well" style="margin-bottom: 0; border-left: 5px solid #f39c12;">
            <strong>Izin (I):</strong><br><?php echo $summary['I']; ?>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="well" style="margin-bottom: 0; border-left: 5px solid #e74c3c;">
            <strong>Alpha (A):</strong><br><?php echo $summary['A']; ?>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-primary">
            <div class="panel-heading">Hasil Absensi Siswa</div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th class="text-center">Tanggal</th>
                                <th class="text-center">NIS</th>
                                <th class="text-center">Nama Siswa</th>
                                <th class="text-center">Kelas</th>
                                <th class="text-center">Mata Pelajaran</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Lokasi</th>
                                <th class="text-center">Foto</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($attendanceRows)): ?>
                                <tr>
                                    <td class="text-center">1</td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                    <td>Belum ada data absensi untuk filter ini.</td>
                                    <td class="text-center">-</td>
                                    <td>-</td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($attendanceRows as $index => $row): ?>
                                    <?php
                                    $status = $row['status'];
                                    $statusClass = 'default';
                                    if ($status === 'H') {
                                        $statusClass = 'success';
                                    } elseif ($status === 'I') {
                                        $statusClass = 'warning';
                                    } elseif ($status === 'A') {
                                        $statusClass = 'danger';
                                    }

                                    $location = '-';
                                    if (!empty($row['latitude']) && !empty($row['longitude'])) {
                                        $location = htmlspecialchars($row['latitude']) . ', ' . htmlspecialchars($row['longitude']);
                                    }
                                    ?>
                                    <tr>
                                        <td class="text-center"><?php echo $index + 1; ?></td>
                                        <td class="text-center"><?php echo htmlspecialchars($row['tanggal']); ?></td>
                                        <td class="text-center"><?php echo htmlspecialchars($row['nis']); ?></td>
                                        <td><?php echo htmlspecialchars($row['nama_siswa']); ?></td>
                                        <td class="text-center"><?php echo htmlspecialchars($row['nama_kelas'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($row['nama_mp']); ?></td>
                                        <td class="text-center"><span class="label label-<?php echo $statusClass; ?>"><?php echo htmlspecialchars($status); ?></span></td>
                                        <td class="text-center"><?php echo $location; ?></td>
                                        <td class="text-center">
                                            <?php if (!empty($row['photo_path'])): ?>
                                                <a href="<?php echo htmlspecialchars($row['photo_path']); ?>" target="_blank" class="btn btn-info btn-xs">
                                                    <i class="fa fa-image"></i> Lihat Foto
                                                </a>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
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
<?php endif; ?>
