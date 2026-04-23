<?php
$acuan = $_GET['idj'] ?? '';

$stmt = $pdo->prepare("SELECT * FROM jadwal WHERE idj = ?");
$stmt->execute([$acuan]);
$rss = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT * FROM kelas WHERE idk = ?");
$stmt->execute([$rss['idk']]);
$kss = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT * FROM mata_pelajaran WHERE idm = ?");
$stmt->execute([$rss['idm']]);
$nama_mp = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT * FROM hari WHERE idh = ?");
$stmt->execute([$rss['idh']]);
$nama_hari = $stmt->fetch(PDO::FETCH_ASSOC);

$pecah = explode(" ", $tgl_lengkap);
$satu=$pecah[0];
$dua=$pecah[1];
$tahun1=$pecah[2];

if ($dua=="Juli" || $dua=="Agustus" || $dua=="September" || $dua=="Oktober" || $dua=="November" || $dua=="Desember") {
  $tahun2=$tahun1 + 1;
}
else {
  $tahun2=$tahun1 - 1;
}
?>
            <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header">
                      <strong>
                      <?php
                      if ($dua=="Juli" || $dua=="Agustus" || $dua=="September" || $dua=="Oktober" || $dua=="November" || $dua=="Desember") {
                        echo "Tahun Ajaran $tahun1 - $tahun2";
                      }
                      else {
                        echo "Tahun Ajaran $tahun2 - $tahun1";
                      }
                      ?>
                      </strong>
                      <form action="module/laporan/guru/cetak_rekap.php" method="post">
                        <input type="hidden" name="idj" value="<?php echo $acuan; ?>">
                        <input type="hidden" name="tgl_lengkap" value="<?php echo $tgl_lengkap; ?>">
                        <input style="float:right; margin-top:-40px;" class="btn btn-success btn-lg" type="submit" name="cetak" value="Cetak">
                      </form>
                    </h3>

                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            Data Absensi Kelas <?php echo "<b>$kss[nama] | $nama_mp[nama_mp]</b>";
?>
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="table-responsive">
<?php
$stmtAbs = $pdo->prepare("SELECT DISTINCT tanggal FROM absensi WHERE idm = ? ORDER BY tanggal ASC");
$stmtAbs->execute([$rss['idm']]);
$dateList = $stmtAbs->fetchAll(PDO::FETCH_ASSOC);
$jumlahtanggal = count($dateList);
$jumlahkolom = $jumlahtanggal + 1;

$siswas = $pdo->prepare("SELECT nis, nama FROM siswa WHERE idk = ? ORDER BY nama ASC");
$siswas->execute([$rss['idk']]);

$stmtStatus = $pdo->prepare("SELECT status FROM absensi WHERE nis = ? AND idm = ? AND tanggal = ?");
 ?>
<table class="table table-striped table-bordered table-hover">
  <tr>
    <td colspan="<?php echo $jumlahkolom; ?>" class="text-center info"><?php echo "<b>$nama_hari[hari], $rss[jam_mulai] - $rss[jam_selesai]</b>";?></td>
  </tr>
  <tr>
    <td class="success text-center" rowspan="2"><b>Siswa</b></td>
    <td colspan="<?php echo $jumlahtanggal; ?>" class="text-center success"><b>Tanggal (TGL/BLN)</b></td>
  </tr>
  <tr>
    <?php
    foreach ($dateList as $tglnya) {
      $pecah = explode('-', $tglnya['tanggal']);
      $tiga = $pecah[2] ?? '';
      $dua  = $pecah[1] ?? '';
    ?>
    <td class="text-center warning"><?php echo "<b>$tiga/$dua</b>"; ?></td>
<?php } ?>
  </tr>
  <?php
  while ($siswanya = $siswas->fetch(PDO::FETCH_ASSOC)) {
  ?>
  <tr>
    <td class="text-center warning"><?php echo htmlspecialchars($siswanya['nama']); ?></td>
    <?php
    foreach ($dateList as $tglnya) {
      $stmtStatus->execute([$siswanya['nis'], $rss['idm'], $tglnya['tanggal']]);
      $statusRow = $stmtStatus->fetch(PDO::FETCH_ASSOC);
      $status = $statusRow['status'] ?? '-';
    ?>
    <td class="text-center"><?php echo htmlspecialchars($status); ?></td>
    <?php } ?>
  </tr>
<?php } ?>
</table>

                            </div>


                            <!-- /.table-responsive -->
                            <div class="well">
                                <h4>Keterangan Absensi</h4>
                                <p>A = Tidak Masuk Tanpa Keterangan<br>
                                <p>I = Tidak Masuk Ada Surat Ijin Atau Pemberitahuan</p>
                                <p>S = Tidak Masuk Ada Surat Dokter Atau Pemberitahuan</p>
                                <p>M = Hadir</p>
                            </div>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
