@extends('layouts.app')

@section('content')
    <style>
        .attendance-shell {
            max-width: 980px;
            margin: 20px auto;
            background: linear-gradient(145deg, #ffffff 0%, #f5f8ff 100%);
            border: 1px solid #d8e3ff;
            border-radius: 18px;
            padding: 18px;
            box-shadow: 0 10px 24px rgba(35, 61, 116, 0.11);
        }

        .steps-container {
            background: #f0f6ff;
            border: 1px solid #c8dcff;
            border-radius: 12px;
            padding: 14px;
            margin: 14px 0;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
            gap: 10px;
        }

        .step {
            text-align: center;
            padding: 8px;
            border-radius: 8px;
            background: #fff;
            border: 1px solid #dce8ff;
            position: relative;
        }

        .step.completed {
            background: #e8f5e9;
            border-color: #4caf50;
        }

        .step .step-num {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            background: #bed4fb;
            color: #1f4d87;
            border-radius: 50%;
            font-weight: 700;
            font-size: 14px;
            margin: 0 auto 4px;
        }

        .step.completed .step-num {
            background: #4caf50;
            color: #fff;
        }

        .step .step-label {
            font-size: 12px;
            color: #4a6b9a;
            font-weight: 600;
            margin: 0;
        }

        .step.completed .step-label {
            color: #2e7d32;
        }

        .step-check {
            display: none;
            position: absolute;
            top: 6px;
            right: 6px;
            color: #4caf50;
            font-size: 16px;
        }

        .step.completed .step-check {
            display: block;
        }

        .mandatory-badge {
            display: inline-block;
            background: #ffebee;
            color: #c62828;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 700;
            margin-left: 4px;
            text-transform: uppercase;
        }

        .attendance-title {
            margin: 0;
            font-size: 24px;
            color: #16345d;
            font-weight: 700;
        }

        .attendance-subtitle {
            margin: 6px 0 0;
            color: #557090;
            font-size: 14px;
        }

        .attendance-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 10px;
            flex-wrap: wrap;
        }

        .attendance-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .attendance-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
            margin: 14px 0;
        }

        .attendance-item {
            background: #fff;
            border: 1px solid #dce8ff;
            border-radius: 12px;
            padding: 10px 12px;
        }

        .attendance-item .label {
            display: block;
            font-size: 11px;
            color: #6a7f9b;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .attendance-item .value {
            display: block;
            margin-top: 3px;
            color: #1b3352;
            font-size: 14px;
            font-weight: 700;
        }

        .attendance-status {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 12px;
        }

        .attendance-status label {
            margin: 0;
            cursor: pointer;
            padding: 7px 12px;
            border: 1px solid #bed4fb;
            border-radius: 999px;
            background: #fff;
            color: #1f4d87;
            font-size: 12px;
            font-weight: 700;
        }

        .attendance-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .camera-box {
            margin-top: 8px;
            border: 1px dashed #c8d8fb;
            border-radius: 12px;
            background: #f9fbff;
            padding: 12px;
        }

        .camera-preview,
        .photo-preview {
            width: 100%;
            max-width: 100%;
            border-radius: 10px;
            background: #101010;
        }

        .photo-preview {
            display: none;
            border: 1px solid #d2e0ff;
            background: #f5f9ff;
        }

        .camera-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
        }

        .helper-text {
            display: block;
            margin-top: 6px;
            color: #60728a;
            font-size: 12px;
        }

        .fallback-box {
            margin-top: 10px;
            border: 1px solid #f3d287;
            border-radius: 10px;
            background: #fff9ea;
            padding: 10px;
        }

        .map-box {
            margin-top: 10px;
            border: 1px solid #c9dafa;
            border-radius: 12px;
            overflow: hidden;
            background: #edf3ff;
            display: none;
        }

        .map-box iframe {
            width: 100%;
            height: 230px;
            border: 0;
        }

        .map-note {
            padding: 8px 10px;
            background: #dfe9ff;
            color: #355983;
            font-size: 12px;
        }

        @media (max-width: 767px) {
            .attendance-grid,
            .attendance-row {
                grid-template-columns: 1fr;
            }

            .attendance-actions {
                width: 100%;
            }

            .attendance-actions .btn {
                flex: 1 1 100%;
            }

            .camera-actions .btn,
            .submit-btn {
                width: 100%;
            }
        }
    </style>

    <div class="attendance-shell">
        <div class="attendance-header">
            <div>
                <h3 class="attendance-title">Absensi Siswa</h3>
                <p class="attendance-subtitle">Halaman absensi siswa dengan tampilan terbaru.</p>
            </div>
            <div class="attendance-actions">
                <a href="{{ route('reports.student-recap') }}" class="btn btn-default btn-sm">
                    <i class="fa fa-history"></i> Riwayat
                </a>
                <a href="{{ route('dashboard') }}" class="btn btn-primary btn-sm">
                    <i class="fa fa-dashboard"></i> Dashboard
                </a>
            </div>
        </div>

        @if(session('dashboard_error'))
            <div class="alert alert-danger" style="margin-top: 12px;">{{ session('dashboard_error') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger" style="margin-top: 12px;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="alert alert-info" style="margin-top: 12px; margin-bottom: 12px;">{{ $attendanceForm['message'] }}</div>

        <div class="attendance-grid">
            <div class="attendance-item">
                <span class="label">Mata Pelajaran Aktif</span>
                <span class="value">{{ $attendanceForm['subject_name'] ?? '-' }}</span>
            </div>
            <div class="attendance-item">
                <span class="label">Guru</span>
                <span class="value">{{ $attendanceForm['teacher_name'] ?? '-' }}</span>
            </div>
            <div class="attendance-item">
                <span class="label">Jam Pelajaran</span>
                <span class="value">{{ $attendanceForm['time_range'] ?? '-' }}</span>
            </div>
        </div>

        @if(count($todaySchedules) > 0)
            <div id="jadwal-hari-ini" style="margin-top: 16px; padding: 12px; background: #f0f6ff; border: 1px solid #c8dcff; border-radius: 10px;">
                <p style="margin: 0 0 10px; font-size: 13px; color: #4a6b9a; font-weight: 600;">Ringkasan Jadwal Hari Ini:</p>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 10px;">
                    @foreach($todaySchedules as $sched)
                        <div style="background: #fff; border: 1px solid #dce8ff; border-radius: 8px; padding: 10px; font-size: 12px;">
                            <div style="color: #1b3352; font-weight: 700; margin-bottom: 4px;">{{ $sched->subject->nama_mp ?? '-' }}</div>
                            <div style="color: #6a7f9b; margin-bottom: 2px;">
                                <i class="fa fa-clock-o" style="margin-right: 4px;"></i>
                                {{ substr((string) $sched->jam_mulai, 0, 5) }} - {{ substr((string) $sched->jam_selesai, 0, 5) }}
                            </div>
                            <div style="color: #6a7f9b;">
                                <i class="fa fa-user" style="margin-right: 4px;"></i>
                                {{ $sched->teacher->nama ?? '-' }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div style="background: #fff3cd; border: 1px solid #ffc107; border-radius: 10px; padding: 12px; margin: 14px 0;">
            <p style="margin: 0; color: #856404; font-size: 13px; font-weight: 600;">
                <i class="fa fa-exclamation-circle" style="margin-right: 6px;"></i>
                <strong>Petunjuk:</strong> Lengkapi semua langkah di bawah untuk mengirim absensi.
            </p>
        </div>

        @if(! $attendanceForm['can_submit'])
            <div style="background: #e3f2fd; border: 1px solid #90caf9; border-radius: 10px; padding: 12px; margin: 14px 0;">
                <p style="margin: 0; color: #1565c0; font-size: 13px; font-weight: 600;">
                    <i class="fa fa-info-circle" style="margin-right: 6px;"></i>
                    UI absensi tetap ditampilkan untuk panduan. Pengiriman absensi aktif saat jadwal aktif tersedia.
                </p>
            </div>
        @endif

        <div class="steps-container">
            <div class="step completed">
                <div class="step-num">1</div>
                <span class="step-check">✓</span>
                <p class="step-label">Status</p>
            </div>
            <div class="step" id="stepGPS">
                <div class="step-num">2</div>
                <span class="step-check">✓</span>
                <p class="step-label">Lokasi GPS</p>
            </div>
            <div class="step" id="stepPhoto">
                <div class="step-num">3</div>
                <span class="step-check">✓</span>
                <p class="step-label">Foto</p>
            </div>
            <div class="step" id="stepSubmit">
                <div class="step-num">4</div>
                <span class="step-check">✓</span>
                <p class="step-label">Kirim</p>
            </div>
        </div>

        <form method="post" action="{{ route('student-attendance.submit') }}" enctype="multipart/form-data" id="studentAttendanceForm" novalidate data-can-submit="{{ $attendanceForm['can_submit'] ? '1' : '0' }}">
            @csrf

            <div class="attendance-status">
                <label>
                    <input type="radio" name="attendance_status" value="H" {{ old('attendance_status') === 'H' ? 'checked' : '' }}>
                    Hadir
                </label>
                <label>
                    <input type="radio" name="attendance_status" value="I" {{ old('attendance_status') === 'I' ? 'checked' : '' }}>
                    Izin
                </label>
            </div>

            <div class="attendance-row">
                <div class="form-group">
                    <label for="location">Lokasi GPS <span class="mandatory-badge">Wajib</span></label>
                    <input type="text" class="form-control" id="location" name="location" value="{{ old('location') }}" placeholder="Contoh: -6.200000,106.816666" readonly>
                    <div class="helper-text">Klik tombol untuk isi lokasi otomatis jika browser mengizinkan.</div>

                    <div class="camera-actions">
                        <button type="button" class="btn btn-info btn-sm" id="btnGetLocation">
                            <i class="fa fa-map-marker"></i> Dapatkan Lokasi
                        </button>
                    </div>

                    <div id="manualLocationBox" class="fallback-box" style="display:none;">
                        <label for="manual_location">Fallback Chrome: isi koordinat manual (lat,lng)</label>
                        <input type="text" class="form-control" id="manual_location" name="manual_location" value="{{ old('manual_location') }}" placeholder="Contoh: -6.200000,106.816666">
                    </div>

                    <div id="locationMapBox" class="map-box">
                        <div class="map-note">Peta lokasi akan tampil di bawah setelah koordinat didapatkan.</div>
                        <iframe id="locationMap" title="Peta lokasi absensi" loading="lazy"></iframe>
                    </div>

                    <small id="locationStatus" class="helper-text"></small>
                    <small id="locationAddress" class="helper-text"></small>
                </div>

                <div class="form-group">
                    <label>Foto Kehadiran <span class="mandatory-badge">Wajib</span></label>
                    <div class="camera-box">
                        <video id="cameraPreview" class="camera-preview" autoplay playsinline muted style="display:none;"></video>
                        <canvas id="photoCanvas" style="display:none;"></canvas>
                        <img id="photoPreview" class="photo-preview" alt="Foto absensi">
                        <input type="hidden" id="photo_data" name="photo_data">

                        <div class="camera-actions">
                            <button type="button" class="btn btn-info btn-sm" id="startCamera">
                                <i class="fa fa-video-camera"></i> Aktifkan Kamera
                            </button>
                            <button type="button" class="btn btn-warning btn-sm" id="capturePhoto" disabled>
                                <i class="fa fa-camera"></i> Ambil Foto
                            </button>
                            <button type="button" class="btn btn-default btn-sm" id="retakePhoto" style="display:none;">
                                <i class="fa fa-refresh"></i> Ulangi
                            </button>
                        </div>

                        <small id="cameraStatus" class="helper-text"></small>

                        <div id="fallbackPhotoBox" class="fallback-box" style="display:none;">
                            <label for="photo">Fallback Chrome: ambil/pilih foto</label>
                            <input type="file" class="form-control" id="photo" name="photo" accept="image/*" capture="environment">
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group" id="evidenceField" style="display:none; margin-top: 10px;">
                <label for="evidence_file">File Bukti Izin (wajib saat status Izin)</label>
                <input type="file" class="form-control" id="evidence_file" name="evidence_file" accept="image/*,.pdf,.doc,.docx">
                <div class="helper-text">Format: gambar/PDF/DOC/DOCX. Maks 5MB.</div>
            </div>

            <button type="submit" class="btn btn-success submit-btn" style="margin-top: 12px; min-width: 180px;" id="submitBtn" disabled>
                <i class="fa fa-send"></i> {{ $attendanceForm['can_submit'] ? 'Kirim Absensi' : 'Kirim Absensi (Jadwal Belum Aktif)' }}
            </button>
        </form>
    </div>

    <script>
        (function () {
            var form = document.getElementById('studentAttendanceForm');
            if (!form) {
                return;
            }

            var canSubmitLocked = form.dataset.canSubmit !== '1';
            var locationInput = document.getElementById('location');
            var manualLocationInput = document.getElementById('manual_location');
            var manualLocationBox = document.getElementById('manualLocationBox');
            var locationButton = document.getElementById('btnGetLocation');
            var locationStatus = document.getElementById('locationStatus');
            var locationAddress = document.getElementById('locationAddress');
            var locationMapBox = document.getElementById('locationMapBox');
            var locationMap = document.getElementById('locationMap');
            var evidenceField = document.getElementById('evidenceField');
            var statusRadios = form.querySelectorAll('input[name="attendance_status"]');
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
            var evidenceInput = document.getElementById('evidence_file');
            var submitBtn = document.getElementById('submitBtn');
            var stepGPS = document.getElementById('stepGPS');
            var stepPhoto = document.getElementById('stepPhoto');
            var stepSubmit = document.getElementById('stepSubmit');

            var cameraStream = null;
            var isChrome = /Chrome/.test(navigator.userAgent) && /Google Inc/.test(navigator.vendor);

            function setStatus(el, message, isError) {
                if (!el) {
                    return;
                }

                el.textContent = message || '';
                el.style.color = isError ? '#c0392b' : '#60728a';
            }

            function showElement(el) {
                if (el) {
                    el.style.display = '';
                }
            }

            function hideElement(el) {
                if (el) {
                    el.style.display = 'none';
                }
            }

            function selectHadirStatus() {
                var hadirRadio = form.querySelector('input[name="attendance_status"][value="H"]');
                if (hadirRadio) {
                    hadirRadio.checked = true;
                    syncEvidenceField();
                }
            }

            function syncEvidenceField() {
                var status = form.querySelector('input[name="attendance_status"]:checked');
                evidenceField.style.display = status && status.value === 'I' ? '' : 'none';
            }

            function updateMap(lat, lng) {
                if (!locationMap || !locationMapBox) {
                    return;
                }

                var delta = 0.0035;
                var minLat = (lat - delta).toFixed(6);
                var minLng = (lng - delta).toFixed(6);
                var maxLat = (lat + delta).toFixed(6);
                var maxLng = (lng + delta).toFixed(6);
                locationMap.src = 'https://www.openstreetmap.org/export/embed.html?bbox=' + minLng + '%2C' + minLat + '%2C' + maxLng + '%2C' + maxLat + '&layer=mapnik&marker=' + lat.toFixed(6) + '%2C' + lng.toFixed(6);
                showElement(locationMapBox);
            }

            function syncMapFromLocation(value) {
                var rawValue = (value || '').trim();
                var parts = rawValue.split(',').map(function (part) {
                    return part.trim();
                });

                if (parts.length !== 2) {
                    return;
                }

                var lat = parseFloat(parts[0]);
                var lng = parseFloat(parts[1]);

                if (isNaN(lat) || isNaN(lng)) {
                    return;
                }

                updateMap(lat, lng);
                reverseGeocodeLocation(lat, lng);
            }

            function reverseGeocodeLocation(lat, lng) {
                if (!locationAddress) {
                    return;
                }

                locationAddress.textContent = 'Mencari nama lokasi...';
                locationAddress.style.color = '#60728a';

                var endpoint = 'https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=' + encodeURIComponent(lat) + '&lon=' + encodeURIComponent(lng) + '&accept-language=id';

                fetch(endpoint, {
                    headers: {
                        'Accept': 'application/json'
                    }
                }).then(function (response) {
                    if (!response.ok) {
                        throw new Error('HTTP ' + response.status);
                    }
                    return response.json();
                }).then(function (data) {
                    var displayName = data && data.display_name ? String(data.display_name) : '';
                    locationAddress.textContent = displayName
                        ? 'Lokasi: ' + displayName
                        : 'Lokasi: nama tempat tidak ditemukan, gunakan koordinat.';
                    locationAddress.style.color = '#60728a';
                }).catch(function () {
                    locationAddress.textContent = 'Lokasi: tidak bisa mengambil nama tempat, tetapi koordinat tetap valid.';
                    locationAddress.style.color = '#c0392b';
                });
            }

            function updateStepsAndButton() {
                var status = form.querySelector('input[name="attendance_status"]:checked');
                var statusValue = status ? status.value : '';
                var hasLocation = !!(locationInput.value || (manualLocationInput && manualLocationInput.value.trim()));
                var hasPhoto = !!(photoDataInput.value || (fallbackPhotoInput && fallbackPhotoInput.files && fallbackPhotoInput.files.length > 0));
                var hasEvidence = !!(evidenceInput && evidenceInput.files && evidenceInput.files.length > 0);

                if (stepGPS) {
                    stepGPS.classList.toggle('completed', hasLocation);
                }
                if (stepPhoto) {
                    stepPhoto.classList.toggle('completed', hasPhoto || hasEvidence);
                }

                var ready = false;
                if (statusValue === 'H') {
                    ready = hasLocation && hasPhoto;
                } else if (statusValue === 'I') {
                    ready = hasEvidence;
                }

                if (!canSubmitLocked) {
                    submitBtn.disabled = !ready;
                } else {
                    submitBtn.disabled = true;
                }

                if (stepSubmit) {
                    stepSubmit.classList.toggle('completed', ready && !canSubmitLocked);
                }
            }

            function stopCamera() {
                if (cameraStream) {
                    cameraStream.getTracks().forEach(function (track) {
                        track.stop();
                    });
                    cameraStream = null;
                }
                hideElement(cameraPreview);
                capturePhotoBtn.disabled = true;
            }

            function startCamera() {
                selectHadirStatus();

                if (window.isSecureContext === false) {
                    alert('Kamera live butuh HTTPS atau localhost. Akses aplikasi via localhost.');
                    return;
                }

                if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                    showElement(fallbackPhotoBox);
                    setStatus(cameraStatus, 'Browser ini tidak mendukung kamera live. Gunakan fallback foto di bawah.', true);
                    return;
                }

                navigator.mediaDevices.getUserMedia({
                    video: { facingMode: { ideal: 'environment' } },
                    audio: false
                }).then(function (stream) {
                    cameraStream = stream;
                    cameraPreview.srcObject = stream;
                    showElement(cameraPreview);
                    capturePhotoBtn.disabled = false;
                    hideElement(retakePhotoBtn);
                    hideElement(photoPreview);
                    photoPreview.removeAttribute('src');
                    photoDataInput.value = '';
                    setStatus(cameraStatus, 'Kamera aktif. Silakan ambil foto.', false);
                    updateStepsAndButton();
                }).catch(function (error) {
                    var chromeHint = isChrome ? ' Cek izin di ikon gembok > Site settings > Camera.' : '';
                    showElement(fallbackPhotoBox);
                    setStatus(cameraStatus, 'Kamera tidak bisa dibuka: ' + error.message + chromeHint, true);
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
                updateStepsAndButton();
            }

            function requestLocation() {
                selectHadirStatus();

                if (window.isSecureContext === false) {
                    alert('Lokasi GPS butuh HTTPS atau localhost. Akses aplikasi via localhost.');
                    return;
                }

                if (!navigator.geolocation) {
                    showElement(manualLocationBox);
                    setStatus(locationStatus, 'Browser ini tidak mendukung geolocation.', true);
                    return;
                }

                locationButton.disabled = true;
                locationButton.innerHTML = '<i class="fa fa-circle-o-notch fa-spin"></i> Mengambil lokasi...';
                setStatus(locationStatus, 'Meminta izin lokasi...', false);

                navigator.geolocation.getCurrentPosition(function (position) {
                    var lat = position.coords.latitude.toFixed(6);
                    var lng = position.coords.longitude.toFixed(6);
                    locationInput.value = lat + ',' + lng;
                    if (manualLocationInput) {
                        manualLocationInput.value = '';
                    }
                    hideElement(manualLocationBox);
                    syncMapFromLocation(locationInput.value);
                    setStatus(locationStatus, 'Lokasi berhasil didapatkan.', false);
                    locationButton.disabled = false;
                    locationButton.innerHTML = '<i class="fa fa-map-marker"></i> Dapatkan Lokasi';
                    updateStepsAndButton();
                }, function (error) {
                    locationButton.disabled = false;
                    locationButton.innerHTML = '<i class="fa fa-map-marker"></i> Dapatkan Lokasi';
                    var chromeHint = isChrome ? ' Cek izin di ikon gembok > Site settings > Location.' : '';
                    showElement(manualLocationBox);
                    setStatus(locationStatus, 'Gagal mengambil lokasi: ' + error.message + chromeHint, true);
                    if (locationAddress) {
                        locationAddress.textContent = '';
                    }
                }, {
                    enableHighAccuracy: true,
                    timeout: 12000,
                    maximumAge: 0
                });
            }

            locationButton.addEventListener('click', requestLocation);
            startCameraBtn.addEventListener('click', startCamera);
            capturePhotoBtn.addEventListener('click', capturePhoto);

            retakePhotoBtn.addEventListener('click', function () {
                hideElement(photoPreview);
                photoPreview.removeAttribute('src');
                photoDataInput.value = '';
                updateStepsAndButton();
                startCamera();
            });

            if (fallbackPhotoInput) {
                fallbackPhotoInput.addEventListener('change', function () {
                    if (fallbackPhotoInput.files && fallbackPhotoInput.files.length > 0) {
                        setStatus(cameraStatus, 'Foto fallback dipilih. Siap untuk absensi.', false);
                        updateStepsAndButton();
                    }
                });
            }

            if (manualLocationInput) {
                manualLocationInput.addEventListener('input', function () {
                    syncMapFromLocation(manualLocationInput.value);
                    updateStepsAndButton();
                });
            }

            statusRadios.forEach(function (radio) {
                radio.addEventListener('change', function () {
                    syncEvidenceField();
                    updateStepsAndButton();
                });
            });

            form.addEventListener('submit', function (event) {
                if (canSubmitLocked) {
                    event.preventDefault();
                    alert('Jadwal belum aktif. Form tetap terlihat untuk panduan, tetapi belum bisa dikirim.');
                    return;
                }

                var selectedStatus = form.querySelector('input[name="attendance_status"]:checked');
                var statusValue = selectedStatus ? selectedStatus.value : '';
                var manualLocation = manualLocationInput ? manualLocationInput.value.trim() : '';

                if (!statusValue) {
                    event.preventDefault();
                    alert('Pilih status Hadir atau Izin terlebih dahulu.');
                    return;
                }

                if (!locationInput.value && manualLocation) {
                    locationInput.value = manualLocation;
                    syncMapFromLocation(manualLocation);
                }

                var hasLivePhoto = !!photoDataInput.value;
                var hasFallbackPhoto = !!(fallbackPhotoInput && fallbackPhotoInput.files && fallbackPhotoInput.files.length > 0);
                var hasEvidenceFile = !!(evidenceInput && evidenceInput.files && evidenceInput.files.length > 0);

                if (statusValue === 'I' && !hasEvidenceFile) {
                    event.preventDefault();
                    alert('Untuk status Izin, Anda wajib upload file bukti.');
                    return;
                }

                if (statusValue === 'H') {
                    if (!locationInput.value) {
                        event.preventDefault();
                        alert('Untuk status Hadir, silakan ambil lokasi terlebih dahulu.');
                        return;
                    }

                    if (!hasLivePhoto && !hasFallbackPhoto) {
                        event.preventDefault();
                        alert('Untuk status Hadir, silakan ambil foto live atau pilih foto fallback terlebih dahulu.');
                    }
                }
            });

            if (locationInput.value) {
                syncMapFromLocation(locationInput.value);
            }

            syncEvidenceField();
            setStatus(cameraStatus, 'Klik Aktifkan Kamera untuk mulai mengambil foto.', false);
            updateStepsAndButton();
            window.addEventListener('beforeunload', stopCamera);
        })();
    </script>
@endsection
