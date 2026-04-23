<?php
$acuan = $_GET['idj'] ?? '';

$stmt = $pdo->prepare("SELECT * FROM jadwal WHERE idj = ?");
$stmt->execute([$acuan]);
$rss = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT * FROM siswa WHERE nis = ?");
$stmt->execute([$uidi]);
$siswa = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT * FROM mata_pelajaran WHERE idm = ?");
$stmt->execute([$rss['idm']]);
$nama_mp = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT * FROM hari WHERE idh = ?");
$stmt->execute([$rss['idh']]);
$nama_hari = $stmt->fetch(PDO::FETCH_ASSOC);

$pecah = explode(' ', $tgl_lengkap);
$satu   = $pecah[0] ?? '';
$dua    = $pecah[1] ?? '';
$tahun1 = $pecah[2] ?? '';

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
                        echo "Tahun Ajaran $tahun1 - $tahun2 ($nama_mp[nama_mp])";
                      }
                      else {
                        echo "Tahun Ajaran $tahun2 - $tahun1 ($nama_mp[nama_mp])";
                      }
                      ?>
                      </strong>
                    </h3>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            Data Absensi Siswa
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="table-responsive">
<?php
$stmt = $pdo->prepare("SELECT DISTINCT tanggal FROM absensi WHERE nis = ? AND idm = ? ORDER BY tanggal ASC");
$stmt->execute([$siswa['nis'], $rss['idm']]);
$sqlabsen = $stmt;
 ?>
<table class="table table-striped table-bordered table-hover">
  <tr>
    <td colspan="2" class="text-center info"><?php echo "<b>$nama_hari[hari], $rss[jam_mulai] - $rss[jam_selesai]</b>";?></td>
  </tr>
  <tr>
    <td class="success text-center" rowspan="2"><b>Tanggal (TGL/BLN)</b></td>
    <td class="text-center success"><b>Siswa</b></td>
  </tr>
  <tr>
    <td class="text-center warning"><?php echo "<b>$usre | Kelas : $rs[nama]</b>"; ?></td>
  </tr>
  <?php
  while ($tglnya = $sqlabsen->fetch(PDO::FETCH_ASSOC)) {
    $pecah = explode('-', $tglnya['tanggal']);
    $satu  = $pecah[0] ?? '';
    $dua   = $pecah[1] ?? '';
    $tiga  = $pecah[2] ?? '';
  ?>
  <tr>
    <td class="text-center warning"><?php echo "<b>$tiga/$dua</b>"; ?></td>
    <?php
    $stmt2 = $pdo->prepare("SELECT status FROM absensi WHERE nis = ? AND idm = ? AND tanggal = ?");
    $stmt2->execute([$siswa['nis'], $rss['idm'], $tglnya['tanggal']]);
    while ($ketnya = $stmt2->fetch(PDO::FETCH_ASSOC)) {
        $display_status = $ketnya['status'];
        if ($display_status == 'M') $display_status = 'H';
    ?>
    <td class="text-center"><?php echo htmlspecialchars($display_status); ?></td>
    <?php } ?>
  </tr>
<?php } ?>
</table>

                            </div>


                            <!-- /.table-responsive -->
                            <div class="well">
                                <h4>Keterangan Absensi</h4>
                                <p>H = Hadir</p>
                                <p>I = Tidak Masuk Ada Surat Ijin Atau Pemberitahuan</p>
                                <p>S = Tidak Masuk Ada Surat Dokter Atau Pemberitahuan</p>
                                <p>A = Tidak Masuk Tanpa Keterangan</p>
                            </div>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
