<?php
// Fetch mata pelajaran with kelas names joined through jadwal
$stmt = $pdo->query("
    SELECT mp.idm, mp.nama_mp,
           GROUP_CONCAT(DISTINCT k.nama ORDER BY k.nama ASC SEPARATOR ', ') AS nama_kelas
    FROM mata_pelajaran mp
    LEFT JOIN jadwal j  ON j.idm  = mp.idm
    LEFT JOIN kelas  k  ON k.idk  = j.idk
    GROUP BY mp.idm, mp.nama_mp
    ORDER BY mp.nama_mp ASC
");
$pelajaranList = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header"><i class="fa fa-book"></i> <strong>Data Mata Pelajaran</strong></h3>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <i class="fa fa-list"></i> Data Mata Pelajaran
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                        <thead>
                            <tr>
                                <th class="text-center"><i class="fa fa-hashtag"></i> No</th>
                                <th class="text-center"><i class="fa fa-book-open"></i> Mata Pelajaran</th>
                                <th class="text-center"><i class="fa fa-school"></i> Kelas</th>
                                <th class="text-center"><i class="fa fa-cogs"></i> Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($pelajaranList)): ?>
                                <tr>
                                    <td colspan="4" class="text-center">Tidak ada data mata pelajaran.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($pelajaranList as $no => $rs): ?>
                                    <tr class="odd gradeX">
                                        <td class="text-center"><?php echo $no + 1; ?></td>
                                        <td><?php echo htmlspecialchars($rs['nama_mp']); ?></td>
                                        <td><?php echo htmlspecialchars($rs['nama_kelas'] ?? '-'); ?></td>
                                        <td class="text-center">
                                            <a href="media.php?module=absen&idm=<?php echo urlencode($rs['idm']); ?>">
                                                <button type="button" class="btn btn-success">Absen</button>
                                            </a>
                                            <a href="media.php?module=input_pelajaran&act=edit_pelajaran&idm=<?php echo urlencode($rs['idm']); ?>">
                                                <button type="button" class="btn btn-info">Edit</button>
                                            </a>
                                            <a href="module/simpan.php?act=hapus_pelajaran&idm=<?php echo urlencode($rs['idm']); ?>"
                                               onclick="return confirm('Yakin ingin menghapus mata pelajaran ini?')">
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