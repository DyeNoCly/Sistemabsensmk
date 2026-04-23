<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; font-size: 11pt; }
        h1, h2, h3 { margin: 0 0 8px 0; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 6px; }
        th { background: #d9edf7; }
    </style>
</head>
<body>
    <h2>Rekap Absensi Guru</h2>
    <div>{{ $teacher->nama }} | NIP {{ $teacher->nip }}</div>
    <br>
    <table>
        <thead>
            <tr>
                <th>Hari</th>
                <th>Kelas</th>
                <th>Mata Pelajaran</th>
                <th>Jam</th>
                <th>Aktif</th>
            </tr>
        </thead>
        <tbody>
            @forelse($schedules as $schedule)
                <tr>
                    <td>{{ $schedule->day?->hari ?? '-' }}</td>
                    <td>{{ $schedule->kelas?->nama ?? '-' }}</td>
                    <td>{{ $schedule->subject?->nama_mp ?? '-' }}</td>
                    <td>{{ $schedule->jam_mulai }} - {{ $schedule->jam_selesai }}</td>
                    <td>{{ (int) $schedule->aktif === 1 ? 'Ya' : 'Tidak' }}</td>
                </tr>
            @empty
                <tr><td colspan="5">Belum ada jadwal mengajar.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
