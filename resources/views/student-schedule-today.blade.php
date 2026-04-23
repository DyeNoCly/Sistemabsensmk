@extends('layouts.app')

@section('content')
    <style>
        .schedule-shell {
            max-width: 980px;
            margin: 20px auto;
            background: linear-gradient(145deg, #ffffff 0%, #f5f8ff 100%);
            border: 1px solid #d8e3ff;
            border-radius: 18px;
            padding: 18px;
            box-shadow: 0 10px 24px rgba(35, 61, 116, 0.11);
        }

        .schedule-title {
            margin: 0;
            font-size: 24px;
            color: #16345d;
            font-weight: 700;
        }

        .schedule-subtitle {
            margin: 6px 0 0;
            color: #557090;
            font-size: 14px;
        }

        .schedule-grid {
            margin-top: 14px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 10px;
        }

        .schedule-item {
            background: #fff;
            border: 1px solid #dce8ff;
            border-radius: 10px;
            padding: 12px;
        }

        .schedule-subject {
            color: #1b3352;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .schedule-meta {
            color: #6a7f9b;
            font-size: 12px;
            margin-bottom: 3px;
        }

        .schedule-actions {
            margin-top: 14px;
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
    </style>

    <div class="schedule-shell">
        <h3 class="schedule-title">Jadwal Hari Ini</h3>

        @if($student)
            <p class="schedule-subtitle">
                {{ $student->nama }} - Kelas {{ $student->kelas->nama ?? '-' }}
            </p>
        @else
            <p class="schedule-subtitle">Data siswa tidak ditemukan.</p>
        @endif

        @if($todaySchedules->count() > 0)
            <div class="schedule-grid">
                @foreach($todaySchedules as $sched)
                    <div class="schedule-item">
                        <div class="schedule-subject">{{ $sched->subject->nama_mp ?? '-' }}</div>
                        <div class="schedule-meta">
                            <i class="fa fa-clock-o" style="margin-right: 4px;"></i>
                            {{ substr((string) $sched->jam_mulai, 0, 5) }} - {{ substr((string) $sched->jam_selesai, 0, 5) }}
                        </div>
                        <div class="schedule-meta">
                            <i class="fa fa-user" style="margin-right: 4px;"></i>
                            {{ $sched->teacher->nama ?? '-' }}
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="alert alert-info" style="margin-top: 14px;">
                Tidak ada jadwal pelajaran untuk hari ini.
            </div>
        @endif

        <div class="schedule-actions">
            <a href="{{ route('student-attendance') }}" class="btn btn-primary btn-sm">
                <i class="fa fa-check-square-o"></i> Buka Absensi
            </a>
            <a href="{{ route('dashboard') }}" class="btn btn-default btn-sm">
                <i class="fa fa-dashboard"></i> Kembali ke Dashboard
            </a>
        </div>
    </div>
@endsection
