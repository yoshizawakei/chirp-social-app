@extends("layouts.user")

@section("css")
    <link rel="stylesheet" href="{{ asset('css/attendance/list.css') }}">
@endsection

@section("content")
    <div class="attendance-list-container">
        <div class="attendance-list__header">
            <h1>勤怠一覧</h1>
            <div class="month-navigation">
                <a href="{{ route("attendance.list", ["year" => $prevMonthYear->year, "month" => $prevMonthYear->month]) }}" class="nav-button">&lt; 前月</a>
                <span class="current-month">{{ $currentMonthYear }}</span>
                <a href="{{ route("attendance.list", ["year" => $nextMonthYear->year, "month" => $nextMonthYear->month]) }}" class="nav-button">翌月 &gt;</a>
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
                @forelse ($formattedAttendances as $attendance)
                    <tr>
                        <td>{{ $attendance->formatted_date }}</td>
                        <td>{{ $attendance->formatted_clock_in_time }}</td>
                        <td>{{ $attendance->formatted_clock_out_time }}</td>
                        <td>{{ $attendance->total_break_time }}</td>
                        <td>{{ $attendance->total_work_time }}</td>
                        <td><a href="{{ route("attendance.detail", ["attendance" => "$attendance->id"]) }}" class="detail-link">詳細</a></td>
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
    {{-- JavaScript --}}
@endsection