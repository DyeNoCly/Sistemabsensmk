        @if($mode === 'guru')
            <div class="neo-att-card">
                <div class="d-flex justify-content-between align-items-center" style="gap: 10px; flex-wrap: wrap; margin-bottom: 12px;">
                    <div>
                        <h4 class="neo-panel-title" style="margin-bottom: 4px;">Jadwal Mengajar Anda</h4>
                        <p class="neo-subhead" style="margin: 0;">Mulai absensi dari jadwal yang sedang aktif atau buka rekap absen kelas.</p>
                    </div>
                    <a class="btn btn-warning btn-sm" href="{{ route('reports.teacher-recap') }}">
                        <i class="fa fa-file-text"></i> Rekap Absen
                    </a>
                </div>

                @if($currentSchedule)
                    <div class="alert alert-success py-2" style="margin-bottom: 12px;">
                        Jadwal aktif sekarang: {{ $currentSchedule->day->hari ?? '-' }} - {{ $currentSchedule->kelas->nama ?? '-' }} - {{ $currentSchedule->subject->nama_mp ?? '-' }}
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="neo-table">
                        <thead>
                            <tr>
                                <th>Hari</th>
                                <th>Jam</th>
                                <th>Kelas</th>
                                <th>Mapel</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($scheduleRows as $schedule)
                                <tr>
                                    <td>{{ $schedule->day->hari ?? '-' }}</td>
                                    <td>{{ substr((string) $schedule->jam_mulai, 0, 5) }} - {{ substr((string) $schedule->jam_selesai, 0, 5) }}</td>
                                    <td>{{ $schedule->kelas->nama ?? '-' }}</td>
                                    <td>{{ $schedule->subject->nama_mp ?? '-' }}</td>
                                    <td>
                                        <div class="d-flex gap-1" style="flex-wrap: wrap;">
                                            <a class="btn btn-info btn-xs" href="{{ route('attendances.roster', ['idj' => $schedule->idj, 'tanggal' => now()->toDateString()]) }}">
                                                <i class="fa fa-check-circle"></i> Mulai Absen
                                            </a>
                                            <a class="btn btn-warning btn-xs" href="{{ route('reports.teacher-recap') }}">
                                                <i class="fa fa-file-text-o"></i> Rekap
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Belum ada jadwal mengajar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($todaySchedules->count())
                    <div class="neo-att-help" style="margin-top: 10px;">
                        Jadwal hari ini tersedia: {{ $todaySchedules->count() }} slot.
                    </div>
                @endif
            </div>
        @endif

        <div class="neo-lower">
            <div class="neo-panel">
                <h4 class="neo-panel-title">{{ $mode === 'siswa' ? 'Distribusi Mapel Anda' : 'Performa per Mapel' }}</h4>

                @forelse($performance as $row)
                    @php
                        $denominator = (int) ($row['total'] ?? 0);
                        $numerator = (int) ($row['hadir'] ?? 0);
                        $percent = $mode === 'siswa'
                            ? ($totalStatus > 0 ? (int) round(($numerator / $totalStatus) * 100) : 0)
                            : (int) ($row['percent'] ?? 0);
                    @endphp
                    <div class="neo-perf-item">
                        <div class="neo-perf-label">
                            <span>{{ $row['label'] }}</span>
                            <span>{{ $mode === 'siswa' ? $numerator . ' data' : $numerator . '/' . $denominator }}</span>
                        </div>
                        <div class="neo-perf-track">
                            <div class="neo-perf-fill" style="width: {{ max(4, $percent) }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-muted" style="margin-top: 4px;">Belum ada data performa.</p>
                @endforelse
            </div>

            <div class="neo-panel">
                <h4 class="neo-panel-title">{{ $dashboard['tableTitle'] ?? 'Data Terbaru' }}</h4>

                <div class="table-responsive">
                    <table class="neo-table">
                        <thead>
                            @if($mode === 'siswa')
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Mapel</th>
                                    <th>Status</th>
                                </tr>
                            @else
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Siswa</th>
                                    <th>Mapel</th>
                                    <th>Status</th>
                                </tr>
                            @endif
                        </thead>
                        <tbody>
                            @forelse($tableRows as $row)
                                @php
                                    $statusCode = strtoupper((string) ($row->status ?? ''));
                                    $pillClass = $statusCode === 'H' ? 'neo-pill-h' : ($statusCode === 'I' ? 'neo-pill-i' : 'neo-pill-a');
                                @endphp
                                @if($mode === 'siswa')
                                    <tr>
                                        <td>{{ optional($row->tanggal)->format('d M Y') ?? '-' }}</td>
                                        <td>{{ $row->subject->nama_mp ?? '-' }}</td>
                                        <td><span class="neo-pill {{ $pillClass }}">{{ $statusCode ?: '-' }}</span></td>
                                    </tr>
                                @else
                                    <tr>
                                        <td>{{ optional($row->tanggal)->format('d M Y') ?? '-' }}</td>
                                        <td>{{ $row->student->nama ?? $row->nis ?? '-' }}</td>
                                        <td>{{ $row->subject->nama_mp ?? '-' }}</td>
                                        <td><span class="neo-pill {{ $pillClass }}">{{ $statusCode ?: '-' }}</span></td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="{{ $mode === 'siswa' ? 3 : 4 }}" class="text-center text-muted">Belum ada data terbaru.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @if($mode === 'siswa')
        <script>
            (function () {
                var form = document.getElementById('studentAttendanceForm');
                if (!form) {
                    return;
                }

                var locationInput = document.getElementById('location');
                var manualLocationInput = document.getElementById('manual_location');
                var manualLocationBox = document.getElementById('manualLocationBox');
                var locationButton = document.getElementById('btnGetLocation');
                var locationStatus = document.getElementById('locationStatus');
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
                    var parts = rawValue.split(',').map(function (part) { return part.trim(); });
                    if (parts.length !== 2) {
                        return;
                    }

                    var lat = parseFloat(parts[0]);
                    var lng = parseFloat(parts[1]);
                    if (isNaN(lat) || isNaN(lng)) {
                        return;
                    }

                    updateMap(lat, lng);
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
                    }).catch(function (error) {
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

                function syncEvidenceField() {
                    var status = form.querySelector('input[name="attendance_status"]:checked');
                    var isIzin = status && status.value === 'I';
                    evidenceField.style.display = isIzin ? '' : 'none';
                }

                statusRadios.forEach(function (radio) {
                    radio.addEventListener('change', syncEvidenceField);
                });
                syncEvidenceField();

                startCameraBtn.addEventListener('click', startCamera);
                capturePhotoBtn.addEventListener('click', capturePhoto);
                retakePhotoBtn.addEventListener('click', function () {
                    hideElement(photoPreview);
                    photoPreview.removeAttribute('src');
                    photoDataInput.value = '';
                    startCamera();
                });

                if (fallbackPhotoInput) {
                    fallbackPhotoInput.addEventListener('change', function () {
                        if (fallbackPhotoInput.files && fallbackPhotoInput.files[0]) {
                            setStatus(cameraStatus, 'Foto fallback dipilih. Anda bisa kirim absensi.', false);
                        }
                    });
                }

                locationButton.addEventListener('click', function () {
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

                    locationButton.disabled = true;
                    locationButton.innerHTML = '<i class="fa fa-circle-o-notch fa-spin"></i> Mengambil lokasi...';
                    setStatus(locationStatus, 'Meminta izin lokasi...', false);

                    navigator.geolocation.getCurrentPosition(function (position) {
                        var lat = position.coords.latitude.toFixed(6);
                        var lng = position.coords.longitude.toFixed(6);
                        locationInput.value = lat + ',' + lng;
                        syncMapFromLocation(locationInput.value);
                        hideElement(manualLocationBox);
                        setStatus(locationStatus, 'Lokasi berhasil didapatkan.', false);
                        locationButton.disabled = false;
                        locationButton.innerHTML = '<i class="fa fa-map-marker"></i> Dapatkan Lokasi';
                    }, function (error) {
                        locationButton.disabled = false;
                        locationButton.innerHTML = '<i class="fa fa-map-marker"></i> Dapatkan Lokasi';
                        var chromeHint = isChrome ? ' Cek izin di ikon gembok > Site settings > Location.' : '';
                        enableManualLocationFallback('Gagal mengambil lokasi: ' + error.message + chromeHint);
                    }, {
                        enableHighAccuracy: true,
                        timeout: 12000,
                        maximumAge: 0
                    });
                });

                if (manualLocationInput) {
                    manualLocationInput.addEventListener('input', function () {
                        syncMapFromLocation(manualLocationInput.value);
                    });
                }

                form.addEventListener('submit', function (event) {
                    var selectedStatus = form.querySelector('input[name="attendance_status"]:checked');
                    var statusValue = selectedStatus ? selectedStatus.value : 'H';
                    var manualLocation = manualLocationInput ? manualLocationInput.value.trim() : '';

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
                            return;
                        }
                    }

                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', '{{ route('student-attendance.submit') }}', true);
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
    @endif