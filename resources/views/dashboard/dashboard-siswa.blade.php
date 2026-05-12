@extends('layouts.app')

@section('content')
    @php
        $status = $dashboard['status'] ?? ['Hadir' => 0, 'Izin' => 0, 'Alpha' => 0];
        $tableRows = $dashboard['tableRows'] ?? collect();
        $trend = $dashboard['trend'] ?? ['labels' => [], 'hadir' => [], 'izin' => [], 'alpha' => []];

        $latestIndex = !empty($trend['labels']) ? count($trend['labels']) - 1 : null;
        $latestHadir = $latestIndex !== null ? (int) ($trend['hadir'][$latestIndex] ?? 0) : null;
        $latestIzin = $latestIndex !== null ? (int) ($trend['izin'][$latestIndex] ?? 0) : null;
        $latestAlpha = $latestIndex !== null ? (int) ($trend['alpha'][$latestIndex] ?? 0) : null;

        $monthHadir = $latestHadir ?? (int) ($status['Hadir'] ?? 0);
        $monthIzin = $latestIzin ?? (int) ($status['Izin'] ?? 0);
        $monthAlpha = $latestAlpha ?? (int) ($status['Alpha'] ?? 0);

        $now = now();
        $isSchoolHour = $now->isWeekday() && $now->hour >= 6 && $now->hour < 16;
        $clockTagText = $isSchoolHour ? 'Di jam pelajaran' : 'Di luar jam pelajaran';
        $monthTitle = $now->translatedFormat('F Y');
        $startOffset = (int) $now->copy()->startOfMonth()->isoWeekday() - 1;
        $daysInMonth = (int) $now->daysInMonth;
        $todayDate = (int) $now->day;

        $calendarCells = [];
        for ($i = 0; $i < $startOffset; $i++) {
            $calendarCells[] = null;
        }

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $calendarCells[] = $day;
        }

        while (count($calendarCells) % 7 !== 0) {
            $calendarCells[] = null;
        }

        $statusMeta = [
            'H' => ['label' => 'Hadir', 'class' => 'std-pill-hadir'],
            'I' => ['label' => 'Izin', 'class' => 'std-pill-izin'],
            'A' => ['label' => 'Alpha', 'class' => 'std-pill-alpha'],
        ];
    @endphp

    <style>
        .std-wrap {
            margin: 18px 0;
            background: #f4f6f3;
            border: 1px solid #d7ddd4;
            border-radius: 16px;
            box-shadow: 0 12px 28px rgba(23, 42, 27, 0.14);
            padding: 14px;
        }

        .std-grid {
            display: grid;
            grid-template-columns: 1.2fr .9fr;
            gap: 12px;
        }

        .std-card {
            background: #ffffff;
            border: 1px solid #dbe2d8;
            border-radius: 12px;
            padding: 12px;
        }

        .std-profile {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
        }

        .std-profile-main {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .std-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: #98c25f;
            color: #2a4a19;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }

        .std-name {
            margin: 0;
            font-size: 17px;
            font-weight: 700;
            color: #1d2f1f;
        }

        .std-sub {
            margin: 2px 0 0;
            font-size: 12px;
            color: #5d6d5e;
        }

        .std-active {
            border-radius: 999px;
            padding: 4px 10px;
            background: #ebf6d9;
            color: #44632f;
            font-weight: 700;
            font-size: 11px;
            white-space: nowrap;
        }

        .std-clock {
            background: #196f33;
            color: #fff;
            border: 0;
            min-height: 198px;
        }

        .std-time {
            margin: 0;
            font-size: 44px;
            font-weight: 700;
            line-height: 1;
            letter-spacing: .6px;
        }

        .std-seconds {
            font-size: 20px;
            opacity: .86;
        }

        .std-date {
            margin: 8px 0 10px;
            font-size: 13px;
            color: #d3efd8;
        }

        .std-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 7px;
            background: rgba(255, 255, 255, .14);
            padding: 5px 10px;
            font-size: 12px;
            font-weight: 600;
        }

        .std-calendar-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .std-calendar-title {
            margin: 0;
            font-size: 15px;
            font-weight: 700;
            color: #2f4230;
            text-transform: capitalize;
        }

        .std-week,
        .std-days {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            gap: 4px;
        }

        .std-week span {
            font-size: 10px;
            text-align: center;
            color: #6f7e70;
            font-weight: 700;
            padding: 3px 0;
        }

        .std-day {
            border-radius: 7px;
            min-height: 23px;
            font-size: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #4a594b;
            background: #f3f7f2;
        }

        .std-day.empty {
            background: transparent;
        }

        .std-day.today {
            background: #196f33;
            color: #fff;
            font-weight: 700;
        }

        .std-mini-grid {
            margin-top: 10px;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
        }

        .std-mini-title {
            margin: 0;
            font-size: 12px;
            color: #6b7b6b;
        }

        .std-mini-value {
            margin: 4px 0 0;
            font-size: 31px;
            line-height: 1;
            color: #253425;
            font-weight: 700;
        }

        .std-mini-foot {
            margin: 3px 0 0;
            font-size: 11px;
            color: #7b887b;
        }

        .std-list-head {
            margin-top: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .std-list-title {
            margin: 0;
            font-size: 17px;
            color: #1f2f20;
            font-weight: 700;
        }

        .std-link {
            color: #2e6f3f;
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
        }

        .std-items {
            margin-top: 8px;
            display: grid;
            gap: 8px;
        }

        .std-item {
            background: #fff;
            border: 1px solid #dce4da;
            border-radius: 10px;
            padding: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
        }

        .std-item-date {
            margin: 0;
            font-weight: 700;
            color: #2b3a2c;
            font-size: 13px;
        }

        .std-item-sub {
            margin: 2px 0 0;
            color: #6a796b;
            font-size: 12px;
        }

        .std-pill {
            border-radius: 999px;
            padding: 5px 10px;
            font-size: 11px;
            font-weight: 700;
            white-space: nowrap;
        }

        .std-pill-hadir { background: #e5f7df; color: #2f7f3d; }
        .std-pill-izin { background: #fff5d8; color: #896717; }
        .std-pill-alpha { background: #fde9ea; color: #a84756; }

        .std-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 8px;
        }

        @media (max-width: 992px) {
            .std-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 767px) {
            .std-mini-grid { grid-template-columns: 1fr; }
            .std-time { font-size: 36px; }
            .std-wrap { padding: 10px; border-radius: 12px; }
        }
    </style>

    <div class="std-wrap">
        @if(session('dashboard_error'))
            <div class="alert alert-danger" style="margin-bottom: 10px;">{{ session('dashboard_error') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger" style="margin-bottom: 10px;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="std-card std-profile">
            <div class="std-profile-main">
                <span class="std-avatar">{{ strtoupper(substr((string) ($sessionUser['nama'] ?? 'S'), 0, 2)) }}</span>
                <div>
                    <p class="std-name">{{ $sessionUser['nama'] ?? 'Siswa' }}</p>
                    <p class="std-sub">NIS {{ $sessionUser['identifier'] ?? '-' }} • Dashboard Siswa</p>
                </div>
            </div>
            <span class="std-active"><i class="fa fa-circle" style="font-size:8px;"></i> Aktif</span>
        </div>

        <div class="std-actions">
            <a href="{{ route('student-attendance') }}" class="btn btn-success btn-sm"><i class="fa fa-check-square-o"></i> Absensi</a>
            <a href="{{ route('student-schedule-today') }}" class="btn btn-default btn-sm"><i class="fa fa-calendar"></i> Jadwal Hari Ini</a>
        </div>

        <div class="std-grid" style="margin-top: 10px;">
            <div class="std-card std-clock">
                <p class="std-time"><span id="std-hour-minute">{{ $now->format('H:i') }}</span><span class="std-seconds" id="std-seconds">:{{ $now->format('s') }}</span></p>
                <p class="std-date" id="std-full-date">{{ $now->translatedFormat('l, d F Y') }}</p>
                <span class="std-tag"><i class="fa fa-check-square-o"></i> {{ $clockTagText }}</span>
            </div>

            <div class="std-card">
                <div class="std-calendar-head">
                    <p class="std-calendar-title">{{ $monthTitle }}</p>
                    <span class="text-muted" style="font-size: 11px;"><i class="fa fa-calendar"></i></span>
                </div>

                <div class="std-week">
                    <span>S</span><span>S</span><span>R</span><span>K</span><span>J</span><span>S</span><span>M</span>
                </div>
                <div class="std-days">
                    @foreach($calendarCells as $cell)
                        @if($cell === null)
                            <span class="std-day empty"></span>
                        @else
                            <span class="std-day {{ $cell === $todayDate ? 'today' : '' }}">{{ $cell }}</span>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

        <div class="std-mini-grid">
            <div class="std-card">
                <p class="std-mini-title">Hadir</p>
                <p class="std-mini-value">{{ $monthHadir }}</p>
                <p class="std-mini-foot">Bulan ini</p>
            </div>
            <div class="std-card">
                <p class="std-mini-title">Alpha</p>
                <p class="std-mini-value">{{ $monthAlpha }}</p>
                <p class="std-mini-foot">Bulan ini</p>
            </div>
            <div class="std-card">
                <p class="std-mini-title">Izin</p>
                <p class="std-mini-value">{{ $monthIzin }}</p>
                <p class="std-mini-foot">Bulan ini</p>
            </div>
        </div>

        <div class="std-list-head">
            <h4 class="std-list-title">Absensi terbaru</h4>
            <a class="std-link" href="{{ route('student-attendance') }}">Lihat semua</a>
        </div>

        <div class="std-items">
            @forelse($tableRows as $row)
                @php
                    $statusCode = strtoupper((string) ($row->status ?? ''));
                    $meta = $statusMeta[$statusCode] ?? ['label' => '-', 'class' => 'std-pill-alpha'];
                @endphp
                <div class="std-item">
                    <div>
                        <p class="std-item-date">{{ optional($row->tanggal)->translatedFormat('l, j F Y') ?? '-' }}</p>
                        <p class="std-item-sub">{{ $row->subject->nama_mp ?? 'Mata pelajaran tidak diketahui' }}</p>
                    </div>
                    <span class="std-pill {{ $meta['class'] }}">{{ $meta['label'] }}</span>
                </div>
            @empty
                <div class="std-item">
                    <div>
                        <p class="std-item-date">Belum ada data absensi</p>
                        <p class="std-item-sub">Data riwayat absensi terbaru akan tampil di sini.</p>
                    </div>
                    <span class="std-pill std-pill-izin">Info</span>
                </div>
            @endforelse
        </div>
    </div>

    <script>
        (function () {
            'use strict';
            var hm = document.getElementById('std-hour-minute');
            var sec = document.getElementById('std-seconds');
            var full = document.getElementById('std-full-date');
            if (!hm || !sec || !full) {
                return;
            }

            function pad(value) {
                return String(value).padStart(2, '0');
            }

            function updateClock() {
                var now = new Date();
                hm.textContent = pad(now.getHours()) + ':' + pad(now.getMinutes());
                sec.textContent = ':' + pad(now.getSeconds());

                var weekdays = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                var months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                full.textContent = weekdays[now.getDay()] + ', ' + pad(now.getDate()) + ' ' + months[now.getMonth()] + ' ' + now.getFullYear();
            }

            updateClock();
            setInterval(updateClock, 1000);
        })();
    </script>
@endsection
