@extends('layouts.user')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/attendance/list.css') }}">
@endsection

@section('content')
    <div class="attendance-list-container">
        <div class="attendance-list__header">
            <h1>勤怠一覧</h1>
            <div class="month-navigation">
                <a href="#" class="nav-button">&lt; 前月</a>
                <span class="current-month">{{ \Carbon\Carbon::now()->isoFormat('YYYY/MM') }}</span>
                <a href="#" class="nav-button">翌月 &gt;</a>
            </div>
        </div>

        <table class="attendance-table">
            <thead>
                <tr>
                    <th>日付</th>
                    <th>出勤</th>
                    <th>退勤</th>
                    <th>休憩</th>
                    <th>合計</th>
                    <th>詳細</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($attendances as $attendance)
                    <tr>
                        <td>{{ $attendance->formatted_date }}</td>
                        <td>{{ $attendance->clock_in_time ? \Carbon\Carbon::parse($attendance->clock_in_time)->format("H:i") : '-' }}</td>
                        <td>{{ $attendance->clock_out_time ? \Carbon\Carbon::parse($attendance->clock_out_time)->format("H:i") : '-' }}</td>
                        <td>{{ $attendance->total_break_time }}</td>
                        <td>{{ $attendance->total_work_time }}</td>
                        <td><a href="#" class="detail-link">詳細</a></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">今月の勤怠記録はありません。</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection

@section('scripts')
    {{-- 必要に応じてJavaScriptをここに記述 --}}
@endsection