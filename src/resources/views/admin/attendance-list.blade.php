@extends("layouts.admin")

@section("css")
    <link rel="stylesheet" href="{{ asset('css/admin/list.css') }}">
@endsection

@section("content")
    <div class="attendance-list-container">
        <div class="attendance-list__header">
            <h1>{{ $displayDate->isoFormat("YYYY年MM月DD日") }}の勤怠</h1>
            <div class="day-navigation">
                <a href="{{ route("admin.attendance.list", ["dateString" => $prevDay]) }}" class="nav-button">&lt; 前日</a>
                <span class="current-day">{{ $displayDate->isoFormat("YYYY/MM/DD") }}</span>
                <a href="{{ route("admin.attendance.list", ["dateString" => $nextDay]) }}"
                    class="nav-button">翌日 &gt;</a>
            </div>
        </div>

        <table class="attendance-table">
            <thead>
                <tr>
                    <th>名前</th>
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
                        <td>{{ $attendance->user_name }}</td>
                        <td>{{ $attendance->formatted_clock_in_time }}</td>
                        <td>{{ $attendance->formatted_clock_out_time }}</td>
                        <td>{{ $attendance->total_break_time }}</td>
                        <td>{{ $attendance->total_work_time }}</td>
                        <td><a href="{{ route("admin.attendance.detail", ["attendance" => "$attendance->id"]) }}" class="detail-link">詳細</a></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">{{ $displayDate->isoFormat("YYYY年MM月DD日") }}の勤怠記録はありません。</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection

@section('scripts')
    {{-- JavaScript --}}
@endsection