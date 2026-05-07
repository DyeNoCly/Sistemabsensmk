<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <link href="{{ asset('legacy/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('legacy/css/sb-admin.css') }}" rel="stylesheet">
    <link href="{{ asset('legacy/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css">
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            padding-top: 72px;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            color: #1f2a3d;
            background: linear-gradient(120deg, rgba(18, 42, 78, 0.78), rgba(15, 74, 118, 0.66));
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background: url('{{ asset('images/school-background-smk3hd.png') }}') center center / cover no-repeat;
            opacity: 0.13;
            pointer-events: none;
            z-index: 0;
        }

        body > * {
            position: relative;
            z-index: 1;
        }

        #wrapper {
            position: relative;
            z-index: 1;
        }

        #page-wrapper {
            background: transparent;
            box-shadow: none;
            border-radius: 0;
            margin-left: 250px;
            padding-top: 0;
            transition: margin-left 0.3s;
        }

        body.route-inner #page-wrapper .container-fluid {
            padding-top: 18px;
        }

        .page-title {
            margin-top: 18px;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .panel {
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08);
            border: 0;
            border-radius: 8px;
        }

        .panel-heading {
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
            font-weight: 600;
        }

        .sidebar .nav>li>a {
            font-weight: 500;
        }

        .logout-inline {
            margin: 0;
            padding: 0;
        }

        .logout-inline button {
            margin: 0 15px 0 0;
        }

        .dashboard-topbar {
            background: linear-gradient(90deg, rgba(17, 48, 90, 0.92), rgba(22, 102, 160, 0.9));
            border-bottom: 1px solid rgba(187, 224, 255, 0.45);
            box-shadow: 0 8px 22px rgba(6, 33, 58, 0.35);
            min-height: 72px;
            backdrop-filter: blur(7px);
            -webkit-backdrop-filter: blur(7px);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
        }

        .dashboard-topbar .navbar-header {
            min-height: 72px;
        }

        .dashboard-topbar .navbar-brand {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            height: auto;
            padding-top: 12px;
            padding-bottom: 12px;
            color: #f2f9ff;
            font-family: Arial, Helvetica, sans-serif;
            font-weight: 700;
            letter-spacing: 0.3px;
        }

        .dashboard-topbar .navbar-brand:hover,
        .dashboard-topbar .navbar-brand:focus {
            color: #ffffff;
        }

        .dashboard-topbar .navbar-brand img {
            height: 46px;
            width: auto;
            object-fit: contain;
        }

        .dashboard-topbar .brand-stack {
            display: inline-flex;
            flex-direction: column;
            line-height: 1.15;
        }

        .dashboard-topbar .brand-title {
            font-size: 16px;
        }

        .dashboard-topbar .topbar-note {
            color: #d5ecff;
            font-size: 12px;
            letter-spacing: 0.3px;
            text-transform: uppercase;
            font-weight: 600;
        }

        .navbar-top-links {
            display: flex;
            align-items: center;
        }

        .navbar-top-links > li {
            display: flex;
            align-items: center;
            height: 50px;
        }

        .navbar-top-links > li > a {
            display: inline-flex;
            align-items: center;
            height: 50px;
            padding-top: 0;
            padding-bottom: 0;
        }

        .dashboard-topbar .navbar-top-links > li {
            height: 72px;
        }

        .dashboard-topbar .navbar-top-links > li > a {
            height: 72px;
            color: #f2f9ff;
        }

        .dashboard-topbar .navbar-top-links > li > a:hover,
        .dashboard-topbar .navbar-top-links > li > a:focus {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.08);
        }

        .dashboard-topbar .navbar-toggle {
            margin-top: 19px;
            border-color: rgba(187, 224, 255, 0.6);
        }

        .dashboard-topbar .navbar-toggle .icon-bar {
            background-color: #eaf5ff;
        }

        @media (max-width: 767px) {
            .dashboard-topbar .navbar-brand {
                gap: 8px;
                padding-top: 16px;
                padding-bottom: 16px;
            }

            .dashboard-topbar .navbar-brand img {
                height: 36px;
            }

            .dashboard-topbar .brand-title {
                font-size: 14px;
            }

            .dashboard-topbar .topbar-note {
                display: none;
            }

            body.route-inner #page-wrapper .container-fluid {
                padding-top: 14px;
            }
        }

        @media (min-width: 768px) {
            .navbar-static-side {
                position: fixed !important;
                top: 72px;
                left: 0;
                bottom: 0;
                width: 250px;
                overflow-y: auto;
                overflow-x: hidden;
                z-index: 1020;
                margin-top: 0;
            }

            .sidebar-collapse {
                display: block !important;
                visibility: visible !important;
                opacity: 1 !important;
                height: auto !important;
            }

            #side-menu {
                display: block !important;
                visibility: visible !important;
                opacity: 1 !important;
            }

            #side-menu > li {
                display: block !important;
            }

            #page-wrapper {
                margin-left: 250px;
                padding-top: 0;
                min-height: 100vh;
            }
        }

        @media (max-width: 767px) {
            .navbar-static-side {
                position: fixed !important;
                top: 72px;
                left: 0;
                right: 0;
                width: 100%;
                max-height: calc(100vh - 72px);
                overflow-y: auto;
                overflow-x: hidden;
                z-index: 1025;
            }

            .sidebar-collapse.collapse {
                display: none !important;
            }

            .sidebar-collapse.in {
                display: block !important;
                visibility: visible !important;
                opacity: 1 !important;
            }

            #page-wrapper {
                margin-left: 0;
                padding-top: 0;
            }
        }

        .navbar-static-side {
            background: linear-gradient(180deg, rgba(17, 48, 90, 0.92), rgba(22, 102, 160, 0.9));
            border-right: 1px solid rgba(187, 224, 255, 0.35);
            box-shadow: 4px 0 18px rgba(6, 33, 58, 0.2);
        }

        .sidebar-collapse,
        #side-menu {
            background: transparent;
        }

        #side-menu > li > a {
            color: #eef7ff;
            border-bottom: 1px solid rgba(187, 224, 255, 0.18);
        }

        #side-menu > li > a:hover,
        #side-menu > li > a:focus {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.1);
        }

        /* Compatibility layer for Bootstrap 5 utility classes used in existing views. */
        .card {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08);
        }

        .p-3 { padding: 15px; }
        .mb-0 { margin-bottom: 0; }
        .mb-1 { margin-bottom: 5px; }
        .mb-3 { margin-bottom: 15px; }
        .mb-4 { margin-bottom: 20px; }
        .mt-3 { margin-top: 15px; }
        .py-2 {
            padding-top: 10px;
            padding-bottom: 10px;
        }
        .w-100 { width: 100%; }
        .text-muted { color: #777; }
        .text-center { text-align: center; }

        .d-flex { display: flex; }
        .justify-content-between { justify-content: space-between; }
        .align-items-center { align-items: center; }
        .gap-1 { gap: 4px; }
        .gap-2 { gap: 8px; }

        .form-label {
            display: inline-block;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .form-select {
            display: block;
            width: 100%;
            height: 34px;
            padding: 6px 12px;
            font-size: 14px;
            line-height: 1.42857143;
            color: #555;
            background-color: #fff;
            background-image: none;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
            transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
        }

        .btn-outline-primary,
        .btn-outline-secondary {
            color: #333;
            background-color: #fff;
            border: 1px solid #ccc;
        }

        .text-bg-success,
        .text-bg-primary {
            color: #fff;
            border-radius: 12px;
            margin-right: 5px;
            margin-bottom: 5px;
            display: inline-block;
            text-decoration: none;
        }

        .text-bg-success {
            background: #5cb85c;
        }

        .text-bg-primary {
            background: #337ab7;
        }

        .module-grid a:hover {
            opacity: 0.9;
            text-decoration: none;
        }

        .pagination {
            margin-top: 15px;
        }

        .user-display {
            display: inline-flex !important;
            align-items: center;
            gap: 8px;
        }

        .user-display .fa {
            margin-top: 0;
            line-height: 1;
        }

        .page-loading-overlay {
            position: fixed;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
            background: rgba(247, 249, 255, 0.88);
            backdrop-filter: blur(3px);
            -webkit-backdrop-filter: blur(3px);
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transition: opacity 0.18s ease, visibility 0.18s ease;
            z-index: 9999;
        }

        .page-loading-overlay.is-active {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }

        .page-loading-spinner {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            border: 4px solid rgba(91, 56, 145, 0.18);
            border-top-color: #5b3891;
            animation: page-loading-spin 0.8s linear infinite;
        }

        .page-loading-text {
            margin: 0;
            color: #392751;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 0.2px;
        }

        body.is-navigating {
            cursor: progress;
        }

        @keyframes page-loading-spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
</head>
<body class="{{ request()->routeIs('dashboard') ? 'route-dashboard' : 'route-inner' }}">
    @php
        $sessionUser = session('legacy_user', []);
        $role = $sessionUser['level'] ?? '';
    @endphp

    <div id="wrapper">
        <nav class="navbar navbar-default navbar-static-top dashboard-topbar" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".sidebar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="{{ route('dashboard') }}">
                    <img src="{{ asset('images/school-logo-smk3hd.png') }}" alt="Logo sekolah">
                    <span class="brand-stack">
                        <span class="brand-title">SMKN 3 Kota Tangerang Selatan</span>
                        <span class="topbar-note">Sistem Absensi</span>
                    </span>
                </a>
            </div>

            <ul class="nav navbar-top-links navbar-right">
                <li>
                    <a href="{{ route('dashboard') }}" class="user-display">
                        <i class="fa fa-user fa-fw"></i>
                        <span>{{ $sessionUser['nama'] ?? '-' }}</span>
                    </a>
                </li>
                <li>
                    <form method="post" action="{{ route('logout') }}" class="logout-inline">
                        @csrf
                        <button class="btn btn-danger btn-xs" type="submit">
                            <i class="fa fa-sign-out"></i> Logout
                        </button>
                    </form>
                </li>
            </ul>
        </nav>

        <div class="navbar-default navbar-static-side" role="navigation">
            <div class="sidebar-collapse collapse">
                <ul class="nav" id="side-menu">
                        <li>
                            <a href="{{ route('dashboard') }}"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
                        </li>

                        @if(in_array($role, ['admin', 'guru'], true))
                            <li>
                                <a href="{{ route('students.index') }}"><i class="fa fa-users fa-fw"></i> Data Siswa</a>
                            </li>
                            <li>
                                <a href="{{ route('subjects.index') }}"><i class="fa fa-book fa-fw"></i> Mata Pelajaran</a>
                            </li>
                            <li>
                                <a href="{{ route('schedules.index') }}"><i class="fa fa-calendar fa-fw"></i> Jadwal</a>
                            </li>
                            <li>
                                <a href="{{ route('attendances.index') }}"><i class="fa fa-check-square fa-fw"></i> Absensi</a>
                            </li>
                        @endif

                        @if($role === 'admin')
                            <li>
                                <a href="{{ route('teachers.index') }}"><i class="fa fa-user fa-fw"></i> Data Guru</a>
                            </li>
                            <li>
                                <a href="{{ route('classes.index') }}"><i class="fa fa-building-o fa-fw"></i> Data Kelas</a>
                            </li>
                        @endif

                        @if($role === 'guru')
                            <li>
                                <a href="{{ route('reports.teacher-recap') }}"><i class="fa fa-file-text fa-fw"></i> Rekap Guru</a>
                            </li>
                        @endif

                        @if($role === 'user')
                            <li>
                                <a href="{{ route('student-schedule-today') }}"><i class="fa fa-calendar fa-fw"></i> Jadwal Hari Ini</a>
                            </li>
                            <li>
                                <a href="{{ route('student-attendance') }}"><i class="fa fa-check-square-o fa-fw"></i> Absensi</a>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>

        <div id="page-wrapper">
            <div class="container-fluid">
                @if(session('status'))
                    <div class="alert alert-success" style="margin-top: 20px;">
                        {{ session('status') }}
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <div id="page-loading-overlay" class="page-loading-overlay" aria-hidden="true" role="status" aria-live="polite">
        <div class="page-loading-spinner" aria-hidden="true"></div>
        <p class="page-loading-text">Memuat halaman...</p>
    </div>

    <script src="{{ asset('legacy/js/jquery-1.10.2.js') }}"></script>
    <script src="{{ asset('legacy/js/bootstrap.min.js') }}"></script>
    <script>
        (function () {
            var overlay = document.getElementById('page-loading-overlay');
            if (!overlay) {
                return;
            }

            var body = document.body;
            var loadingDelay = 250;
            var loadingTimer = null;

            function clearLoadingTimer() {
                if (loadingTimer) {
                    clearTimeout(loadingTimer);
                    loadingTimer = null;
                }
            }

            function showOverlay() {
                overlay.classList.add('is-active');
                overlay.setAttribute('aria-hidden', 'false');
                body.classList.add('is-navigating');
            }

            function hideOverlay() {
                clearLoadingTimer();
                overlay.classList.remove('is-active');
                overlay.setAttribute('aria-hidden', 'true');
                body.classList.remove('is-navigating');
            }

            window.addEventListener('pageshow', hideOverlay);

            document.addEventListener('click', function (event) {
                if (event.defaultPrevented || event.button !== 0) {
                    return;
                }

                if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
                    return;
                }

                var link = event.target.closest('a');
                if (!link) {
                    return;
                }

                if (link.hasAttribute('download')) {
                    return;
                }

                if (link.target && link.target !== '_self') {
                    return;
                }

                var href = link.getAttribute('href') || '';
                if (!href || href.charAt(0) === '#' || href.indexOf('javascript:') === 0) {
                    return;
                }

                if (/^(mailto:|tel:|http:|https:|\/\/)/i.test(href)) {
                    try {
                        var destination = new URL(href, window.location.href);
                        if (destination.origin !== window.location.origin) {
                            return;
                        }
                    } catch (error) {
                        return;
                    }
                }

                var isSamePageAnchor = link.pathname === window.location.pathname && link.search === window.location.search && link.hash;
                if (isSamePageAnchor) {
                    return;
                }

                clearLoadingTimer();
                loadingTimer = setTimeout(function () {
                    showOverlay();
                }, loadingDelay);
            });

            document.addEventListener('submit', function (event) {
                var form = event.target;
                if (!(form instanceof HTMLFormElement)) {
                    return;
                }

                if (event.defaultPrevented) {
                    return;
                }

                if (!form.checkValidity()) {
                    return;
                }

                clearLoadingTimer();
                loadingTimer = setTimeout(function () {
                    showOverlay();
                }, loadingDelay);
            });
        })();
    </script>
</body>
</html>
