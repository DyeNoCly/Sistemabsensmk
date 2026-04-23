@extends('layouts.app')

@section('content')
    <div class="card p-3">
        @php
            $proofBasePath = realpath(base_path('../uploads/photos'));
        @endphp

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Data Absensi</h4>
            <div class="d-flex gap-2">
                <a href="{{ route('attendances.create') }}" class="btn btn-primary btn-sm">Tambah Absensi</a>
                <a href="{{ route('attendances.export', array_merge(request()->query(), ['export' => 1])) }}" class="btn btn-outline-success btn-sm">Export CSV</a>
            </div>
        </div>

        @if(session('status'))
            <div class="alert alert-success py-2">{{ session('status') }}</div>
        @endif

        <form class="row g-2 mb-3" method="get">
            <div class="col-md-2">
                <input type="date" class="form-control" name="tanggal_from" value="{{ $filters['tanggal_from'] ?? '' }}" placeholder="Dari">
            </div>
            <div class="col-md-2">
                <input type="date" class="form-control" name="tanggal_to" value="{{ $filters['tanggal_to'] ?? '' }}" placeholder="Sampai">
            </div>
            <div class="col-md-2">
                <select class="form-select" name="kelas">
                    <option value="">Kelas</option>
                    @foreach($classes as $classItem)
                        <option value="{{ $classItem->idk }}" @selected((string)($filters['kelas'] ?? '') === (string) $classItem->idk)>{{ $classItem->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" name="student">
                    <option value="">Siswa</option>
                    @foreach($students as $student)
                        <option value="{{ $student->nis }}" @selected((string)($filters['student'] ?? '') === (string) $student->nis)>{{ $student->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" name="subject">
                    <option value="">Mapel</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->idm }}" @selected((string)($filters['subject'] ?? '') === (string) $subject->idm)>{{ $subject->nama_mp }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-1">
                <select class="form-select" name="status">
                    <option value="">Status</option>
                    <option value="H" @selected((string)($filters['status'] ?? '') === 'H')>H</option>
                    <option value="I" @selected((string)($filters['status'] ?? '') === 'I')>I</option>
                    <option value="A" @selected((string)($filters['status'] ?? '') === 'A')>A</option>
                </select>
            </div>
            <div class="col-md-1 d-flex gap-2">
                <button class="btn btn-outline-primary w-100">Filter</button>
            </div>
        </form>

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
                        <th>Bukti</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $attendance)
                        @php
                            $photoPath = trim((string) ($attendance->photo_path ?? ''));
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
                            <td>{{ optional($attendance->tanggal)->format('Y-m-d') }}</td>
                            <td>{{ $attendance->nis }}</td>
                            <td>{{ $attendance->student?->nama ?? '-' }}</td>
                            <td>{{ $attendance->student?->kelas?->nama ?? '-' }}</td>
                            <td>{{ $attendance->subject?->nama_mp ?? '-' }}</td>
                            <td>{{ $attendance->status }}</td>
                            <td>
                                @if($attendance->latitude && $attendance->longitude)
                                    <span class="js-location-name" data-lat="{{ $attendance->latitude }}" data-lng="{{ $attendance->longitude }}">Mencari nama lokasi...</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($photoUrl)
                                    @if($isImageProof)
                                        <a href="{{ $photoUrl }}" target="_blank" rel="noopener noreferrer" title="Lihat gambar ukuran penuh">
                                            <img src="{{ $photoUrl }}" alt="Bukti absensi" style="width: 56px; height: 56px; object-fit: cover; border-radius: 8px; border: 1px solid #d9e3f0;">
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
                            <td class="d-flex gap-1">
                                <a href="{{ route('attendances.edit', $attendance) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form method="post" action="{{ route('attendances.destroy', $attendance) }}" onsubmit="return confirm('Hapus absensi ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $attendances->links() }}
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
