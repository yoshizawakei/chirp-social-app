@extends("layouts.user")

@section("css")
    <link rel="stylesheet" href="{{ asset('css/application/list.css') }}">
@endsection

@section("content")
    <div class="application-list-container">
        <div class="application-list__header">
            <h1>申請一覧</h1>
        </div>

        <div class="tab-navigation">
            <a href="{{ route('application.list', ['status' => 'pending']) }}"
                class="tab-button {{ request('status', 'pending') == 'pending' ? 'active' : '' }}">承認待ち</a>
            <a href="{{ route('application.list', ['status' => 'approved']) }}"
                class="tab-button {{ request('status') == 'approved' ? 'active' : '' }}">承認済み</a>
        </div>

        <table class="application-table">
            <thead>
                <tr>
                    <th>状態</th>
                    <th>名前</th>
                    <th>対象日時</th>
                    <th>申請理由</th>
                    <th>申請日時</th>
                    <th>詳細</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($applications as $application)
                    <tr>
                        <td>
                            @if ($application->is_approved === 0 || $application->is_approved === null)
                                承認待ち
                            @elseif ($application->is_approved === 1)
                                承認済み
                            @else
                                却下
                            @endif
                        </td>
                        <td>{{ $application->user->name }}</td>
                        <td>{{ \Carbon\Carbon::parse($application->attendance->date)->isoFormat('YYYY/MM/DD') }}</td>
                        <td>{{ $application->notes_after ?? $application->notes_before }}</td>
                        <td>{{ \Carbon\Carbon::parse($application->created_at)->isoFormat('YYYY/MM/DD') }}</td>
                        <td><a href="{{ route('attendance.detail', ['attendance' => $application->attendance->id]) }}" class="detail-link">詳細</a></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">該当する申請はありません。</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection

@section('scripts')
    {{-- 必要に応じてJavaScriptをここに記述 --}}
@endsection