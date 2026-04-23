<?php

// hitung jumlah absensi
$hadir = $pdo->query("SELECT COUNT(*) FROM absensi WHERE status='H'")->fetchColumn();
$izin  = $pdo->query("SELECT COUNT(*) FROM absensi WHERE status='I'")->fetchColumn();
$alpha = $pdo->query("SELECT COUNT(*) FROM absensi WHERE status='A'")->fetchColumn();

?>

<div class="row">
<div class="col-lg-12">
<h3 class="page-header"><strong>Statistik Absensi</strong></h3>
</div>
</div>

<div class="row">

<div class="col-lg-6">
<canvas id="chartAbsensi"></canvas>
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

var ctx = document.getElementById('chartAbsensi');

new Chart(ctx, {

type: 'pie',

data: {
labels: ['Hadir','Izin','Alpha'],
datasets: [{
data: [<?php echo $hadir ?>, <?php echo $izin ?>, <?php echo $alpha ?>],
backgroundColor: [
'#28a745',
'#ffc107',
'#dc3545'
]
}]
}

});

</script>