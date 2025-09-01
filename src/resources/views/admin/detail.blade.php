@extends("layouts.admin")

@section("css")
    <link rel="stylesheet" href="{{ asset('css/admin/detail.css') }}">
@endsection

@section("content")
    <div class="attendance-detail-container">
        <h1 class="page-title">勤怠詳細</h1>
        <form action="{{ route('admin.attendance.modify', ['id' => $attendance->id]) }}" method="post">
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
                                @if ($isEditable)
                                    <div class="input-group">
                                        <input name="clock_in_time_after" type="text" class="text-field time-field" value="{{ old("clock_in_time_after", $formattedClockInTime) }}">
                                        @error('clock_in_time_after')
                                            <span class="error-message">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <span class="separator">~</span>
                                    <div class="input-group">
                                        <input name="clock_out_time_after" type="text" class="text-field time-field" value="{{ old("clock_out_time_after", $formattedClockOutTime) }}">
                                        @error('clock_out_time_after')
                                            <span class="error-message">{{ $message }}</span>
                                        @enderror
                                    </div>
                                @else
                                    <span class="text-field date-name-field">{{ $formattedClockInTime }}</span>
                                    <span class="separator">~</span>
                                    <span class="text-field date-name-field">{{ $formattedClockOutTime }}</span>
                                @endif
                            </td>
                        </tr>
                        @foreach ($formattedRests as $index => $rest)
                            <tr>
                                <th class="detail-label">休憩{{ $index + 1 }}</th>
                                <td class="detail-value time-range">
                                    @if ($isEditable)
                                        <div class="input-group">
                                            <input name="rests_after[{{ $index }}][start]" type="text" class="text-field time-field"
                                                value="{{ old("rests_after." . $index . ".start", $rest["start"]) }}">
                                            @error("rests_after." . $index . ".start")
                                                <span class="error-message">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <span class="separator">~</span>
                                        <div class="input-group">
                                            <input name="rests_after[{{ $index }}][end]" type="text" class="text-field time-field"
                                                value="{{ old("rests_after." . $index . ".end", $rest["end"]) }}">
                                            @error("rests_after." . $index . ".end")
                                                <span class="error-message">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    @else
                                        <span class="text-field date-name-field">{{ $rest["start"] }}</span>
                                        <span class="separator">~</span>
                                        <span class="text-field date-name-field">{{ $rest["end"] }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        <tr>
                            <th class="detail-label">備考</th>
                            <td class="detail-value memo-cell">
                                @if ($isEditable)
                                    <div class="input-group">
                                        <textarea name="notes_after" class="memo-textarea">{{ old("notes_after", $formattedNotes) }}</textarea>
                                        @error('notes_after')
                                            <span class="error-message">{{ $message }}</span>
                                        @enderror
                                    </div>
                                @else
                                    <p>{{ $formattedNotes }}</p>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="action-buttons">
                @if ($isEditable)
                    <button type="submit" class="action-button submit-button">修正</button>
                    <button type="button" class="action-button cancel-button" onclick="history.back()">戻る</button>
                @else
                    <p class="pending-text">*承認待ちのため修正はできません。</p>
                    <button type="button" class="action-button cancel-button" onclick="history.back()">戻る</button>
                @endif
            </div>
        </form>
    </div>
@endsection