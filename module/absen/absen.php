<?php
$idm     = $_GET['idm'] ?? '';
$tanggal = date('Y-m-d');

// Fetch siswa
$stmt = $pdo->query("SELECT nis, nama FROM siswa ORDER BY nama ASC");
$siswaList = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch mata pelajaran name for display
$stmtMp = $pdo->prepare("SELECT nama_mp FROM mata_pelajaran WHERE idm = ? LIMIT 1");
$stmtMp->execute([$idm]);
$mp = $stmtMp->fetch(PDO::FETCH_ASSOC);
$namaMp = htmlspecialchars($mp['nama_mp'] ?? '');
?>

<style>
.btn-absen.active {
    border: 3px solid black;
    opacity: 0.7;
}
</style>

<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header">
            <strong>Absensi Siswa</strong>
            <?php if ($namaMp): ?>
                <small> — <?php echo $namaMp; ?></small>
            <?php endif; ?>
        </h3>
    </div>
</div>

<div class="row">
    <div class="col-lg-12" style="margin-bottom: 16px;">
        <a href="media.php?module=mata_pelajaran" class="btn btn-default">
            <i class="fa fa-arrow-left"></i> Kembali ke Data Mata Pelajaran
        </a>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th class="text-center">NIS</th>
                <th class="text-center">Nama</th>
                <th class="text-center">Absensi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            foreach ($siswaList as $s):
                $cek = $pdo->prepare("SELECT status FROM absensi WHERE nis=? AND idm=? AND tanggal=?");
                $cek->execute([$s['nis'], $idm, $tanggal]);
                $dataAbsen = $cek->fetch(PDO::FETCH_ASSOC);
                $status    = $dataAbsen['status'] ?? '';
            ?>
                <tr>
                    <td class="text-center"><?php echo $no++; ?></td>
                    <td class="text-center"><?php echo htmlspecialchars($s['nis']); ?></td>
                    <td><?php echo htmlspecialchars($s['nama']); ?></td>
                    <td class="text-center">
                        <button class="btn btn-success btn-absen <?php echo $status === 'H' ? 'active' : ''; ?>"
                            data-nis="<?php echo htmlspecialchars($s['nis']); ?>"
                            data-status="H">
                            Hadir
                        </button>
                        <button class="btn btn-warning btn-absen <?php echo $status === 'I' ? 'active' : ''; ?>"
                            data-nis="<?php echo htmlspecialchars($s['nis']); ?>"
                            data-status="I">
                            Izin
                        </button>
                        <button class="btn btn-danger btn-absen <?php echo $status === 'A' ? 'active' : ''; ?>"
                            data-nis="<?php echo htmlspecialchars($s['nis']); ?>"
                            data-status="A">
                            Alpha
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="row">
    <div class="col-lg-12" style="margin-top: 8px; margin-bottom: 24px;">
        <a href="media.php?module=mata_pelajaran" class="btn btn-default">
            <i class="fa fa-arrow-left"></i> Kembali ke Data Mata Pelajaran
        </a>
    </div>
</div>

<script>
$(document).on("click", ".btn-absen", function () {
    var btn     = $(this);
    var nis     = btn.data("nis");
    var status  = btn.data("status");
    var idm     = "<?php echo htmlspecialchars($idm); ?>";
    var tanggal = "<?php echo $tanggal; ?>";

    $.ajax({
        url:  "module/simpan_absen_ajax.php",
        type: "POST",
        data: { nis: nis, idm: idm, tanggal: tanggal, status: status },
        success: function () {
            btn.closest("td").find("button").removeClass("active");
            btn.addClass("active");
        }
    });
});
</script>