            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Data User</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            Data User
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th class="text-center">User Name</th>
                                            <th class="text-center">Level</th>
                                            <th class="text-center">Sekolah</th>
                                            <th class="text-center">Aksi</th>

                                        </tr>
                                    </thead>
                                    <tbody>
<?php
$no = 1;
if ($_SESSION['level'] == "admin_guru") {
	$stmt = $pdo->prepare("SELECT * FROM user WHERE idu = ?");
	$stmt->execute([$_SESSION['idu'] ?? '']);
} else {
	$stmt = $pdo->query("SELECT * FROM user WHERE idu <> '1'");
}
while ($rs = $stmt->fetch(PDO::FETCH_ASSOC)) {
	$stmt2 = $pdo->prepare("SELECT * FROM sekolah WHERE id = ?");
	$stmt2->execute([$rs['id']]);
	$rsa = $stmt2->fetch(PDO::FETCH_ASSOC) ?: [];


?>                                        <tr class="odd gradeX">
                                            <td><?php echo"$rs[nama]";  ?></td>
                                            <td class="text-center"><?php echo"$rs[level]";  ?></td>
                                            <td class="text-center"><?php echo"$rsa[nama]";  ?></td>

                                        <td class="text-center"><a href="./././media.php?module=input_user&act=edit_user&idu=<?php echo $rs['idu'] ?>"><button type="button" class="btn btn-info">Edit</button>
 <?php 	if($_SESSION['level']!="admin_guru"){ ?>
 <a href="././module/simpan.php?act=hapus_user&idu=<?php echo $rs['idu'] ?>"><button type="button" class="btn btn-danger">Hapus</button></a></td>
<?php } ?>

                                        </tr>
<?php
}
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
