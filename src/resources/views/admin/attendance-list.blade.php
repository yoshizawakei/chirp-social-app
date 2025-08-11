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
                <div class="calendar-container">
                    <img src="{{ asset('img/calender-img.png') }}" alt="カレンダー" id="calendar-icon" style="cursor: pointer;">
                    <span class="current-day">{{ $displayDate->isoFormat("YYYY/MM/DD") }}</span>
                </div>
                <a href="{{ route("admin.attendance.list", ["dateString" => $nextDay]) }}"
                    class="nav-button">翌日 &gt;</a>
            </div>
            <div id="calendar-modal" class="calendar-modal">
                <input type="date" id="date-picker">
                <button id="close-modal">閉じる</button>
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
    const calendarIcon = document.getElementById('calendar-icon');
    const calendarModal = document.getElementById('calendar-modal');
    const datePicker = document.getElementById('date-picker');
    const closeModalButton = document.getElementById('close-modal');

    calendarIcon.addEventListener('click', function() {
        calendarModal.style.display = 'block';
    });

    closeModalButton.addEventListener('click', function() {
        calendarModal.style.display = 'none';
    });

    datePicker.addEventListener('change', function() {
        const selectedDate = this.value;
        if (selectedDate) {
            window.location.href = `/admin/attendance/list/${selectedDate}`;
        }
    });
});
</script>
@endsection