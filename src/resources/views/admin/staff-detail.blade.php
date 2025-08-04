@extends("layouts.admin")

@section("css")
    <link rel="stylesheet" href="{{ asset('css/admin/list.css') }}">
@endsection

@section("content")
    <div class="attendance-list-container">
        <div class="attendance-list__header">
            <h1>{{ $targetUser->name }}の勤怠</h1>
            <div class="day-navigation">
                <a href="{{ route("admin.staff.detail", ["id" => $id, "year" => $prevMonth->year, "month" => $prevMonth->month
                ]) }}" class="nav-button">&lt; 前月</a>

                <span class="current-day">
                    <i class="fa-solid fa-calendar-days"></i> {{ $displayMonth->isoFormat("YYYY/MM") }}
                </span>

                <a href="{{ route("admin.staff.detail", ["id" => $id, "year" => $nextMonth->year, "month" => $nextMonth->month]) }}" class="nav-button">翌月 &gt;</a>
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
                        <td><a href="{{ route("admin.attendance.detail", ["attendance" => $attendance->id]) }}" class="detail-link">詳細</a></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">{{ $displayMonth->isoFormat("YYYY年MM月") }}の勤怠記録はありません。</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="csv-output">
            <form action="#" method="POST">
            @csrf
                <button type="submit" class="csv-button">CSV出力</button>
            </form>
        </div>
    </div>
@endsection