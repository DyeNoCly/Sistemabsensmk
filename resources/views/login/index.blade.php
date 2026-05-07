<!DOCTYPE html>
<html lang="id">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Login - Sistem Absensi</title>
	<link href="{{ asset('legacy/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css">
	<style>
		* { box-sizing: border-box; }

		body {
			margin: 0;
			min-height: 100vh;
			font-family: "Georgia", "Times New Roman", serif;
			color: #f7f7fb;
			background: linear-gradient(120deg, rgba(18, 42, 78, 0.78), rgba(15, 74, 118, 0.66));
			display: flex;
			align-items: center;
			justify-content: center;
			padding: 96px 18px 24px;
			position: relative;
			overflow: hidden;
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

		.login-topbar {
			position: fixed;
			top: 0;
			left: 0;
			width: 100%;
			height: 72px;
			display: flex;
			align-items: center;
			justify-content: space-between;
			padding: 0 20px;
			background: linear-gradient(90deg, rgba(17, 48, 90, 0.92), rgba(22, 102, 160, 0.9));
			border-bottom: 1px solid rgba(187, 224, 255, 0.45);
			box-shadow: 0 8px 22px rgba(6, 33, 58, 0.35);
			backdrop-filter: blur(7px);
			-webkit-backdrop-filter: blur(7px);
			z-index: 3;
		}

		.topbar-brand {
			display: inline-flex;
			align-items: center;
			gap: 10px;
			color: #f2f9ff;
			font-family: Arial, Helvetica, sans-serif;
			font-weight: 700;
			letter-spacing: 0.3px;
		}

		.topbar-brand img {
			height: 46px;
			width: auto;
			object-fit: contain;
		}

		.topbar-note {
			color: #d5ecff;
			font-size: 12px;
			font-family: Arial, Helvetica, sans-serif;
			letter-spacing: 0.3px;
			text-transform: uppercase;
		}

		.login-stage {
			width: min(720px, 96vw);
			min-height: min(700px, calc(100vh - 130px));
			border-radius: 24px;
			overflow: hidden;
			position: relative;
			display: flex;
			align-items: center;
			justify-content: center;
			padding: 32px;
			background: linear-gradient(145deg, rgba(17, 48, 90, 0.34), rgba(19, 96, 150, 0.3));
			border: 1px solid rgba(197, 225, 255, 0.35);
			box-shadow: 0 30px 70px rgba(8, 35, 62, 0.45);
		}

		.glass-card {
			width: min(510px, 100%);
			border-radius: 36px;
			padding: 36px 34px 30px;
			backdrop-filter: blur(11px);
			-webkit-backdrop-filter: blur(11px);
			background: linear-gradient(150deg, rgba(225, 240, 255, 0.32), rgba(88, 150, 212, 0.23));
			border: 1px solid rgba(210, 234, 255, 0.45);
			box-shadow: 0 18px 45px rgba(8, 43, 79, 0.34);
		}

		.school-logo-wrap {
			display: flex;
			justify-content: center;
			margin-bottom: 10px;
		}

		.school-logo {
			height: 104px;
			width: auto;
			object-fit: contain;
			filter: drop-shadow(0 4px 10px rgba(9, 44, 78, 0.35));
		}

		.card-head {
			display: flex;
			align-items: baseline;
			justify-content: space-between;
			margin-bottom: 26px;
		}

		.card-title {
			margin: 0;
			font-size: 42px;
			font-weight: 700;
			letter-spacing: 0.5px;
		}

		.school-name {
			margin: -8px 0 18px;
			color: #eaf5ff;
			opacity: 0.9;
			font-size: 16px;
		}

		.alert-box {
			margin-bottom: 15px;
			padding: 10px 14px;
			border-radius: 12px;
			border: 1px solid rgba(255, 179, 179, 0.45);
			background: rgba(132, 30, 30, 0.35);
			color: #ffe9e9;
			font-family: Arial, Helvetica, sans-serif;
			font-size: 14px;
		}

		.form-row { margin-bottom: 13px; }

		.field-label {
			display: block;
			margin-bottom: 5px;
			font-size: 31px;
			color: #f7f6ff;
			font-weight: 600;
			line-height: 1;
		}

		.field-input {
			width: 100%;
			height: 40px;
			border: 0;
			border-radius: 9px;
			padding: 8px 12px;
			background: #f2f8ff;
			color: #10243d;
			box-shadow: inset 0 1px 2px rgba(9, 39, 67, 0.15);
			font-family: Arial, Helvetica, sans-serif;
			font-size: 15px;
		}

		.field-input:focus {
			outline: 2px solid rgba(121, 183, 244, 0.95);
			outline-offset: 1px;
		}

		.form-foot {
			display: flex;
			align-items: center;
			justify-content: flex-end;
			gap: 14px;
			margin-top: 2px;
		}

		.login-btn {
			border: 0;
			border-radius: 8px;
			min-width: 100px;
			height: 40px;
			font-family: Arial, Helvetica, sans-serif;
			font-weight: 700;
			font-size: 18px;
			color: #f3f9ff;
			background: linear-gradient(160deg, #2c7fc5 0%, #1f69ab 100%);
			cursor: pointer;
			box-shadow: 0 8px 20px rgba(8, 41, 70, 0.35);
			transition: transform 0.16s ease, box-shadow 0.16s ease;
		}

		.login-btn:hover {
			transform: translateY(-1px);
			box-shadow: 0 10px 23px rgba(6, 38, 67, 0.44);
		}

		.page-loading-overlay {
			position: fixed;
			inset: 0;
			display: flex;
			align-items: center;
			justify-content: center;
			gap: 14px;
			background: rgba(9, 27, 51, 0.48);
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
			border: 4px solid rgba(215, 236, 255, 0.22);
			border-top-color: #f7fbff;
			animation: page-loading-spin 0.8s linear infinite;
		}

		.page-loading-text {
			margin: 0;
			color: #f7fbff;
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

		@media (max-width: 980px) {
			.login-stage {
				min-height: auto;
				padding-top: 6px;
			}
		}

		@media (max-width: 560px) {
			body {
				padding: 84px 12px 18px;
			}

			.login-topbar {
				height: 64px;
				padding: 0 12px;
			}

			.topbar-brand {
				gap: 8px;
				font-size: 13px;
			}

			.topbar-brand img {
				height: 36px;
			}

			.topbar-note {
				display: none;
			}

			.glass-card {
				padding: 26px 22px 22px;
				border-radius: 22px;
			}

			.school-logo {
				height: 78px;
			}

			.card-title {
				font-size: 34px;
			}

			.field-label {
				font-size: 24px;
			}
		}
	</style>
</head>
<body>
	<header class="login-topbar">
		<div class="topbar-brand">
			<img src="{{ asset('images/school-logo-smk3hd.png') }}" alt="Logo sekolah">
			<span>SMKN 3 Kota Tangerang Selatan</span>
		</div>
		<div class="topbar-note">Sistem Absensi</div>
	</header>

	<main class="login-stage">
		<div class="glass-card">
			<div class="school-logo-wrap">
				<img src="{{ asset('images/school-logo-smk3hd.png') }}" alt="Logo sekolah" class="school-logo">
			</div>
			<div class="card-head">
				<h2 class="card-title">Selamat Datang!</h2>
			</div>

			<p class="school-name">Masukkan Username dan Password untuk melanjutkan</p>

			@if($errors->any())
				<div class="alert-box">{{ $errors->first() }}</div>
			@endif

			<form method="post" action="{{ route('login.submit') }}">
				@csrf
				<div class="form-row">
					<label class="field-label" for="username">Username</label>
					<input id="username" type="text" name="username" class="field-input" value="{{ old('username') }}" required autofocus>
				</div>

				<div class="form-row">
					<label class="field-label" for="password">Password</label>
					<input id="password" type="password" name="password" class="field-input" required>
				</div>

				<div class="form-foot">
					<button type="submit" class="login-btn">Log In</button>
				</div>
			</form>
		</div>
	</main>

	<div id="page-loading-overlay" class="page-loading-overlay" aria-hidden="true" role="status" aria-live="polite">
		<div class="page-loading-spinner" aria-hidden="true"></div>
		<p class="page-loading-text">Memuat halaman...</p>
	</div>

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

				if (link.hasAttribute('download') || (link.target && link.target !== '_self')) {
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