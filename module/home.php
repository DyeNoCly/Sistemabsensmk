<?php
$isStudent = isset($_SESSION['level']) && $_SESSION['level'] === 'user';

if ($isStudent) {
    $nis = $_SESSION['idu'];
    $hadir      = $pdo->prepare("SELECT COUNT(*) FROM absensi WHERE nis = ? AND status = 'H'");
    $hadir->execute([$nis]);
    $hadir = $hadir->fetchColumn();

    $izin       = $pdo->prepare("SELECT COUNT(*) FROM absensi WHERE nis = ? AND status = 'I'");
    $izin->execute([$nis]);
    $izin = $izin->fetchColumn();

    $alpha      = $pdo->prepare("SELECT COUNT(*) FROM absensi WHERE nis = ? AND status = 'A'");
    $alpha->execute([$nis]);
    $alpha = $alpha->fetchColumn();
} else {
    $hadir      = $pdo->query("SELECT COUNT(*) FROM absensi WHERE status='H'")->fetchColumn();
    $izin       = $pdo->query("SELECT COUNT(*) FROM absensi WHERE status='I'")->fetchColumn();
    $alpha      = $pdo->query("SELECT COUNT(*) FROM absensi WHERE status='A'")->fetchColumn();
    $totalSiswa = $pdo->query("SELECT COUNT(*) FROM siswa")->fetchColumn();
}

$total  = $hadir + $izin + $alpha;
$pHadir = $total > 0 ? round($hadir / $total * 100) : 0;
$pIzin  = $total > 0 ? round($izin  / $total * 100) : 0;
$pAlpha = $total > 0 ? round($alpha / $total * 100) : 0;
?>

<style>
.dash-stat-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(220px, 1fr));
    gap: 20px;
    align-items: stretch;
    margin-bottom: 30px;
}
.dash-stat-grid.student-mode {
    grid-template-columns: repeat(3, minmax(220px, 1fr));
}
.dash-stat-card {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    border: none;
    border-radius: 20px;
    padding: 25px;
    display: flex;
    align-items: center;
    gap: 20px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}
.dash-stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #667eea, #764ba2);
}
.dash-stat-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.15);
}
.dash-stat-card:nth-child(1)::before { background: linear-gradient(90deg, #1D9E75, #a8e6cf); }
.dash-stat-card:nth-child(2)::before { background: linear-gradient(90deg, #BA7517, #f093fb); }
.dash-stat-card:nth-child(3)::before { background: linear-gradient(90deg, #A32D2D, #ff6b6b); }
.dash-stat-card:nth-child(4)::before { background: linear-gradient(90deg, #185FA5, #667eea); }
.dash-stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    flex-shrink: 0;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
.dash-stat-label {
    font-size: 14px;
    color: #6c757d;
    margin: 0 0 6px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
}
.dash-stat-value {
    font-size: 32px;
    font-weight: 700;
    margin: 0;
    line-height: 1;
}
.dash-stat-sub {
    font-size: 12px;
    margin: 6px 0 0;
    opacity: 0.8;
}
.dash-chart-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}
.dash-chart-card {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    border: none;
    border-radius: 20px;
    padding: 25px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}
.dash-chart-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 30px rgba(0,0,0,0.15);
}
.dash-chart-title {
    font-size: 18px;
    font-weight: 700;
    color: #495057;
    margin: 0 0 20px;
    text-align: center;
    position: relative;
}
.dash-chart-title::after {
    content: '';
    position: absolute;
    bottom: -8px;
    left: 50%;
    transform: translateX(-50%);
    width: 50px;
    height: 3px;
    background: linear-gradient(90deg, #667eea, #764ba2);
    border-radius: 2px;
}
.dash-legend {
    display: flex;
    gap: 20px;
    margin-bottom: 15px;
    flex-wrap: wrap;
    justify-content: center;
}
.dash-legend span {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    font-weight: 500;
}
.dash-legend-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
}
.dash-progress-row {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 16px;
}
.dash-progress-label {
    font-size: 14px;
    color: #495057;
    min-width: 60px;
    font-weight: 500;
}
.dash-progress-bg {
    flex: 1;
    height: 10px;
    background: #e9ecef;
    border-radius: 5px;
    overflow: hidden;
    box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);
}
.dash-progress-fill {
    height: 100%;
    border-radius: 5px;
    transition: width 0.8s ease;
    position: relative;
}
.dash-progress-fill::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    animation: shimmer 2s infinite;
}
@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}
.dash-progress-pct {
    font-size: 13px;
    color: #6c757d;
    min-width: 40px;
    text-align: right;
    font-weight: 600;
}
.dash-rate-block {
    margin-top: 25px;
    padding-top: 20px;
    border-top: 2px solid #e9ecef;
    text-align: center;
}
.dash-rate-block h4 {
    color: #495057;
    font-weight: 600;
    margin-bottom: 10px;
}
@media (max-width: 992px) {
    .dash-stat-grid { grid-template-columns: repeat(2, minmax(220px, 1fr)); }
    .dash-chart-grid { grid-template-columns: 1fr; }
}
@media (max-width: 576px) {
    .dash-stat-grid { grid-template-columns: 1fr; }
    .dash-stat-card {
        padding: 20px;
        flex-direction: column;
        text-align: center;
    }
}
</style>

<!-- ─── Stat Cards ─── -->
<div class="dash-stat-grid <?php echo $isStudent ? 'student-mode' : ''; ?> fade-in-up">

    <div class="dash-stat-card fade-in" style="animation-delay: 0.1s;">
        <div class="dash-stat-icon" style="background: linear-gradient(135deg, #e8f8f2 0%, #a8e6cf 100%);">
            <i class="fa fa-check" style="color:#1D9E75;"></i>
        </div>
        <div>
            <p class="dash-stat-label">Hadir</p>
            <p class="dash-stat-value" style="color:#1D9E75;"><?php echo $hadir; ?></p>
            <p class="dash-stat-sub" style="color:#1D9E75;"><?php echo $pHadir; ?>% dari total</p>
        </div>
    </div>

    <div class="dash-stat-card fade-in" style="animation-delay: 0.2s;">
        <div class="dash-stat-icon" style="background: linear-gradient(135deg, #fef7e8 0%, #f093fb 100%);">
            <i class="fa fa-exclamation" style="color:#BA7517;"></i>
        </div>
        <div>
            <p class="dash-stat-label">Izin</p>
            <p class="dash-stat-value" style="color:#BA7517;"><?php echo $izin; ?></p>
            <p class="dash-stat-sub" style="color:#BA7517;"><?php echo $pIzin; ?>% dari total</p>
        </div>
    </div>

    <div class="dash-stat-card fade-in" style="animation-delay: 0.3s;">
        <div class="dash-stat-icon" style="background: linear-gradient(135deg, #fdecea 0%, #ff6b6b 100%);">
            <i class="fa fa-times" style="color:#A32D2D;"></i>
        </div>
        <div>
            <p class="dash-stat-label">Alpha</p>
            <p class="dash-stat-value" style="color:#A32D2D;"><?php echo $alpha; ?></p>
            <p class="dash-stat-sub" style="color:#A32D2D;"><?php echo $pAlpha; ?>% dari total</p>
        </div>
    </div>

    <?php if (!$isStudent): ?>
        <div class="dash-stat-card fade-in" style="animation-delay: 0.4s;">
            <div class="dash-stat-icon" style="background: linear-gradient(135deg, #e8f0fb 0%, #667eea 100%);">
                <i class="fa fa-users" style="color:#185FA5;"></i>
            </div>
            <div>
                <p class="dash-stat-label">Total Siswa</p>
                <p class="dash-stat-value" style="color:#185FA5;"><?php echo $totalSiswa; ?></p>
                <p class="dash-stat-sub" style="color:#888;">terdaftar</p>
            </div>
        </div>
    <?php endif; ?>

</div>

<!-- ─── Charts ─── -->
<div class="dash-chart-grid fade-in-up">

    <div class="dash-chart-card">
        <p class="dash-chart-title">Distribusi Kehadiran</p>
        <div class="dash-legend">
            <span><span class="dash-legend-dot" style="background:#1D9E75;"></span>Hadir (<?php echo $hadir; ?>)</span>
            <span><span class="dash-legend-dot" style="background:#BA7517;"></span>Izin (<?php echo $izin; ?>)</span>
            <span><span class="dash-legend-dot" style="background:#A32D2D;"></span>Alpha (<?php echo $alpha; ?>)</span>
        </div>
        <div style="position:relative; height:240px;">
            <canvas id="chartDonut"></canvas>
        </div>
    </div>

    <div class="dash-chart-card">
        <p class="dash-chart-title">Persentase Kehadiran</p>

        <div style="margin-top: 8px;">
            <div class="dash-progress-row">
                <span class="dash-progress-label">Hadir</span>
                <div class="dash-progress-bg">
                    <div class="dash-progress-fill" style="width:<?php echo $pHadir; ?>%; background:#1D9E75;"></div>
                </div>
                <span class="dash-progress-pct"><?php echo $pHadir; ?>%</span>
            </div>
            <div class="dash-progress-row">
                <span class="dash-progress-label">Izin</span>
                <div class="dash-progress-bg">
                    <div class="dash-progress-fill" style="width:<?php echo $pIzin; ?>%; background:#BA7517;"></div>
                </div>
                <span class="dash-progress-pct"><?php echo $pIzin; ?>%</span>
            </div>
            <div class="dash-progress-row">
                <span class="dash-progress-label">Alpha</span>
                <div class="dash-progress-bg">
                    <div class="dash-progress-fill" style="width:<?php echo $pAlpha; ?>%; background:#A32D2D;"></div>
                </div>
                <span class="dash-progress-pct"><?php echo $pAlpha; ?>%</span>
            </div>
        </div>

        <div class="dash-rate-block">
            <p style="font-size:12px; color:#888; margin:0 0 4px;">Tingkat kehadiran</p>
            <p style="font-size:32px; font-weight:600; color:#1D9E75; margin:0;"><?php echo $pHadir; ?>%</p>
            <?php if ($isStudent): ?>
                <p style="font-size:12px; color:#888; margin:4px 0 0;"><?php echo $total; ?> catatan absensi</p>
            <?php else: ?>
                <p style="font-size:12px; color:#888; margin:4px 0 0;"><?php echo $total; ?> dari <?php echo $totalSiswa; ?> siswa tercatat</p>
            <?php endif; ?>
        </div>
    </div>

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
<script>
new Chart(document.getElementById('chartDonut'), {
    type: 'doughnut',
    data: {
        labels: ['Hadir', 'Izin', 'Alpha'],
        datasets: [{
            data: [<?php echo (int)$hadir; ?>, <?php echo (int)$izin; ?>, <?php echo (int)$alpha; ?>],
            backgroundColor: ['#1D9E75', '#BA7517', '#A32D2D'],
            borderWidth: 0,
            hoverOffset: 6
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '70%',
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: function(ctx) {
                        var total = ctx.dataset.data.reduce(function(a,b){return a+b;},0);
                        var pct = total > 0 ? Math.round(ctx.parsed / total * 100) : 0;
                        return ' ' + ctx.label + ': ' + ctx.parsed + ' (' + pct + '%)';
                    }
                }
            }
        }
    }
});
</script>