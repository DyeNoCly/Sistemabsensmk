            <div class="row">
                <div class="col-lg-12">
					<h3 class="page-header"><strong>Input Data Absensi</h3>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <?php
                            $idj = $_GET['idj'] ?? '';
                            $stmt = $pdo->prepare("SELECT * FROM jadwal WHERE idj = ?");
                            $stmt->execute([$idj]);
                            $arrayj = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

                            $stmt = $pdo->prepare("SELECT * FROM mata_pelajaran WHERE idm = ?");
                            $stmt->execute([$arrayj['idm'] ?? '']);
                            $arraymp = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

                            echo "Data Siswa";

                            $stmt = $pdo->prepare("SELECT * FROM kelas WHERE idk = ?");
                            $stmt->execute([$arrayj['idk'] ?? '']);
                            $rsj = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

                            echo "Kelas {$rsj['nama']} | {$arraymp['nama_mp']}";
                            ?>
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive">
                           <form method="post" role="form" action="././module/simpan.php?act=input_absen&idj=<?php echo $idj ?>&tanggal=<?php echo date("Y-m-d") ?>&kelas=<?php echo $arrayj['idk'] ?>">
                                <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th class="text-center">NIS</th>
                                            <th class="text-center">Nama</th>
                                            <th class="text-center">Keterangan</th>

                                        </tr>
                                    </thead>
                                    <tbody>
<?php
$no = 1;
$tg = date("Y-m-d");
$stmt = $pdo->prepare("SELECT * FROM siswa WHERE idk = ?");
$stmt->execute([$arrayj['idk'] ?? '']);
while ($rs = $stmt->fetch(PDO::FETCH_ASSOC)) {
	$stmt2 = $pdo->prepare("SELECT * FROM absen WHERE ids = ? AND tgl = ? AND idj = ?");
	$stmt2->execute([$rs['ids'], $tg, $idj]);
	$rsa = $stmt2->fetch(PDO::FETCH_ASSOC);
	$conk = $stmt2->rowCount();

	$stmt3 = $pdo->prepare("SELECT * FROM kelas WHERE idk = ?");
	$stmt3->execute([$rs['idk']]);
	$rsw = $stmt3->fetch(PDO::FETCH_ASSOC) ?: [];

	$stmt4 = $pdo->prepare("SELECT * FROM sekolah WHERE id = ?");
	$stmt4->execute([$rsw['id'] ?? '']);
	$rsb = $stmt4->fetch(PDO::FETCH_ASSOC) ?: [];

?>                                        <tr class="odd gradeX">
                                            <td><label style="font-weight:normal;"><?php echo "$rs[nis]";  ?></label></td>
                                            <td><label style="font-weight:normal;"><?php echo "$rs[nama]";  ?></label></td>

                                            <td class="text-center">
                                                                                    <div class="form-group">

<?php
if($conk==0){
?>
                                            <label class="radio-inline">
                                                <input type="radio" name="<?php echo $rs['ids'] ?>" value="A"  >A
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="<?php echo $rs['ids'] ?>" value="I">I
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="<?php echo $rs['ids'] ?>" value="S">S
                                            </label>

                                            <label class="radio-inline">
                                                <input type="radio" name="<?php echo $rs['ids'] ?>" value="H" >H
                                            </label>

                                            <label class="radio-inline">
                                                <input type="radio" name="<?php echo $rs['ids'] ?>" value="H" checked>H
                                            </label>


<?php } ?>

<?php
if($rsa['ket']=="A"){
?>
                                            <label class="radio-inline">
                                                <input type="radio" name="<?php echo $rs['ids'] ?>" value="A" checked >A
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="<?php echo $rs['ids'] ?>" value="I">I
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="<?php echo $rs['ids'] ?>" value="S">S
                                            </label>

                                            <label class="radio-inline">
                                                <input type="radio" name="<?php echo $rs['ids'] ?>" value="H" >H
                                            </label>

                                            <label class="radio-inline">
                                                <input type="radio" name="<?php echo $rs['ids'] ?>" value="N" >N
                                            </label>


<?php } ?>
<?php
if($rsa['ket']=="I"){
?>
                                            <label class="radio-inline">
                                                <input type="radio" name="<?php echo $rs['ids'] ?>" value="A"  >A
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="<?php echo $rs['ids'] ?>" value="I" checked>I
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="<?php echo $rs['ids'] ?>" value="S">S
                                            </label>

                                            <label class="radio-inline">
                                                <input type="radio" name="<?php echo $rs['ids'] ?>" value="H" >H
                                            </label>

                                            <label class="radio-inline">
                                                <input type="radio" name="<?php echo $rs['ids'] ?>" value="N" >N
                                            </label>


<?php } ?>
<?php
if($rsa['ket']=="S"){
?>
                                            <label class="radio-inline">
                                                <input type="radio" name="<?php echo $rs['ids'] ?>" value="A"  >A
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="<?php echo $rs['ids'] ?>" value="I" >I
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="<?php echo $rs['ids'] ?>" value="S" checked>S
                                            </label>

                                            <label class="radio-inline">
                                                <input type="radio" name="<?php echo $rs['ids'] ?>" value="H" >H
                                            </label>

                                            <label class="radio-inline">
                                                <input type="radio" name="<?php echo $rs['ids'] ?>" value="N" >N
                                            </label>


<?php } ?>
<?php
if($rsa['ket']=="M"){
?>
                                            <label class="radio-inline">
                                                <input type="radio" name="<?php echo $rs['ids'] ?>" value="A"  >A
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="<?php echo $rs['ids'] ?>" value="I" >I
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="<?php echo $rs['ids'] ?>" value="S" >S
                                            </label>

                                            <label class="radio-inline">
                                                <input type="radio" name="<?php echo $rs['ids'] ?>" value="H" checked>H
                                            </label>

                                            <label class="radio-inline">
                                                <input type="radio" name="<?php echo $rs['ids'] ?>" value="N" >N
                                            </label>


<?php } ?>
<?php
if($rsa['ket']=="N"){
?>
                                            <label class="radio-inline">
                                                <input type="radio" name="<?php echo $rs['ids'] ?>" value="A"  >A
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="<?php echo $rs['ids'] ?>" value="I" >I
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="<?php echo $rs['ids'] ?>" value="S" >S
                                            </label>

                                            <label class="radio-inline">
                                                <input type="radio" name="<?php echo $rs['ids'] ?>" value="H" >H
                                            </label>

                                            <label class="radio-inline">
                                                <input type="radio" name="<?php echo $rs['ids'] ?>" value="N" checked >N
                                            </label>


<?php } ?>


                                        </div>

                                            </td>

                                        </tr>
<?php
}
?>
                                    </tbody>
                                </table>
                                        <button type="submit" class="btn btn-success">Simpan Data Absen</button>

</form>
                            </div>
                            <!-- /.table-responsive -->
<br>
                            <div class="well">
                                <h4>Keterangan Absensi</h4>
                                <p>A = Tidak Masuk Tanpa Keterangan</p>
                                <p>I = Tidak Masuk Ada Surat Ijin Atau Pemberitahuan</p>
                                <p>S = Tidak Masuk Ada Surat Dokter Atau Pemberitahuan</p>
                                <p>H = Hadir</p>
                                <p>N = Belum di Absen</p>

                            </div>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
