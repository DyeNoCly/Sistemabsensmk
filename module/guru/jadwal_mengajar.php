            <div class="row">
                <div class="col-lg-12">
					<h3 class="page-header"><strong>Jadwal Mengajar</strong></h3>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <?php
            	$stmt = $pdo->prepare("SELECT * FROM guru WHERE nip = ?");
            	$stmt->execute([$uidi]);
            	$rs = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
             ?>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th class="text-center">No</th>
                                            <th class="text-center">Hari</th>
                                            <th class="text-center">Jam</th>
                                            <th class="text-center">Kelas</th>
                                            <th class="text-center">Mata Pelajaran</th>
                                            <th class="text-center">Aksi</th>

                                        </tr>
                                    </thead>
                                    <tbody>
<?php
$no = 1;
	$stmt = $pdo->prepare("SELECT jadwal.idj, hari.hari, kelas.nama AS nama_kelas, guru.idg AS id_guru, mata_pelajaran.nama_mp, jadwal.jam_selesai, jadwal.jam_mulai
	FROM jadwal
	JOIN hari ON jadwal.idh = hari.idh
	JOIN kelas ON jadwal.idk = kelas.idk
	JOIN guru ON jadwal.idg = guru.idg
	JOIN mata_pelajaran ON jadwal.idm = mata_pelajaran.idm
	WHERE guru.idg = ?
	ORDER BY jadwal.idh ASC");
	$stmt->execute([$rs['idg']]);
	while($rs = $stmt->fetch(PDO::FETCH_ASSOC)) {

?>                                        <tr class="odd gradeX">
                                            <td><?php echo"$no";  ?></td>
                                            <td ><?php echo"$rs[hari]";  ?></td>
                                            <td ><?php echo"$rs[jam_mulai] - $rs[jam_selesai]";  ?></td>
                                            <td ><?php echo"$rs[nama_kelas]";  ?></td>
                                            <td ><?php echo"$rs[nama_mp]";  ?></td>
                                        <td class="text-center"><a href="./././media.php?module=absen&idj=<?php echo $rs['idj'] ?>"><button type="button" class="btn btn-info">Mulai Absen</button></a>
                                          <a href="./././media.php?module=rekap_g&idj=<?php echo $rs['idj'] ?>"><button type="button" class="btn btn-warning">Rekap Absen</button></a>
                                        </td>
                                        </tr>
<?php
$no++;}
?>
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.table-responsive -->
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
