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

                <div class="calendar-container">
                    <img src="{{ asset('img/calender-img.png') }}" alt="カレンダー" id="calendar-icon" style="cursor: pointer;">
                    <span class="current-month">{{ $currentMonthYear }}</span>
                </div>
                <div id="calendar-modal" class="calendar-modal">
                    <input type="month" id="date-picker" value="{{ $currentMonthYear }}">
                    <button id="close-modal">閉じる</button>
                </div>

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
                        <td><a href="{{ route("attendance.detail", ["attendance" => $attendance->id]) }}" class="detail-link">詳細</a></td>
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
            const [year, month] = selectedDate.split('-');
            calendarModal.style.display = 'none';
            if (selectedDate) {
                window.location.href = `/attendance/list/${year}/${month}`;
            }
        });
    });
    </script>
@endsection