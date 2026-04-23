          <div class="row">
                <div class="col-lg-12">
					<h3 class="page-header"><strong>Data Siswa</strong></h3>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            Data Siswa
                        </div>
                        <div class="panel-body">
                            <div class="row">
<?php
                            	$stmt = $pdo->prepare("SELECT * FROM siswa WHERE nis = ?");
								$stmt->execute([$_SESSION['idu']]);
								$rs = $stmt->fetch(PDO::FETCH_ASSOC);
?>
                                    <form method="post" role="form" action="././module/simpan.php?act=siswa_det">
<input type="hidden" name="id" value="<?php echo htmlspecialchars($rs['ids']); ?>" />

                                <div class="col-lg-6">
                                        <fieldset disabled>

                                        <div class="form-group">
                                            <label>NIS</label>
                                            <input class="form-control"  placeholder="Nis" name="nis" value="<?php echo htmlspecialchars($rs['nis']); ?>" >
                                        </div>
                                        <div class="form-group">
                                            <label>Nama</label>
                                            <input class="form-control" placeholder="Nama" name="nama" value="<?php echo htmlspecialchars($rs['nama']); ?>">
                                        </div>
                                        <div class="form-group">

                                           <label>Jenis Kelamin</label>
        <?php if($rs['jk']=="L"){ ?>
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="jk" value="L"
                                                    checked>Laki - Laki
                                                </label>
                                            </div>
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="jk" value="P">
                                                    Perempuan
                                                </label>
                                            </div>
                                        </div>
<?php } ?>
        <?php if($rs['jk']=="P"){ ?>
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="jk" value="L"
                                                    >Laki - Laki
                                                </label>
                                            </div>
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="jk" value="P" checked>
                                                    Perempuan
                                                </label>
                                            </div>
                                        </div>
<?php } ?>


                                        <div class="form-group">
                                            <label>Alamat</label>
                                            <textarea class="form-control" placeholder="Alamat" name="alamat" rows="3"><?php echo htmlspecialchars($rs['alamat']); ?></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label>Kelas</label>
                                            <select class="form-control" name="kelas">
  <?php
 	$stmtc = $pdo->query("SELECT * FROM kelas");
	while($rsc = $stmtc->fetch(PDO::FETCH_ASSOC)){
	$stmtla = $pdo->prepare("SELECT * FROM sekolah WHERE id = ?");
	$stmtla->execute([$rsc['id']]);
	$rsa = $stmtla->fetch(PDO::FETCH_ASSOC);
if($_SESSION['level']=="admin_guru"){
if($rsa['id']==$_SESSION['id']){

if($rs['idk']==$rsc['idk']){
	echo "<option value='" . htmlspecialchars($rsc['idk']) . "' selected>" . htmlspecialchars($rsc['nama']) . "</option>";
}else{
	echo "<option value='" . htmlspecialchars($rsc['idk']) . "'>" . htmlspecialchars($rsc['nama']) . "</option>";

}
}
}else{
if($rs['idk']==$rsc['idk']){
	echo "<option value='" . htmlspecialchars($rsc['idk']) . "' selected>" . htmlspecialchars($rsc['nama']) . "</option>";
}else{
	echo "<option value='" . htmlspecialchars($rsc['idk']) . "'>" . htmlspecialchars($rsc['nama']) . "</option>";

}

}
}?>
                                          </select>
                                        </div>
                                        <div class="form-group input-group">
                                            <span class="input-group-addon">+62</span>
                                            <input type="text" class="form-control" placeholder="No Telepon" name="tlp" value="<?php echo htmlspecialchars($rs['tlp']); ?>">
                                        </div>
</fieldset>
</div>

                                <div class="col-lg-6">
                              <fieldset disabled>

                                        <div class="form-group">
                                            <label>Nama Ayah</label>
                                            <input class="form-control" placeholder="Nama" name="bapak" value="<?php echo htmlspecialchars($rs['bapak']); ?>">
                                        </div>
                                        <div class="form-group">
                                            <label>Pekerjaan</label>
                                            <input class="form-control" placeholder="Pekerjaan" name="k_bapak" value="<?php echo htmlspecialchars($rs['k_bapak']); ?>">
                                        </div>
                                        <div class="form-group">
                                            <label>Nama Ibu</label>
                                            <input class="form-control" placeholder="Nama" name="ibu" value="<?php echo htmlspecialchars($rs['ibu']); ?>">
                                        </div>
                                        <div class="form-group">
                                            <label>Pekerjaan</label>
                                            <input class="form-control" placeholder="Pekerjaan" name="k_ibu" value="<?php echo htmlspecialchars($rs['k_ibu']); ?>">
                                        </div>
</fieldset>
                                        <div class="form-group">
                                            <label>Ganti Password</label>
                                            <input class="form-control" placeholder="Password baru" name="pass">
                                        </div>


                                        <button type="submit" class="btn btn-success">Simpan</button>
                                </div>
                                <!-- /.col-lg-6 (nested) -->
                                    </form>

                            </div>
                            <!-- /.row (nested) -->
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
