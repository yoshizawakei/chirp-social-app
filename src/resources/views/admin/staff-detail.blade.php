@extends("layouts.admin")

@section("css")
    <link rel="stylesheet" href="{{ asset('css/admin/list.css') }}">
@endsection

@section("content")
    <div class="attendance-list-container">
        <div class="attendance-list__header">
            <h1>{{ $targetUser->name }}の勤怠</h1>
            <div class="day-navigation">
                <a href="{{ route("admin.staff.detail",["id" => $id, "year" => $prevMonth->year, "month" => $prevMonth->month]) }}" class="nav-button">&lt; 前月</a>

                <span class="current-day">
                    <img src="{{ asset('img/calender-img.png') }}" alt="カレンダー" id="calendar-icon" style="cursor: pointer;">
                    <i class="fa-solid fa-calendar-days"></i> {{ $displayMonth->isoFormat("YYYY/MM") }}
                </span>
                <div id="calendar-modal" class="calendar-modal">
                    <input type="month" id="date-picker" value="{{ $displayMonth->format('Y-m') }}">
                    <input type="hidden" id="user-id" value="{{ $id }}">
                    <button id="close-modal">閉じる</button>
                </div>

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
            <form action="{{ route("admin.staff.exportCsv", ["id" => $id, "year" => $displayMonth->year, "month" => $displayMonth->month]) }}" method="POST">
            @csrf
                <button type="submit" class="csv-button">CSV出力</button>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const calendarIcon = document.getElementById('calendar-icon');
            const calendarModal = document.getElementById('calendar-modal');
            const datePicker = document.getElementById('date-picker');
            const closeModalButton = document.getElementById('close-modal');
            const userId = document.getElementById('user-id').value;

            calendarIcon.addEventListener('click', function () {
                calendarModal.style.display = 'block';
            });

            closeModalButton.addEventListener('click', function () {
                calendarModal.style.display = 'none';
            });

            datePicker.addEventListener('change', function () {
                const selectedDate = this.value;
                if (selectedDate) {
                    const [year, month] = selectedDate.split('-');
                    const $id = userId;
                    window.location.href = `/admin/attendance/staff/${$id}/${year}/${month}`;
                }
            });
        });
    </script>
@endsection