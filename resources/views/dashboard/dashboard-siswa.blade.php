@extends('layouts.app')

@section('content')
	@php
		$cards = $dashboard['cards'] ?? [];
		$status = $dashboard['status'] ?? ['Hadir' => 0, 'Izin' => 0, 'Alpha' => 0];
		$trend = $dashboard['trend'] ?? ['labels' => [], 'hadir' => [], 'izin' => [], 'alpha' => []];
		$performance = $dashboard['performance'] ?? collect();
		$tableRows = $dashboard['tableRows'] ?? collect();
		$totalStatus = array_sum($status);
		$pctHadir = $totalStatus > 0 ? round(($status['Hadir'] / $totalStatus) * 100) : 0;
		$pctIzin = $totalStatus > 0 ? round(($status['Izin'] / $totalStatus) * 100) : 0;
		$pctAlpha = max(0, 100 - $pctHadir - $pctIzin);

		$allSeries = array_merge($trend['hadir'] ?? [], $trend['izin'] ?? [], $trend['alpha'] ?? []);
		$maxSeries = !empty($allSeries) ? max($allSeries) : 0;
		$maxSeries = $maxSeries > 0 ? $maxSeries : 1;
	@endphp

	<style>
		.student-shell {
			background: radial-gradient(circle at top left, #eef7ff 0%, #fbfdff 45%, #f6fbf8 100%);
			border: 3px solid #21455a;
			border-radius: 24px;
			padding: 24px;
			margin: 20px 0;
			box-shadow: 0 16px 34px rgba(20, 45, 60, 0.12);
		}

		.student-headline {
			margin: 0;
			font-size: 30px;
			color: #143244;
			font-weight: 700;
			letter-spacing: 0.2px;
		}

		.student-subhead {
			margin-top: 6px;
			color: #64798a;
			font-size: 14px;
		}

		.student-actions {
			display: flex;
			gap: 10px;
			flex-wrap: wrap;
			margin-top: 14px;
		}

		.student-card-grid {
			margin-top: 20px;
			display: grid;
			grid-template-columns: repeat(4, minmax(170px, 1fr));
			gap: 14px;
		}

		.student-stat {
			background: linear-gradient(160deg, #ffffff 0%, #eef8ff 100%);
			border: 1px solid #d9e6ef;
			border-radius: 14px;
			padding: 14px;
			min-height: 108px;
			position: relative;
			overflow: hidden;
		}

		.student-stat-top {
			display: flex;
			justify-content: space-between;
			align-items: center;
		}

		.student-stat-label {
			margin: 0;
			color: #708092;
			font-size: 12px;
			text-transform: uppercase;
			letter-spacing: 0.5px;
		}

		.student-stat-icon {
			width: 30px;
			height: 30px;
			border-radius: 8px;
			display: inline-flex;
			align-items: center;
			justify-content: center;
			color: #18506a;
			background: #dff2ff;
		}

		.student-stat-value {
			margin: 8px 0 0;
			font-size: 30px;
			line-height: 1;
			color: #123244;
			font-weight: 700;
		}

		.student-stat-foot {
			margin: 7px 0 0;
			color: #7a8a97;
			font-size: 12px;
		}

		.student-main-grid {
			margin-top: 16px;
			display: grid;
			grid-template-columns: 1.4fr 0.9fr;
			gap: 14px;
		}

		.student-panel {
			background: #fff;
			border: 1px solid #dce8ef;
			border-radius: 16px;
			padding: 16px;
			min-height: 250px;
		}

		.student-panel-title {
			margin: 0 0 14px;
			color: #143244;
			font-weight: 700;
			font-size: 19px;
		}

		.student-bars {
			display: grid;
			grid-template-columns: repeat(5, minmax(64px, 1fr));
			gap: 12px;
			align-items: end;
			min-height: 200px;
			padding-top: 8px;
		}

		.student-bar-wrap {
			text-align: center;
		}

		.student-bar-stack {
			display: flex;
			align-items: flex-end;
			justify-content: center;
			gap: 3px;
			min-height: 156px;
			margin-bottom: 8px;
		}

		.student-bar {
			width: 14px;
			border-radius: 6px;
			transition: height .4s ease;
		}

		.student-bar.hadir { background: #1477a3; }
		.student-bar.izin { background: #69a7c7; }
		.student-bar.alpha { background: #c6ddeb; }

		.student-month {
			font-size: 12px;
			color: #6a7e8f;
		}

		.student-legend {
			display: flex;
			gap: 14px;
			margin-top: 8px;
			flex-wrap: wrap;
		}

		.student-legend-item {
			display: inline-flex;
			align-items: center;
			gap: 6px;
			color: #607382;
			font-size: 12px;
		}

		.student-dot {
			width: 10px;
			height: 10px;
			border-radius: 50%;
		}

		.student-ring-box {
			display: flex;
			justify-content: center;
			align-items: center;
			margin: 10px 0 14px;
		}

		.student-ring {
			width: 156px;
			height: 156px;
			border-radius: 50%;
			background: conic-gradient(
				#1477a3 0% {{ $pctHadir }}%,
				#69a7c7 {{ $pctHadir }}% {{ $pctHadir + $pctIzin }}%,
				#c6ddeb {{ $pctHadir + $pctIzin }}% 100%
			);
			position: relative;
			box-shadow: inset 0 0 0 8px #ffffff;
		}

		.student-ring::after {
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
			color: #123244;
			font-weight: 700;
			font-size: 28px;
		}

		.student-status-row {
			display: flex;
			justify-content: space-between;
			font-size: 13px;
			padding: 4px 0;
			color: #547082;
		}

		.student-lower {
			margin-top: 14px;
			display: grid;
			grid-template-columns: 1fr 1.4fr;
			gap: 14px;
		}

		.student-perf-item {
			margin-bottom: 12px;
		}

		.student-perf-label {
			display: flex;
			justify-content: space-between;
			color: #4e6676;
			font-size: 13px;
			margin-bottom: 6px;
		}

		.student-perf-track {
			width: 100%;
			height: 8px;
			border-radius: 6px;
			background: #e7f0f5;
			overflow: hidden;
		}

		.student-perf-fill {
			height: 100%;
			border-radius: 6px;
			background: linear-gradient(90deg, #1477a3 0%, #69a7c7 100%);
		}

		.student-table {
			width: 100%;
			border-collapse: collapse;
			font-size: 13px;
		}

		.student-table th,
		.student-table td {
			padding: 8px 10px;
			border-bottom: 1px solid #e9f0f4;
			color: #445965;
		}

		.student-table th {
			font-size: 12px;
			text-transform: uppercase;
			color: #7d919f;
		}

		.student-pill {
			border-radius: 999px;
			padding: 2px 8px;
			font-size: 11px;
			font-weight: 700;
			display: inline-block;
			min-width: 32px;
			text-align: center;
		}

		.student-pill-h { background: #dff2ff; color: #13546f; }
		.student-pill-i { background: #eaf6fb; color: #2f6f8c; }
		.student-pill-a { background: #eef7fb; color: #6d8a99; }

		@media (max-width: 1200px) {
			.student-card-grid { grid-template-columns: repeat(2, minmax(170px, 1fr)); }
			.student-main-grid { grid-template-columns: 1fr; }
			.student-lower { grid-template-columns: 1fr; }
		}

		@media (max-width: 767px) {
			.student-shell {
				border-width: 2px;
				border-radius: 16px;
				padding: 14px;
			}

			.student-headline { font-size: 24px; }
			.student-card-grid { grid-template-columns: 1fr; }
			.student-bars { grid-template-columns: repeat(5, minmax(44px, 1fr)); gap: 8px; }
			.student-bar { width: 10px; }
		}
	</style>

	<div class="student-shell">
		<h3 class="student-headline">Hi, {{ $sessionUser['nama'] ?? 'Siswa' }}</h3>
		<p class="student-subhead">{{ $dashboard['headline'] ?? 'Dashboard Siswa' }}. {{ $dashboard['subhead'] ?? '' }}</p>

		<div class="student-actions">
			<a href="{{ route('student-attendance') }}" class="btn btn-primary btn-sm">
				<i class="fa fa-check-square-o"></i> Buka Absensi
			</a>
			<a href="{{ route('student-schedule-today') }}" class="btn btn-info btn-sm">
				<i class="fa fa-calendar"></i> Jadwal Hari Ini
			</a>
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

		<div class="student-card-grid">
			@foreach($cards as $card)
				<div class="student-stat">
					<div class="student-stat-top">
						<p class="student-stat-label">{{ $card['label'] }}</p>
						<span class="student-stat-icon"><i class="fa {{ $card['icon'] }}"></i></span>
					</div>
					<p class="student-stat-value">{{ $card['value'] }}</p>
					<p class="student-stat-foot">{{ $card['delta'] }}</p>
				</div>
			@endforeach
		</div>

		<div class="student-main-grid">
			<div class="student-panel">
				<h4 class="student-panel-title">Tren Kehadiran 5 Bulan</h4>

				<div class="student-bars">
					@foreach($trend['labels'] as $idx => $label)
						@php
							$h = (int) ($trend['hadir'][$idx] ?? 0);
							$i = (int) ($trend['izin'][$idx] ?? 0);
							$a = (int) ($trend['alpha'][$idx] ?? 0);
						@endphp
						<div class="student-bar-wrap">
							<div class="student-bar-stack">
								<div class="student-bar hadir" style="height: {{ max(6, (int) round(($h / $maxSeries) * 140)) }}px" title="Hadir: {{ $h }}"></div>
								<div class="student-bar izin" style="height: {{ max(6, (int) round(($i / $maxSeries) * 140)) }}px" title="Izin: {{ $i }}"></div>
								<div class="student-bar alpha" style="height: {{ max(6, (int) round(($a / $maxSeries) * 140)) }}px" title="Alpha: {{ $a }}"></div>
							</div>
							<div class="student-month">{{ $label }}</div>
						</div>
					@endforeach
				</div>

				<div class="student-legend">
					<span class="student-legend-item"><span class="student-dot" style="background:#1477a3;"></span>Hadir</span>
					<span class="student-legend-item"><span class="student-dot" style="background:#69a7c7;"></span>Izin</span>
					<span class="student-legend-item"><span class="student-dot" style="background:#c6ddeb;"></span>Alpha</span>
				</div>
			</div>

			<div class="student-panel">
				<h4 class="student-panel-title">Komposisi Status</h4>
				<div class="student-ring-box">
					<div class="student-ring"></div>
				</div>
				<div class="student-status-row"><span>Hadir</span><strong>{{ $status['Hadir'] ?? 0 }} ({{ $pctHadir }}%)</strong></div>
				<div class="student-status-row"><span>Izin</span><strong>{{ $status['Izin'] ?? 0 }} ({{ $pctIzin }}%)</strong></div>
				<div class="student-status-row"><span>Alpha</span><strong>{{ $status['Alpha'] ?? 0 }} ({{ $pctAlpha }}%)</strong></div>
			</div>
		</div>

		<div class="student-lower">
			<div class="student-panel">
				<h4 class="student-panel-title">Distribusi Mapel Anda</h4>

				@forelse($performance as $row)
					@php
						$denominator = (int) ($row['total'] ?? 0);
						$numerator = (int) ($row['hadir'] ?? 0);
						$percent = $totalStatus > 0 ? (int) round(($numerator / $totalStatus) * 100) : 0;
					@endphp
					<div class="student-perf-item">
						<div class="student-perf-label">
							<span>{{ $row['label'] }}</span>
							<span>{{ $numerator }} data</span>
						</div>
						<div class="student-perf-track">
							<div class="student-perf-fill" style="width: {{ max(4, $percent) }}%"></div>
						</div>
					</div>
				@empty
					<p class="text-muted" style="margin-top: 4px;">Belum ada data performa.</p>
				@endforelse
			</div>

			<div class="student-panel">
				<h4 class="student-panel-title">Riwayat Absensi Terbaru</h4>

				<div class="table-responsive">
					<table class="student-table">
						<thead>
							<tr>
								<th>Tanggal</th>
								<th>Mapel</th>
								<th>Status</th>
							</tr>
						</thead>
						<tbody>
							@forelse($tableRows as $row)
								@php
									$statusCode = strtoupper((string) ($row->status ?? ''));
									$pillClass = $statusCode === 'H' ? 'student-pill-h' : ($statusCode === 'I' ? 'student-pill-i' : 'student-pill-a');
								@endphp
								<tr>
									<td>{{ optional($row->tanggal)->format('d M Y') ?? '-' }}</td>
									<td>{{ $row->subject->nama_mp ?? '-' }}</td>
									<td><span class="student-pill {{ $pillClass }}">{{ $statusCode ?: '-' }}</span></td>
								</tr>
							@empty
								<tr>
									<td colspan="3" class="text-center text-muted">Belum ada data terbaru.</td>
								</tr>
							@endforelse
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
@endsection
