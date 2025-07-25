@extends("layouts.user")

@section("css")
    <link rel="stylesheet" href="{{ asset('css/attendance/detail.css') }}">
@endsection

@section("content")
    <div class="attendance-detail-container">
        <h1 class="page-title">勤怠詳細</h1>
        <form action="{{ route('attendance.requestModify', ['id' => $attendance->id]) }}" method="post">
            @csrf
            <div class="detail-card">
                <table class="detail-table">
                    <tbody>
                        <tr>
                            <th class="detail-label">名前</th>
                            <td class="detail-value">
                                <span class="text-field date-name-field">{{ $attendance->user->name }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th class="detail-label">日付</th>
                            <td class="detail-value date-value-container">
                                <span class="text-field date-name-field">{{ $formattedDateYear }}</span>
                                <span class="separator"></span>
                                <span class="text-field date-name-field">{{ $formattedDateMonthDay }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th class="detail-label">出勤・退勤</th>
                            <td class="detail-value time-range">
                                <input name="clock_in_time_after" type="text" class="text-field time-field"
                                    value="{{ $formattedClockInTime }}">
                                <span class="separator">~</span>
                                <input name="clock_out_time_after" type="text" class="text-field time-field"
                                    value="{{ $formattedClockOutTime }}">
                            </td>
                        </tr>
                        <tr>
                            <th class="detail-label">休憩</th>
                            <td class="detail-value time-range">
                                <input name="rests_after[0][start]" type="text" class="text-field time-field"
                                    value="{{ $formattedRest1Start }}">
                                <span class="separator">~</span>
                                <input name="rests_after[0][end]" type="text" class="text-field time-field"
                                    value="{{ $formattedRest1End }}">
                            </td>
                        </tr>
                        <tr>
                            <th class="detail-label">休憩2</th>
                            <td class="detail-value time-range">
                                <input name="rests_after[1][start]" type="text" class="text-field time-field"
                                    value="{{ $formattedRest2Start }}">
                                <span class="separator">~</span>
                                <input name="rests_after[1][end]" type="text" class="text-field time-field"
                                    value="{{ $formattedRest2End }}">
                            </td>
                        </tr>
                        <tr>
                            <th class="detail-label">備考</th>
                            <td class="detail-value memo-cell">
                                <textarea name="notes_after" class="memo-textarea">{{ $formattedNotes }}</textarea>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="action-buttons">
                <button type="submit" class="action-button submit-button">申請</button>
                <button type="button" class="action-button cancel-button" onclick="history.back()">戻る</button>
            </div>
        </form>
    </div>
@endsection