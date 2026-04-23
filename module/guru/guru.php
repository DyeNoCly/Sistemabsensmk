<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header"><i class="fa fa-user-tie"></i> <strong>Data Guru</strong></h3>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <i class="fa fa-list"></i> Data Guru
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                        <thead>
                            <tr>
                                <th class="text-center"><i class="fa fa-id-card"></i> NIP</th>
                                <th class="text-center" width="50%"><i class="fa fa-user"></i> Nama</th>
                                <th class="text-center"><i class="fa fa-venus-mars"></i> JK</th>
                                <th class="text-center"><i class="fa fa-cogs"></i> Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $pdo->query("SELECT * FROM guru");
                            $gurus = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            if (empty($gurus)): ?>
                                <tr>
                                    <td colspan="4" class="text-center">Tidak ada data guru.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($gurus as $rs): ?>
                                    <tr class="odd gradeX">
                                        <td><?php echo htmlspecialchars($rs['nip']); ?></td>
                                        <td><?php echo htmlspecialchars($rs['nama']); ?></td>
                                        <td class="text-center">
                                            <?php echo $rs['jk'] === 'L' ? 'Laki - Laki' : 'Perempuan'; ?>
                                        </td>
                                        <td class="text-center">
                                            <a href="media.php?module=detail_guru&idg=<?php echo urlencode($rs['idg']); ?>">
                                                <button type="button" class="btn btn-warning">Detail</button>
                                            </a>
                                            <a href="media.php?module=input_guru&act=edit_guru&idg=<?php echo urlencode($rs['idg']); ?>">
                                                <button type="button" class="btn btn-info">Edit</button>
                                            </a>
                                            <a href="module/simpan.php?act=hapus_guru&idg=<?php echo urlencode($rs['idg']); ?>"
                                               onclick="return confirm('Yakin ingin menghapus guru ini?')">
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