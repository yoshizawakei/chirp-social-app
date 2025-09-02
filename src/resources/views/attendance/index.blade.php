@extends("layouts.user")

@section("css")
<link rel="stylesheet" href="{{ asset('css/attendance/index.css') }}">
@endsection

@section("content")
    <div class="attendance-card">
        @if (session("success"))
            <div class="alert alert-success">
                {{ session("success") }}
            </div>
        @endif
        @if (session("error"))
            <div class="alert alert-danger">
                {{ session("error") }}
            </div>
        @endif

        <div class="attendance-card__status" id="attendanceStatus">{{ $status }}</div>

        <div class="attendance-card__date" id="currentDate"></div>
        <div class="attendance-card__time" id="currentTime"></div>

        <div class="attendance-card__buttons">
            @if ($status === "勤務外")
                <form action="{{ route("attendance.clockIn") }}" method="post" id="clockInForm">
                @csrf
                    <button type="submit" class="attendance-card__button attendance-card__button--clock-in">出勤</button>
                </form>
            @elseif ($status === "出勤中")
                <form action="{{ route("attendance.clockOut")}}" method="post" id="clockOutForm">
                    @csrf
                    <button type="submit" class="attendance-card__button attendance-card__button--clock-out">退勤</button>
                </form>
                <form action="{{ route("attendance.breakIn") }}" method="post" id="breakInForm">
                    @csrf
                    <button type="submit" class="attendance-card__button attendance-card__button-break-in">休憩入</button>
                </form>
            @elseif ($status === "休憩中")
                <form action="{{ route("attendance.breakOut") }}" method="post" id="breakOutForm">
                    @csrf
                    <button type="submit" class="attendance-card__button attendance-card__button-break-out">休憩戻</button>
                </form>
            @elseif ($status === "退勤済")
                <p>お疲れ様でした。</p>
            @endif
        </div>
    </div>
@endsection

@section("scripts")
    <script>
        function updateDateTime() {
            const now = new Date();

            const year = now.getFullYear();
            const month = now.getMonth() + 1;
            const day = now.getDate();
            const dayOfWeek = ["日", "月", "火", "水", "木", "金", "土"][now.getDay()];
            document.getElementById("currentDate").textContent = `${year}年${month}月${day}日(${dayOfWeek})`;

            const hours = String(now.getHours()).padStart(2, "0");
            const minutes = String(now.getMinutes()).padStart(2, "0");
            document.getElementById("currentTime").textContent = `${hours}:${minutes}`;
        }

        updateDateTime();
        setInterval(updateDateTime, 1000);

        document.addEventListener("DOMContentLoaded", function() {
            const successAlert = document.querySelector(".alert-success");
            const errorAlert = document.querySelector(".alert-danger");

            if (successAlert) {
                setTimeout(() => {
                    successAlert.style.display = "none";
                }, 3000);
            }
            if (errorAlert) {
                setTimeout(() => {
                    errorAlert.style.display = "none";
                }, 3000);
            }
        });
    </script>
@endsection