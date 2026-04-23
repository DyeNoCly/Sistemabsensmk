            <div class="row">
                <div class="col-lg-12">
					<h3 class="page-header"><strong>Jadwal Belajar</strong></h3>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <?php
            	$stmt = $pdo->prepare("SELECT * FROM kelas WHERE idk = ?");
            	$stmt->execute([$klss]);
            	$rs = $stmt->fetch(PDO::FETCH_ASSOC);
             ?>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                      <div class="panel-heading">
                          Jadwal Belajar Kelas <?php echo htmlspecialchars($rs['nama']); ?>
                      </div>
                      <ul class="nav nav-tabs">
                    	  <li role="presentation"><a href="media.php?module=siswa_senin">Senin</a></li>
                    	  <li role="presentation" class="active"><a href="media.php?module=siswa_selasa">Selasa</a></li>
                    	  <li role="presentation"><a href="media.php?module=siswa_rabu">Rabu</a></li>
                        <li role="presentation"><a href="media.php?module=siswa_kamis">Kamis</a></li>
                        <li role="presentation"><a href="media.php?module=siswa_jumat">Jum'at</a></li>
                    	</ul>
                      <br>
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th class="text-center">No</th>
                                            <th class="text-center">Hari</th>
                                            <th class="text-center">Jam</th>
                                            <th class="text-center">Guru Pengajar</th>
                                            <th class="text-center">Mata Pelajaran</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
<?php
$no=1;
	$stmt = $pdo->prepare("SELECT jadwal.idj, hari.hari, guru.nama AS nama_guru, mata_pelajaran.nama_mp, jadwal.jam_selesai, jadwal.jam_mulai FROM jadwal, hari, guru, mata_pelajaran WHERE jadwal.idh = hari.idh AND jadwal.idg = guru.idg AND jadwal.idm = mata_pelajaran.idm AND jadwal.idh = 2 AND jadwal.idk = ? ORDER BY jadwal.jam_mulai");
	$stmt->execute([$rs['idk']]);
	while($row = $stmt->fetch(PDO::FETCH_ASSOC)){

?>                                        <tr class="odd gradeX">
                                            <td><?php echo $no; ?></td>
                                            <td><?php echo htmlspecialchars($row['hari']); ?></td>
                                            <td><?php echo htmlspecialchars($row['jam_mulai']) . ' - ' . htmlspecialchars($row['jam_selesai']); ?></td>
                                            <td><?php echo htmlspecialchars($row['nama_guru']); ?></td>
                                            <td><?php echo htmlspecialchars($row['nama_mp']); ?></td>
                                            <td><a href="./././media.php?module=rekap_s&idj=<?php echo $row['idj']; ?>"><button type="button" class="btn btn-primary">Data Absen</button></a></td>
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
