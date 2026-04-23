@extends('layouts.app')

@section('content')
    <style>
        .roster-shell {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 15px;
        }

        .roster-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 14px;
        }

        .roster-title {
            margin: 0;
            font-size: 22px;
            font-weight: 700;
            color: #333;
        }

        .roster-subtitle {
            margin: 4px 0 0;
            color: #777;
        }

        .roster-meta {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin: 10px 0 16px;
        }

        .roster-chip {
            background: #f5f5f5;
            color: #555;
            border: 1px solid #ddd;
            border-radius: 999px;
            padding: 5px 11px;
            font-size: 12px;
            font-weight: 600;
        }

        .roster-table {
            width: 100%;
            border-collapse: collapse;
        }

        .roster-table th,
        .roster-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }

        .roster-table th {
            text-transform: uppercase;
            font-size: 12px;
            color: #666;
        }

        .roster-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn-roster {
            min-width: 66px;
            border-radius: 4px;
            font-weight: 700;
            padding: 4px 10px;
            font-size: 12px;
        }

        .btn-roster.active {
            box-shadow: inset 0 0 0 2px rgba(0, 0, 0, 0.28);
        }

        .btn-roster[data-status="H"] { background: #29a96a; border-color: #29a96a; }
        .btn-roster[data-status="I"] { background: #d39a16; border-color: #d39a16; }
        .btn-roster[data-status="A"] { background: #c94c4c; border-color: #c94c4c; }

        .roster-help {
            margin-top: 12px;
            padding: 12px;
            background: #fcfcfc;
            border: 1px solid #eee;
            border-radius: 6px;
            color: #666;
            font-size: 13px;
        }
    </style>

    <div class="roster-shell">
        <div class="roster-header">
            <div>
                <h3 class="roster-title">Absensi Siswa</h3>
                <p class="roster-subtitle">Pilih status per siswa. Klik tombol untuk menyimpan langsung.</p>
            </div>
            <div>
                <a href="{{ route('dashboard') }}" class="btn btn-default btn-sm">
                    <i class="fa fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        <div class="roster-meta">
            <span class="roster-chip">Hari: {{ $schedule->day->hari ?? '-' }}</span>
            <span class="roster-chip">Kelas: {{ $schedule->kelas->nama ?? '-' }}</span>
            <span class="roster-chip">Mapel: {{ $schedule->subject->nama_mp ?? '-' }}</span>
            <span class="roster-chip">Guru: {{ $schedule->teacher->nama ?? '-' }}</span>
            <span class="roster-chip">Tanggal: {{ $date }}</span>
        </div>

        @if(session('status'))
            <div class="alert alert-success py-2">{{ session('status') }}</div>
        @endif

        @if(session('dashboard_error'))
            <div class="alert alert-danger py-2">{{ session('dashboard_error') }}</div>
        @endif

        <div id="rosterAlert" class="alert alert-info py-2" style="display:none;"></div>

        <form id="rosterForm">
            @csrf
            <input type="hidden" name="idj" value="{{ $schedule->idj }}">
            <input type="hidden" name="tanggal" value="{{ $date }}">

            <div class="table-responsive">
                <table class="roster-table table table-striped table-bordered table-hover" id="dataTables-example">
                    <thead>
                        <tr>
                            <th class="text-center" width="80">No</th>
                            <th>NIS</th>
                            <th>Nama</th>
                            <th class="text-center">Absensi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $index => $student)
                            @php
                                $currentStatus = strtoupper((string) ($attendanceMap[$student->nis] ?? ''));
                            @endphp
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>{{ $student->nis }}</td>
                                <td>{{ $student->nama }}</td>
                                <td>
                                    <div class="roster-actions">
                                        <button type="button" class="btn btn-success btn-roster {{ $currentStatus === 'H' ? 'active' : '' }}" data-nis="{{ $student->nis }}" data-status="H">Hadir</button>
                                        <button type="button" class="btn btn-warning btn-roster {{ $currentStatus === 'I' ? 'active' : '' }}" data-nis="{{ $student->nis }}" data-status="I">Izin</button>
                                        <button type="button" class="btn btn-danger btn-roster {{ $currentStatus === 'A' ? 'active' : '' }}" data-nis="{{ $student->nis }}" data-status="A">Alpha</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">Tidak ada siswa di kelas ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>

        <div class="roster-help">
            Keterangan: H = Hadir, I = Izin, A = Alpha.
        </div>
    </div>

    <script>
        (function () {
            var form = document.getElementById('rosterForm');
            var alertBox = document.getElementById('rosterAlert');

            function showAlert(message, type) {
                alertBox.className = 'alert ' + (type === 'error' ? 'alert-danger' : 'alert-success') + ' py-2';
                alertBox.textContent = message;
                alertBox.style.display = '';
            }

            function setButtonState(rowButton) {
                var row = rowButton.closest('td');
                row.querySelectorAll('.btn-roster').forEach(function (button) {
                    button.classList.remove('active');
                });
                rowButton.classList.add('active');
            }

            document.querySelectorAll('.btn-roster').forEach(function (button) {
                button.addEventListener('click', function () {
                    var nis = button.getAttribute('data-nis');
                    var status = button.getAttribute('data-status');
                    var payload = new FormData(form);
                    payload.append('nis', nis);
                    payload.append('status', status);
                    payload.append('ajax', '1');

                    button.disabled = true;

                    fetch('{{ route('attendances.roster.update') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: payload
                    }).then(function (response) {
                        if (!response.ok) {
                            throw new Error('Gagal menyimpan status absensi.');
                        }
                        setButtonState(button);
                        showAlert('Status absensi berhasil disimpan untuk ' + nis + '.', 'success');
                    }).catch(function (error) {
                        showAlert(error.message, 'error');
                    }).finally(function () {
                        button.disabled = false;
                    });
                });
            });
        })();
    </script>
@endsection