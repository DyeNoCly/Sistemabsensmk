@extends('layouts.app')

@section('content')
@php
    $cards = $dashboard['cards'] ?? [];
    $status = $dashboard['status'] ?? ['Hadir' => 0, 'Izin' => 0, 'Alpha' => 0];
    $trend = $dashboard['trend'] ?? ['labels' => [], 'hadir' => [], 'izin' => [], 'alpha' => []];
    $performance = $dashboard['performance'] ?? collect();
    $tableRows = $dashboard['tableRows'] ?? collect();
    $mode = $dashboard['mode'] ?? 'empty';
    $scheduleRows = $dashboard['scheduleRows'] ?? collect();
    $todaySchedules = $dashboard['todaySchedules'] ?? collect();
    $currentSchedule = $dashboard['currentSchedule'] ?? null;

    $totalStatus = array_sum($status);
    $pctHadir = $totalStatus > 0 ? round(($status['Hadir'] / $totalStatus) * 100) : 0;
    $pctIzin = $totalStatus > 0 ? round(($status['Izin'] / $totalStatus) * 100) : 0;
    $pctAlpha = max(0, 100 - $pctHadir - $pctIzin);

    $allSeries = array_merge($trend['hadir'] ?? [], $trend['izin'] ?? [], $trend['alpha'] ?? []);
    $maxSeries = !empty($allSeries) ? max($allSeries) : 0;
    $maxSeries = $maxSeries > 0 ? $maxSeries : 1;
@endphp

<style>
    .neo-shell {
        background: radial-gradient(circle at top left, #f2eefc 0%, #fbf9ff 42%, #f7fbff 100%);
        border: 3px solid #2f1b49;
        border-radius: 24px;
        padding: 24px;
        margin: 20px 0;
        box-shadow: 0 16px 34px rgba(30, 23, 52, 0.13);
    }

    .neo-headline {
        margin: 0;
        font-size: 30px;
        color: #241336;
        font-weight: 700;
        letter-spacing: 0.2px;
    }

    .neo-subhead {
        margin-top: 6px;
        color: #6b6482;
        font-size: 14px;
    }

    .neo-card-grid {
        margin-top: 20px;
        display: grid;
        grid-template-columns: repeat(4, minmax(170px, 1fr));
        gap: 14px;
    }

    .neo-stat {
        background: linear-gradient(160deg, #ffffff 0%, #f7f2ff 100%);
        border: 1px solid #e5dff2;
        border-radius: 14px;
        padding: 14px;
        min-height: 108px;
        position: relative;
        overflow: hidden;
    }

    .neo-stat-link {
        display: block;
        color: inherit;
        text-decoration: none;
        transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
    }

    .neo-stat-link:hover,
    .neo-stat-link:focus {
        color: inherit;
        text-decoration: none;
        transform: translateY(-2px);
        box-shadow: 0 10px 24px rgba(68, 46, 97, 0.12);
        border-color: #cfbdf5;
    }

    .neo-stat-link:focus-visible {
        outline: 3px solid rgba(79, 46, 132, 0.25);
        outline-offset: 2px;
    }

    .neo-stat-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .neo-stat-label {
        margin: 0;
        color: #7a6f90;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .neo-stat-icon {
        width: 30px;
        height: 30px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #593891;
        background: #efe7ff;
    }

    .neo-stat-value {
        margin: 8px 0 0;
        font-size: 30px;
        line-height: 1;
        color: #241336;
        font-weight: 700;
    }

    .neo-stat-foot {
        margin: 7px 0 0;
        color: #8d84a2;
        font-size: 12px;
    }

    .neo-main-grid {
        margin-top: 16px;
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 14px;
    }

    .neo-panel {
        background: #fff;
        border: 1px solid #e6deef;
        border-radius: 16px;
        padding: 16px;
        min-height: 250px;
    }

    .neo-panel-title {
        margin: 0 0 14px;
        color: #2d1a46;
        font-weight: 700;
        font-size: 19px;
    }

    .neo-bars {
        display: grid;
        grid-template-columns: repeat(5, minmax(64px, 1fr));
        gap: 12px;
        align-items: end;
        min-height: 200px;
        padding-top: 8px;
    }

    .neo-bar-wrap {
        text-align: center;
    }

    .neo-bar-stack {
        display: flex;
        align-items: flex-end;
        justify-content: center;
        gap: 3px;
        min-height: 156px;
        margin-bottom: 8px;
    }

    .neo-bar {
        width: 14px;
        border-radius: 6px;
        transition: height .4s ease;
    }

    .neo-bar.hadir { background: #4f2e84; }
    .neo-bar.izin { background: #9b84d6; }
    .neo-bar.alpha { background: #d4c7f0; }

    .neo-month {
        font-size: 12px;
        color: #7a7191;
    }

    .neo-legend {
        display: flex;
        gap: 14px;
        margin-top: 8px;
        flex-wrap: wrap;
    }

    .neo-legend-item {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #6f6588;
        font-size: 12px;
    }

    .neo-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
    }

    .neo-ring-box {
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 10px 0 14px;
    }

    .neo-ring {
        width: 156px;
        height: 156px;
        border-radius: 50%;
        background: conic-gradient(
            #4f2e84 0% {{ $pctHadir }}%,
            #9b84d6 {{ $pctHadir }}% {{ $pctHadir + $pctIzin }}%,
            #d4c7f0 {{ $pctHadir + $pctIzin }}% 100%
        );
        position: relative;
        box-shadow: inset 0 0 0 8px #ffffff;
    }

    .neo-ring::after {
        content: '{{ $totalStatus }}';
        width: 96px;
        height: 96px;
        border-radius: 50%;
        background: #fff;
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #271543;
        font-weight: 700;
        font-size: 28px;
    }

    .neo-status-row {
        display: flex;
        justify-content: space-between;
        font-size: 13px;
        padding: 4px 0;
        color: #5e5575;
    }

    .neo-lower {
        margin-top: 14px;
        display: grid;
        grid-template-columns: 1.2fr 1.8fr;
        gap: 14px;
    }

    .neo-perf-item {
        margin-bottom: 12px;
    }

    .neo-perf-label {
        display: flex;
        justify-content: space-between;
        color: #5d5475;
        font-size: 13px;
        margin-bottom: 6px;
    }

    .neo-perf-track {
        width: 100%;
        height: 8px;
        border-radius: 6px;
        background: #ede6f7;
        overflow: hidden;
    }

    .neo-perf-fill {
        height: 100%;
        border-radius: 6px;
        background: linear-gradient(90deg, #4f2e84 0%, #9b84d6 100%);
    }

    .neo-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    .neo-table th,
    .neo-table td {
        padding: 8px 10px;
        border-bottom: 1px solid #ece5f5;
        color: #4f4567;
    }

    .neo-table th {
        font-size: 12px;
        text-transform: uppercase;
        color: #857a9f;
    }

    .neo-pill {
        border-radius: 999px;
        padding: 2px 8px;
        font-size: 11px;
        font-weight: 700;
        display: inline-block;
        min-width: 32px;
        text-align: center;
    }

    .neo-pill-h { background: #e5dbfb; color: #40266f; }
    .neo-pill-i { background: #ede6ff; color: #6143a0; }
    .neo-pill-a { background: #f4efff; color: #8f76c9; }

    .neo-att-card {
        margin-top: 14px;
        background: linear-gradient(130deg, #ffffff 0%, #f4eefe 100%);
        border: 1px solid #ddcffa;
        border-radius: 16px;
        padding: 16px;
    }

    @media (max-width: 1200px) {
        .neo-card-grid { grid-template-columns: repeat(2, minmax(170px, 1fr)); }
        .neo-main-grid { grid-template-columns: 1fr; }
        .neo-lower { grid-template-columns: 1fr; }
    }

    @media (max-width: 767px) {
        .neo-shell {
            border-width: 2px;
            border-radius: 16px;
            padding: 14px;
        }
        .neo-headline { font-size: 24px; }
        .neo-card-grid { grid-template-columns: 1fr; }
        .neo-bars { grid-template-columns: repeat(5, minmax(44px, 1fr)); gap: 8px; }
        .neo-bar { width: 10px; }
    }
</style>

<div class="neo-shell">
    <h3 class="neo-headline">Hi, {{ $sessionUser['nama'] ?? 'Pengguna' }}</h3>
    <p class="neo-subhead">{{ $dashboard['headline'] ?? 'Dashboard' }}. {{ $dashboard['subhead'] ?? '' }}</p>

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

    <div class="neo-card-grid">
        @foreach($cards as $card)
            @if(!empty($card['url']))
                <a class="neo-stat neo-stat-link" href="{{ $card['url'] }}">
                    <div class="neo-stat-top">
                        <p class="neo-stat-label">{{ $card['label'] }}</p>
                        <span class="neo-stat-icon"><i class="fa {{ $card['icon'] }}"></i></span>
                    </div>
                    <p class="neo-stat-value">{{ $card['value'] }}</p>
                    <p class="neo-stat-foot">{{ $card['delta'] }}</p>
                </a>
            @else
                <div class="neo-stat">
                    <div class="neo-stat-top">
                        <p class="neo-stat-label">{{ $card['label'] }}</p>
                        <span class="neo-stat-icon"><i class="fa {{ $card['icon'] }}"></i></span>
                    </div>
                    <p class="neo-stat-value">{{ $card['value'] }}</p>
                    <p class="neo-stat-foot">{{ $card['delta'] }}</p>
                </div>
            @endif
        @endforeach
    </div>

    <div class="neo-main-grid">
        <div class="neo-panel">
            <h4 class="neo-panel-title">Tren Kehadiran 5 Bulan</h4>
            <div class="neo-bars">
                @foreach($trend['labels'] as $idx => $label)
                    @php
                        $h = (int) ($trend['hadir'][$idx] ?? 0);
                        $i = (int) ($trend['izin'][$idx] ?? 0);
                        $a = (int) ($trend['alpha'][$idx] ?? 0);
                    @endphp
                    <div class="neo-bar-wrap">
                        <div class="neo-bar-stack">
                            <div class="neo-bar hadir" style="height: {{ max(6, (int) round(($h / $maxSeries) * 140)) }}px" title="Hadir: {{ $h }}"></div>
                            <div class="neo-bar izin" style="height: {{ max(6, (int) round(($i / $maxSeries) * 140)) }}px" title="Izin: {{ $i }}"></div>
                            <div class="neo-bar alpha" style="height: {{ max(6, (int) round(($a / $maxSeries) * 140)) }}px" title="Alpha: {{ $a }}"></div>
                        </div>
                        <div class="neo-month">{{ $label }}</div>
                    </div>
                @endforeach
            </div>
            <div class="neo-legend">
                <span class="neo-legend-item"><span class="neo-dot" style="background:#4f2e84;"></span>Hadir</span>
                <span class="neo-legend-item"><span class="neo-dot" style="background:#9b84d6;"></span>Izin</span>
                <span class="neo-legend-item"><span class="neo-dot" style="background:#d4c7f0;"></span>Alpha</span>
            </div>
        </div>

        <div class="neo-panel">
            <h4 class="neo-panel-title">Komposisi Status</h4>
            <div class="neo-ring-box">
                <div class="neo-ring"></div>
            </div>
            <div class="neo-status-row"><span>Hadir</span><strong>{{ $status['Hadir'] ?? 0 }} ({{ $pctHadir }}%)</strong></div>
            <div class="neo-status-row"><span>Izin</span><strong>{{ $status['Izin'] ?? 0 }} ({{ $pctIzin }}%)</strong></div>
            <div class="neo-status-row"><span>Alpha</span><strong>{{ $status['Alpha'] ?? 0 }} ({{ $pctAlpha }}%)</strong></div>
        </div>
    </div>

    @if($mode === 'guru')
        <div class="neo-att-card">
            <div style="display: flex; justify-content: space-between; align-items: center; gap: 10px; flex-wrap: wrap; margin-bottom: 12px;">
                <div>
                    <h4 class="neo-panel-title" style="margin-bottom: 4px;">Jadwal Mengajar Anda</h4>
                    <p class="neo-subhead" style="margin: 0;">Mulai absensi dari jadwal aktif atau buka rekap absen kelas.</p>
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
                                    <div style="display: flex; gap: 4px; flex-wrap: wrap;">
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
                <div style="margin-top: 10px; font-size: 12px; color: #776b90;">
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
                    $percent = (int) ($row['percent'] ?? 0);
                @endphp
                <div class="neo-perf-item">
                    <div class="neo-perf-label">
                        <span>{{ $row['label'] }}</span>
                        <span>{{ $numerator }}/{{ $denominator }}</span>
                    </div>
                    <div class="neo-perf-track">
                        <div class="neo-perf-fill" style="width: {{ max(4, $percent) }}%"></div>
                    </div>
                </div>
            @empty
                <p style="color: #8d84a2; font-size: 13px;">Belum ada data performa.</p>
            @endforelse
        </div>

        <div class="neo-panel">
            <h4 class="neo-panel-title">{{ $dashboard['tableTitle'] ?? 'Data Terbaru' }}</h4>
            <div class="table-responsive">
                <table class="neo-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            @if($mode !== 'siswa')
                                <th>Siswa</th>
                            @endif
                            <th>Mapel</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tableRows as $row)
                            @php
                                $statusCode = strtoupper((string) ($row->status ?? ''));
                                $pillClass = $statusCode === 'H' ? 'neo-pill-h' : ($statusCode === 'I' ? 'neo-pill-i' : 'neo-pill-a');
                            @endphp
                            <tr>
                                <td>{{ optional($row->tanggal)->format('d M Y') ?? '-' }}</td>
                                @if($mode !== 'siswa')
                                    <td>{{ $row->student->nama ?? $row->nis ?? '-' }}</td>
                                @endif
                                <td>{{ $row->subject->nama_mp ?? '-' }}</td>
                                <td><span class="neo-pill {{ $pillClass }}">{{ $statusCode ?: '-' }}</span></td>
                            </tr>
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
@endsection
