<?php
$stmt = $pdo->query("SELECT id, kode, nama, alamat FROM sekolah ORDER BY nama ASC");
$sekolahList = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header"><strong>Data Sekolah</strong></h3>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-primary">

            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                        <thead>
                            <tr>
                                <th class="text-center">Kode</th>
                                <th class="text-center">Nama Sekolah</th>
                                <th class="text-center">Alamat</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($sekolahList)): ?>
                                <tr>
                                    <td colspan="4" class="text-center">Tidak ada data sekolah.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($sekolahList as $rs): ?>
                                    <tr class="odd gradeX">
                                        <td><?php echo htmlspecialchars($rs['kode']); ?></td>
                                        <td><?php echo htmlspecialchars($rs['nama']); ?></td>
                                        <td><?php echo htmlspecialchars($rs['alamat']); ?></td>
                                        <td class="text-center">
                                            <a href="media.php?module=input_sekolah&act=edit_sekolah&id=<?php echo urlencode($rs['id']); ?>">
                                                <button type="button" class="btn btn-info">Edit</button>
                                            </a>
                                            <a href="module/simpan.php?act=hapus_sekolah&id=<?php echo urlencode($rs['id']); ?>"
                                               onclick="return confirm('Yakin ingin menghapus sekolah ini?')">
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