<?php
session_start();
if(!empty($_SESSION['nama'])){
$uidi=$_SESSION['idu'];
$usre=$_SESSION['nama'];
$level=$_SESSION['level'];
$klss=$_SESSION['idk'];
$ortu=$_SESSION['ortu'];
$idd=$_SESSION['id'];

include "../../../config/conn.php";
include "../../../config/fungsi.php";
require ("../../../config/html2pdf/html2pdf.class.php");
$filename="Laporan_Absensi_Kelas.pdf";
$content = ob_get_clean();
$acuan = $_POST['idj'] ?? '';
$tgl_lengkap = $_POST['tgl_lengkap'] ?? '';

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
  $tahun_ajaran="Tahun Ajaran $tahun1 - $tahun2";
}
else {
  $tahun2=$tahun1 - 1;
  $tahun_ajaran="Tahun Ajaran $tahun2 - $tahun1";
}

$stmtAbs = $pdo->prepare("SELECT DISTINCT tanggal FROM absensi WHERE idm = ? ORDER BY tanggal ASC");
$stmtAbs->execute([$rss['idm']]);
$dateList = $stmtAbs->fetchAll(PDO::FETCH_ASSOC);
$jumlahtanggal = count($dateList);
$jumlahkolom = $jumlahtanggal + 1;

$stmtSiswa = $pdo->prepare("SELECT nis, nama FROM siswa WHERE idk = ? ORDER BY nama ASC");
$stmtSiswa->execute([$rss['idk']]);

$content = "<h3>Laporan Data Absensi Kelas $kss[nama] | $nama_mp[nama_mp]</h3>

				    <p><b>$tahun_ajaran</b><br>$nama_hari[hari], $rss[jam_mulai] - $rss[jam_selesai]</p>
				    <table cellpadding=0 cellspacing=0>
              <tr>
                <td align='center' style='border: 1px solid #000; padding: 5px; font-size: 11.5px; background-color:#d0e9c6;' rowspan=2><b>Siswa</b></td>
                <td align='center' style='border: 1px solid #000; padding: 5px; font-size: 11.5px; background-color:#d0e9c6;' colspan='$jumlahtanggal'><b>Tanggal (TGL/BLN)</b></td>
              </tr>
              <tr>
            ";
foreach ($dateList as $tglnya) {
  $pecah = explode('-', $tglnya['tanggal']);
  $tiga = $pecah[2] ?? '';
  $dua  = $pecah[1] ?? '';
  $content .= "<td align='center' style='border: 1px solid #000; padding: 5px; font-size: 11.5px; background-color:#faf2cc;'><b>$tiga/$dua</b></td>";
}
$content .= "</tr>";

while ($siswanya = $stmtSiswa->fetch(PDO::FETCH_ASSOC)) {
  $content .= "<tr>
              <td align='center' style='border: 1px solid #000; padding: 5px; font-size: 11.5px; background-color:#faf2cc;'>" . htmlspecialchars($siswanya['nama']) . "</td>";

  foreach ($dateList as $tglnya) {
    $stmtStatus = $pdo->prepare("SELECT status FROM absensi WHERE nis = ? AND idm = ? AND tanggal = ?");
    $stmtStatus->execute([$siswanya['nis'], $rss['idm'], $tglnya['tanggal']]);
    $statusnya = $stmtStatus->fetch(PDO::FETCH_ASSOC);
    $statusText = $statusnya['status'] ?? '-';
    if ($statusText == 'M') $statusText = 'H';
    $content .= "<td align='center' style='border: 1px solid #000; padding: 5px; font-size: 11.5px;'>" . htmlspecialchars($statusText) . "</td>";
  }

  $content .= "</tr>";
}
$content .= "</table>
              <br>
              <br>
              <b>Keterangan Absensi</b>
              <p>A = Tidak Masuk Tanpa Keterangan<br>
              I = Tidak Masuk Ada Surat Ijin Atau Pemberitahuan<br>
              S = Tidak Masuk Ada Surat Dokter Atau Pemberitahuan<br>
              H = Hadir</p>
            ";
            // conversion HTML => PDF
            	try
            	{
            		$html2pdf = new HTML2PDF('P','A4','fr', false, 'UTF-8',array(10, 10, 10, 10)); //setting ukuran kertas dan margin pada dokumen anda
            		// $html2pdf->setModeDebug();
            		$html2pdf->setDefaultFont('Arial');
            		$html2pdf->writeHTML($content, isset($_GET['vuehtml']));
            		$html2pdf->Output($filename);
            	}
            	catch(HTML2PDF_exception $e) { echo $e; }
?>


<?php }
else {
  echo "<center><h2>Anda Harus Login Terlebih Dahulu</h2>
    <a href=index.php><b>Klik ini untuk Login</b></a></center>";
}
?>
