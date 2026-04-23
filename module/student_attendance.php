<?php
// Check if student is logged in
if (!isset($_SESSION['level']) || $_SESSION['level'] !== 'user') {
    header('Location: ../login.php');
    exit;
}

$student_id = $_SESSION['idu']; // NIS
$student_name = $_SESSION['nama'];
$class_id = $_SESSION['idk'];

// Get current date and time
$current_date = date('Y-m-d');
$current_time = date('H:i:s');
$current_day = date('N'); // 1=Monday, 7=Sunday

// Find current schedule for the student
$stmt = $pdo->prepare("
    SELECT j.idj, j.idm, j.jam_mulai, j.jam_selesai, mp.nama_mp, g.nama as guru_nama
    FROM jadwal j
    JOIN mata_pelajaran mp ON j.idm = mp.idm
    JOIN guru g ON j.idg = g.idg
    WHERE j.idk = ? AND j.idh = ?
    AND (
        (j.jam_mulai <= j.jam_selesai AND ? BETWEEN j.jam_mulai AND j.jam_selesai)
        OR (j.jam_mulai > j.jam_selesai AND (? >= j.jam_mulai OR ? <= j.jam_selesai))
    )
    LIMIT 1
");
$stmt->execute([$class_id, $current_day, $current_time, $current_time, $current_time]);
$schedule = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$schedule) {
    $message = "Tidak ada jadwal pelajaran aktif saat ini.";
    $can_attend = false;
} else {
    $can_attend = true;
    $stmt_check = $pdo->prepare("SELECT id FROM absensi WHERE nis = ? AND idm = ? AND tanggal = ?");
    $stmt_check->execute([$student_id, $schedule['idm'], $current_date]);
    $existing = $stmt_check->fetch(PDO::FETCH_ASSOC);
    if ($existing) {
        $message = "Anda sudah melakukan absensi untuk pelajaran ini hari ini.";
        $can_attend = false;
    }
}
?>

<style>
.student-attendance-wrap .attendance-shell {
    max-width: 880px;
    margin: 0 auto;
}

.student-attendance-wrap .card {
    border-radius: 20px !important;
    overflow: hidden;
}

.student-attendance-wrap .card-header {
    background: linear-gradient(120deg, #124d9a 0%, #2f86d3 100%) !important;
    color: #fff !important;
    padding: 18px 22px !important;
    font-weight: 700;
    font-size: 18px;
}

.student-attendance-wrap .card-body {
    background: #ffffff;
    padding: 22px !important;
}

.student-attendance-wrap .info-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 10px;
    margin: 8px 0 18px;
}

.student-attendance-wrap .info-chip {
    background: #f4f8fd;
    border: 1px solid #d9e7f7;
    border-radius: 12px;
    padding: 10px 12px;
}

.student-attendance-wrap .info-chip .label {
    display: block;
    font-size: 12px;
    color: #5a7188;
    margin-bottom: 3px;
}

.student-attendance-wrap .info-chip .value {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: #1b2b3a;
}

.student-attendance-wrap .section-title {
    margin: 0 0 10px;
    font-size: 14px;
    font-weight: 700;
    color: #2c3e50;
    letter-spacing: 0.2px;
    text-transform: uppercase;
}

.student-attendance-wrap .camera-box {
    border: 2px dashed #d9e2ef;
    border-radius: 16px;
    padding: 18px;
    background: #f8fbff;
    margin-bottom: 15px;
}

.student-attendance-wrap .camera-preview,
.student-attendance-wrap .photo-preview {
    width: 100%;
    max-width: 100%;
    border-radius: 14px;
    background: #000;
}

.student-attendance-wrap .photo-preview {
    display: none;
    background: #f3f6fb;
    border: 1px solid #d9e2ef;
}

.student-attendance-wrap .camera-actions {
    margin-top: 12px;
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.student-attendance-wrap .helper-text {
    display: block;
    margin-top: 8px;
    color: #6b7280;
    font-size: 13px;
}

.student-attendance-wrap .fallback-box {
    margin-top: 12px;
    padding: 10px;
    border-radius: 10px;
    background: #fff8e1;
    border: 1px solid #ffe0a3;
}

.student-attendance-wrap .btn.btn-submit {
    min-width: 190px;
    padding: 10px 24px !important;
    font-weight: 700 !important;
}

.student-attendance-wrap .status-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.student-attendance-wrap .status-actions .btn {
    min-width: 140px;
}

.student-attendance-wrap .status-actions .btn.active {
    box-shadow: 0 0 0 3px rgba(20, 115, 230, 0.15);
}

@media (max-width: 991px) {
    .student-attendance-wrap .info-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 767px) {
    .student-attendance-wrap .card-body {
        padding: 16px !important;
    }

    .student-attendance-wrap .camera-actions .btn,
    .student-attendance-wrap .btn.btn-submit {
        width: 100%;
    }
}
</style>

<div class="row student-attendance-wrap">
    <div class="col-md-12 attendance-shell">
        <div class="card">
            <div class="card-header text-center">
                <i class="fa fa-check-circle"></i> Absensi Siswa - <?php echo htmlspecialchars($student_name); ?>
            </div>
            <div class="card-body">
                <?php if (isset($message)): ?>
                    <div class="alert alert-info"><?php echo $message; ?></div>
                <?php endif; ?>

                <?php if ($can_attend): ?>
                    <div class="info-grid">
                        <div class="info-chip">
                            <span class="label">Pelajaran</span>
                            <span class="value"><?php echo htmlspecialchars($schedule['nama_mp']); ?></span>
                        </div>
                        <div class="info-chip">
                            <span class="label">Guru</span>
                            <span class="value"><?php echo htmlspecialchars($schedule['guru_nama']); ?></span>
                        </div>
                        <div class="info-chip">
                            <span class="label">Waktu</span>
                            <span class="value"><?php echo htmlspecialchars($schedule['jam_mulai']) . ' - ' . htmlspecialchars($schedule['jam_selesai']); ?></span>
                        </div>
                    </div>

                    <form id="attendanceForm" novalidate>
                        <div class="form-group">
                            <p class="section-title">Pilih Status Absensi</p>
                            <input type="hidden" id="attendance_status" name="attendance_status" value="H">
                            <div class="status-actions">
                                <button type="button" id="btnHadir" class="btn btn-success active">
                                    <i class="fa fa-check"></i> Hadir
                                </button>
                                <button type="button" id="btnIzin" class="btn btn-warning">
                                    <i class="fa fa-file-text"></i> Izin
                                </button>
                            </div>
                            <small id="statusInfo" class="helper-text">Status aktif: Hadir.</small>
                        </div>

                        <div class="form-group">
                            <p class="section-title">Ambil Lokasi</p>
                            <label for="location"><i class="fa fa-map-marker"></i> Lokasi (GPS):</label>
                            <input type="text" id="location" name="location" class="form-control" readonly>
                            <div class="camera-actions">
                                <button type="button" id="getLocation" class="btn btn-info btn-sm">
                                    <i class="fa fa-crosshairs"></i> Dapatkan Lokasi
                                </button>
                            </div>
                            <small id="locationStatus" class="helper-text"></small>
                            <div id="manualLocationBox" class="fallback-box" style="display:none;">
                                <label for="manual_location">Fallback Chrome: isi koordinat manual (lat,lng)</label>
                                <input type="text" id="manual_location" class="form-control" placeholder="Contoh: -6.200000,106.816666">
                            </div>
                        </div>

                        <div class="form-group">
                            <p class="section-title">Ambil Foto</p>
                            <label><i class="fa fa-camera"></i> Foto Live Camera:</label>
                            <div class="camera-box">
                                <video id="cameraPreview" class="camera-preview" autoplay playsinline muted style="display:none;"></video>
                                <canvas id="photoCanvas" style="display:none;"></canvas>
                                <img id="photoPreview" class="photo-preview" alt="Foto absensi">
                                <input type="hidden" id="photo_data" name="photo_data">

                                <div class="camera-actions">
                                    <button type="button" id="startCamera" class="btn btn-info btn-sm">
                                        <i class="fa fa-video-camera"></i> Aktifkan Kamera
                                    </button>
                                    <button type="button" id="capturePhoto" class="btn btn-warning btn-sm" disabled>
                                        <i class="fa fa-camera"></i> Ambil Foto
                                    </button>
                                    <button type="button" id="retakePhoto" class="btn btn-default btn-sm" style="display:none;">
                                        <i class="fa fa-refresh"></i> Ulangi
                                    </button>
                                </div>

                                <small id="cameraStatus" class="helper-text"></small>

                                <div id="fallbackPhotoBox" class="fallback-box" style="display:none;">
                                    <label for="photo"><i class="fa fa-image"></i> Fallback Chrome: Ambil/Pilih Foto</label>
                                    <input type="file" id="photo" name="photo" accept="image/*" capture="environment" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="form-group" id="evidenceSection" style="display:none;">
                            <p class="section-title">Upload File Bukti</p>
                            <label for="evidence_file"><i class="fa fa-paperclip"></i> Surat Izin / Bukti (wajib untuk Izin)</label>
                            <input type="file" id="evidence_file" name="evidence_file" class="form-control" accept="image/*,.pdf,.doc,.docx">
                            <small id="fileStatus" class="helper-text">Silakan upload surat izin atau bukti lainnya.</small>
                        </div>

                        <button type="submit" class="btn btn-success btn-submit"><i class="fa fa-send"></i> Kirim Absensi</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    'use strict';

    var form = document.getElementById('attendanceForm');
    if (!form) {
        return;
    }

    var locationInput = document.getElementById('location');
    var manualLocationInput = document.getElementById('manual_location');
    var manualLocationBox = document.getElementById('manualLocationBox');
    var locationStatus = document.getElementById('locationStatus');
    var statusInput = document.getElementById('attendance_status');
    var statusInfo = document.getElementById('statusInfo');
    var btnHadir = document.getElementById('btnHadir');
    var btnIzin = document.getElementById('btnIzin');
    var evidenceFileInput = document.getElementById('evidence_file');
    var evidenceSection = document.getElementById('evidenceSection');
    var fileStatus = document.getElementById('fileStatus');

    var cameraPreview = document.getElementById('cameraPreview');
    var photoCanvas = document.getElementById('photoCanvas');
    var photoPreview = document.getElementById('photoPreview');
    var photoDataInput = document.getElementById('photo_data');
    var fallbackPhotoBox = document.getElementById('fallbackPhotoBox');
    var fallbackPhotoInput = document.getElementById('photo');
    var cameraStatus = document.getElementById('cameraStatus');

    var startCameraBtn = document.getElementById('startCamera');
    var capturePhotoBtn = document.getElementById('capturePhoto');
    var retakePhotoBtn = document.getElementById('retakePhoto');
    var getLocationBtn = document.getElementById('getLocation');

    var cameraStream = null;
    var isChrome = /Chrome/.test(navigator.userAgent) && /Google Inc/.test(navigator.vendor);

    function setActiveStatus(status) {
        statusInput.value = status;
        if (status === 'I') {
            btnIzin.classList.add('active');
            btnHadir.classList.remove('active');
            showElement(evidenceSection);
            setStatus(statusInfo, 'Status aktif: Izin. Wajib upload file bukti.', false);
            setStatus(fileStatus, 'Silakan upload surat izin atau bukti lainnya.', false);
        } else {
            btnHadir.classList.add('active');
            btnIzin.classList.remove('active');
            hideElement(evidenceSection);
            if (evidenceFileInput) {
                evidenceFileInput.value = '';
            }
            setStatus(statusInfo, 'Status aktif: Hadir.', false);
            setStatus(fileStatus, '', false);
        }
    }

    function setStatus(el, message, isError) {
        if (!el) return;
        el.textContent = message || '';
        el.style.color = isError ? '#c0392b' : '#6b7280';
    }

    function showElement(el) {
        if (el) el.style.display = '';
    }

    function hideElement(el) {
        if (el) el.style.display = 'none';
    }

    function stopCamera() {
        if (cameraStream) {
            cameraStream.getTracks().forEach(function(track) {
                track.stop();
            });
            cameraStream = null;
        }
        hideElement(cameraPreview);
        capturePhotoBtn.disabled = true;
    }

    function enablePhotoFallback(message) {
        showElement(fallbackPhotoBox);
        setStatus(cameraStatus, message, true);
    }

    function enableManualLocationFallback(message) {
        showElement(manualLocationBox);
        setStatus(locationStatus, message, true);
    }

    function startCamera() {
        if (window.isSecureContext === false) {
            var insecureMsg = 'Kamera live butuh HTTPS atau localhost. Akses aplikasi via localhost.';
            enablePhotoFallback(insecureMsg);
            alert(insecureMsg);
            return;
        }

        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            enablePhotoFallback('Browser ini tidak mendukung kamera live. Gunakan fallback foto di bawah.');
            return;
        }

        navigator.mediaDevices.getUserMedia({
            video: { facingMode: { ideal: 'environment' } },
            audio: false
        }).then(function(stream) {
            cameraStream = stream;
            cameraPreview.srcObject = stream;
            showElement(cameraPreview);
            capturePhotoBtn.disabled = false;
            hideElement(retakePhotoBtn);
            hideElement(photoPreview);
            photoPreview.removeAttribute('src');
            photoDataInput.value = '';
            setStatus(cameraStatus, 'Kamera aktif. Silakan ambil foto.', false);
        }).catch(function(error) {
            var chromeHint = isChrome ? ' Cek izin di ikon gembok > Site settings > Camera.' : '';
            enablePhotoFallback('Kamera tidak bisa dibuka: ' + error.message + chromeHint);
        });
    }

    function capturePhoto() {
        if (!cameraStream || !cameraPreview.videoWidth) {
            setStatus(cameraStatus, 'Aktifkan kamera terlebih dahulu.', true);
            return;
        }

        photoCanvas.width = cameraPreview.videoWidth;
        photoCanvas.height = cameraPreview.videoHeight;
        photoCanvas.getContext('2d').drawImage(cameraPreview, 0, 0, photoCanvas.width, photoCanvas.height);

        var imageData = photoCanvas.toDataURL('image/jpeg', 0.9);
        photoDataInput.value = imageData;
        photoPreview.src = imageData;
        showElement(photoPreview);
        showElement(retakePhotoBtn);
        setStatus(cameraStatus, 'Foto berhasil diambil.', false);
        stopCamera();
    }

    function getLocation() {
        if (window.isSecureContext === false) {
            var insecureMsg = 'Lokasi GPS butuh HTTPS atau localhost. Akses aplikasi via localhost.';
            enableManualLocationFallback(insecureMsg);
            alert(insecureMsg);
            return;
        }

        if (!navigator.geolocation) {
            enableManualLocationFallback('Browser ini tidak mendukung geolocation.');
            return;
        }

        setStatus(locationStatus, 'Meminta izin lokasi...', false);
        navigator.geolocation.getCurrentPosition(function(position) {
            var lat = position.coords.latitude;
            var lng = position.coords.longitude;
            locationInput.value = lat + ',' + lng;
            setStatus(locationStatus, 'Lokasi berhasil didapatkan.', false);
        }, function(error) {
            var chromeHint = isChrome ? ' Cek izin di ikon gembok > Site settings > Location.' : '';
            enableManualLocationFallback('Gagal mengambil lokasi: ' + error.message + chromeHint);
        }, {
            enableHighAccuracy: true,
            timeout: 15000,
            maximumAge: 0
        });
    }

    startCameraBtn.addEventListener('click', startCamera);
    capturePhotoBtn.addEventListener('click', capturePhoto);
    btnHadir.addEventListener('click', function() { setActiveStatus('H'); });
    btnIzin.addEventListener('click', function() { setActiveStatus('I'); });
    retakePhotoBtn.addEventListener('click', function() {
        hideElement(photoPreview);
        photoPreview.removeAttribute('src');
        photoDataInput.value = '';
        startCamera();
    });
    getLocationBtn.addEventListener('click', getLocation);

    if (fallbackPhotoInput) {
        fallbackPhotoInput.addEventListener('change', function() {
            if (fallbackPhotoInput.files && fallbackPhotoInput.files[0]) {
                setStatus(cameraStatus, 'Foto fallback dipilih. Anda bisa kirim absensi.', false);
            }
        });
    }

    if (evidenceFileInput) {
        evidenceFileInput.addEventListener('change', function() {
            if (evidenceFileInput.files && evidenceFileInput.files[0]) {
                setStatus(fileStatus, 'File bukti dipilih: ' + evidenceFileInput.files[0].name, false);
            }
        });
    }

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        var manualLocation = (manualLocationInput && manualLocationInput.value || '').trim();
        if (!locationInput.value && manualLocation) {
            locationInput.value = manualLocation;
        }

        var hasLivePhoto = !!photoDataInput.value;
        var hasFallbackPhoto = !!(fallbackPhotoInput && fallbackPhotoInput.files && fallbackPhotoInput.files.length > 0);
        var hasEvidenceFile = !!(evidenceFileInput && evidenceFileInput.files && evidenceFileInput.files.length > 0);
        var selectedStatus = statusInput.value || 'H';

        if (selectedStatus === 'I' && !hasEvidenceFile) {
            alert('Untuk status Izin, Anda wajib upload file bukti.');
            return;
        }

        if (selectedStatus === 'H') {
            if (!locationInput.value) {
                alert('Untuk status Hadir, silakan ambil lokasi terlebih dahulu.');
                return;
            }

            if (!hasLivePhoto && !hasFallbackPhoto) {
                alert('Untuk status Hadir, silakan ambil foto live atau pilih foto fallback terlebih dahulu.');
                return;
            }
        }

        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'module/simpan_absen_siswa_submit.php', true);
        xhr.onload = function() {
            if (xhr.status >= 200 && xhr.status < 300) {
                alert(xhr.responseText);
                window.location.reload();
            } else {
                var msg = xhr.responseText ? xhr.responseText : 'No response body';
                alert('Error submitting attendance (' + xhr.status + '): ' + msg);
            }
        };
        xhr.onerror = function() {
            alert('Error submitting attendance. Network error or blocked request.');
        };
        xhr.send(new FormData(form));
    });

    setActiveStatus('H');
    window.addEventListener('beforeunload', stopCamera);
})();
</script>
