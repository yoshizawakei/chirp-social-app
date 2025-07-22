@extends("layouts.user")

@section("css")
    <link rel="stylesheet" href="{{ asset('css/attendance/detail.css') }}">
@endsection

@section("content")
    <div class="attendance-detail-container">
        <h1 class="page-title">勤怠詳細</h1>
        <form action="#" method="post">
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
                                <span class="text-field date-name-field">{{ \Carbon\Carbon::parse($attendance->date)->isoFormat('YYYY年') }}</span>
                                <span class="separator"></span>
                                <span class="text-field date-name-field">{{ \Carbon\Carbon::parse($attendance->date)->isoFormat('M月D日') }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th class="detail-label">出勤・退勤</th>
                            <td class="detail-value time-range">
                                <input type="text" class="text-field time-field"
                                    value="{{ $attendance->clock_in_time ? \Carbon\Carbon::parse($attendance->clock_in_time)->format('H:i') : '' }}">
                                <span class="separator">~</span>
                                <input type="text" class="text-field time-field"
                                    value="{{ $attendance->clock_out_time ? \Carbon\Carbon::parse($attendance->clock_out_time)->format('H:i') : '' }}">
                            </td>
                        </tr>
                        <tr>
                            <th class="detail-label">休憩</th>
                            <td class="detail-value time-range">
                                @if ($attendance->rests->isNotEmpty() && $attendance->rests->first()->start_time)
                                    <input type="text" class="text-field time-field"
                                        value="{{ \Carbon\Carbon::parse($attendance->rests->first()->start_time)->format('H:i') }}">
                                    <span class="separator">~</span>
                                    <input type="text" class="text-field time-field"
                                        value="{{ $attendance->rests->first()->end_time ? \Carbon\Carbon::parse($attendance->rests->first()->end_time)->format('H:i') : '' }}">
                                @else
                                    <input type="text" class="text-field time-field" value="">
                                    <span class="separator">~</span>
                                    <input type="text" class="text-field time-field" value="">
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th class="detail-label">休憩2</th>
                            <td class="detail-value time-range">
                                @if ($attendance->rests->count() > 1 && $attendance->rests->get(1)->start_time)
                                    <input type="text" class="text-field time-field"
                                        value="{{ \Carbon\Carbon::parse($attendance->rests->get(1)->start_time)->format('H:i') }}">
                                    <span class="separator">~</span>
                                    <input type="text" class="text-field time-field"
                                        value="{{ $attendance->rests->get(1)->end_time ? \Carbon\Carbon::parse($attendance->rests->get(1)->end_time)->format('H:i') : '' }}">
                                @else
                                    <input type="text" class="text-field time-field" value="">
                                    <span class="separator">~</span>
                                    <input type="text" class="text-field time-field" value="">
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th class="detail-label">備考</th>
                            <td class="detail-value memo-cell">
                                <textarea class="memo-textarea">{{ $attendance->memo ?? '' }}</textarea>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="action-buttons">
                <button type="submit" class="action-button submit-button">申請</button>
                <button type="button" class="action-button cancel-button">戻る</button>
            </div>
        </form>
    </div>
@endsection