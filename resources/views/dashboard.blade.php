@extends('layouts.app')

@section('content')
    @php
        $cards = $dashboard['cards'] ?? [];
        $status = $dashboard['status'] ?? ['Hadir' => 0, 'Izin' => 0, 'Alpha' => 0];
        $trend = $dashboard['trend'] ?? ['labels' => [], 'hadir' => [], 'izin' => [], 'alpha' => []];
        $performance = $dashboard['performance'] ?? collect();
        $tableRows = $dashboard['tableRows'] ?? collect();
        $mode = $dashboard['mode'] ?? 'empty';
        @include('dashboard.index')
        $todaySchedules = $dashboard['todaySchedules'] ?? collect();
