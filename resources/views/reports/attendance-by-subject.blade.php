@extends('layouts.app')

@section('content')
    <div class="card p-3">
        @php
            $proofBasePath = realpath(base_path('../uploads/photos'));
        @endphp

        <h4 class="mb-3">Laporan Absensi Siswa per Mata Pelajaran</h4>

        <form class="row g-2 mb-3" method="get">
            <div class="col-md-3">
                <label class="form-label">Mata Pelajaran</label>
                <select class="form-select" name="idm" required>
                    <option value="">-- Pilih Mata Pelajaran --</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->idm }}" @selected((string) $selectedSubject === (string) $subject->idm)>{{ $subject->nama_mp }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Kelas</label>
                <select class="form-select" name="idk">
                    <option value="">-- Semua Kelas --</option>
                    @foreach($classes as $classItem)
                        <option value="{{ $classItem->idk }}" @selected((string) $selectedClass === (string) $classItem->idk)>{{ $classItem->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Dari Tanggal</label>
                <input type="date" class="form-control" name="start_date" value="{{ $startDate }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Sampai Tanggal</label>
                <input type="date" class="form-control" name="end_date" value="{{ $endDate }}">
            </div>
            <div class="col-md-2 d-flex align-items-end gap-2">
                <button class="btn btn-primary w-100">Tampilkan</button>
                <a href="{{ route('reports.attendance-by-subject') }}" class="btn btn-outline-secondary w-100">Reset</a>
            </div>
        </form>

        @if($selectedSubject)
            <div class="row g-3 mb-3">
                <div class="col-md-3"><div class="card p-3"><div class="text-muted">Total</div><div class="fs-4 fw-bold">{{ array_sum($summary) }}</div></div></div>
                <div class="col-md-3"><div class="card p-3"><div class="text-muted">Hadir</div><div class="fs-4 fw-bold text-success">{{ $summary['H'] }}</div></div></div>
                <div class="col-md-3"><div class="card p-3"><div class="text-muted">Izin</div><div class="fs-4 fw-bold text-warning">{{ $summary['I'] }}</div></div></div>
                <div class="col-md-3"><div class="card p-3"><div class="text-muted">Alpha</div><div class="fs-4 fw-bold text-danger">{{ $summary['A'] }}</div></div></div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>NIS</th>
                            <th>Siswa</th>
                            <th>Kelas</th>
                            <th>Mapel</th>
                            <th>Status</th>
                            <th>Lokasi</th>
                            <th>Foto</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $row)
                            @php
                                $photoPath = trim((string) ($row->photo_path ?? ''));
                                $photoUrl = null;
                                $isImageProof = false;

                                if ($photoPath !== '') {
                                    $relativePath = ltrim($photoPath, '/');
                                    $safeRelativePath = str_replace(['..\\', '../'], '', $relativePath);
                                    $diskPath = base_path('../' . $safeRelativePath);

                                    if ($proofBasePath && is_file($diskPath) && str_starts_with(realpath($diskPath) ?: '', $proofBasePath)) {
                                        $photoUrl = route('proof.file', ['path' => $safeRelativePath]);
                                    }

                                    $extension = strtolower(pathinfo($relativePath, PATHINFO_EXTENSION));
                                    $isImageProof = in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true);
                                }
                            @endphp
                            <tr>
                                <td>{{ optional($row->tanggal)->format('Y-m-d') }}</td>
                                <td>{{ $row->nis }}</td>
                                <td>{{ $row->student?->nama ?? '-' }}</td>
                                <td>{{ $row->student?->kelas?->nama ?? '-' }}</td>
                                <td>{{ $row->subject?->nama_mp ?? '-' }}</td>
                                <td>{{ $row->status }}</td>
                                <td>
                                    @if($row->latitude && $row->longitude)
                                        <span class="js-location-name" data-lat="{{ $row->latitude }}" data-lng="{{ $row->longitude }}">Mencari nama lokasi...</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($photoUrl)
                                        @if($isImageProof)
                                            <a href="{{ $photoUrl }}" target="_blank" rel="noopener noreferrer" title="Lihat gambar ukuran penuh">
                                                <img src="{{ $photoUrl }}" alt="Bukti absensi" style="width: 72px; height: 72px; object-fit: cover; border-radius: 8px; border: 1px solid #d9e3f0;">
                                            </a>
                                        @else
                                            <a href="{{ $photoUrl }}" target="_blank" rel="noopener noreferrer" class="btn btn-xs btn-info">
                                                Lihat/Unduh
                                            </a>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted">Belum ada data absensi untuk filter ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <script>
        (function () {
            var locationElements = Array.prototype.slice.call(document.querySelectorAll('.js-location-name[data-lat][data-lng]'));
            if (!locationElements.length) {
                return;
            }

            var cachePrefix = 'nominatim-location:';
            var memoryCache = {};

            function sleep(ms) {
                return new Promise(function (resolve) {
                    setTimeout(resolve, ms);
                });
            }

            function buildKey(lat, lng) {
                return Number(lat).toFixed(5) + ',' + Number(lng).toFixed(5);
            }

            function setText(el, text) {
                el.textContent = text;
            }

            function getCachedName(key) {
                if (memoryCache[key]) {
                    return memoryCache[key];
                }

                try {
                    var fromStorage = window.localStorage.getItem(cachePrefix + key);
                    if (fromStorage) {
                        memoryCache[key] = fromStorage;
                        return fromStorage;
                    }
                } catch (e) {
                    // Ignore storage errors and continue without local cache.
                }

                return null;
            }

            function setCachedName(key, value) {
                memoryCache[key] = value;
                try {
                    window.localStorage.setItem(cachePrefix + key, value);
                } catch (e) {
                    // Ignore storage errors and keep in-memory cache only.
                }
            }

            function formatName(payload) {
                if (!payload || typeof payload !== 'object') {
                    return '';
                }

                if (payload.display_name) {
                    return payload.display_name;
                }

                var address = payload.address || {};
                var parts = [
                    address.road,
                    address.suburb,
                    address.village || address.town || address.city,
                    address.state,
                ].filter(Boolean);

                return parts.join(', ');
            }

            async function resolveLocationName(lat, lng) {
                var key = buildKey(lat, lng);
                var cached = getCachedName(key);
                if (cached) {
                    return cached;
                }

                var url = 'https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=' + encodeURIComponent(lat) + '&lon=' + encodeURIComponent(lng) + '&zoom=18&addressdetails=1';
                var response = await fetch(url, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error('Reverse geocoding request failed.');
                }

                var payload = await response.json();
                var label = formatName(payload);
                if (!label) {
                    throw new Error('Location name not found.');
                }

                setCachedName(key, label);
                return label;
            }

            (async function hydrateLocations() {
                for (var i = 0; i < locationElements.length; i += 1) {
                    var el = locationElements[i];
                    var lat = el.getAttribute('data-lat');
                    var lng = el.getAttribute('data-lng');

                    if (!lat || !lng) {
                        setText(el, '-');
                        continue;
                    }

                    try {
                        var name = await resolveLocationName(lat, lng);
                        setText(el, name);
                    } catch (e) {
                        setText(el, lat + ', ' + lng);
                    }

                    await sleep(250);
                }
            })();
        })();
    </script>
@endsection
