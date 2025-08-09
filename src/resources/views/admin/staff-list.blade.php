@extends("layouts.admin")

@section("css")
    <link rel="stylesheet" href="{{ asset('css/admin/staff-list.css') }}">
@endsection

@section("content")
    <div class="staff-list-container">
        <h1 class="staff-list__header">スタッフ一覧</h1>

        <table class="staff-table">
            <thead>
                <tr>
                    <th>名前</th>
                    <th>メールアドレス</th>
                    <th>月次勤怠</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($staffs as $staff)
                    <tr>
                        <td>{{ $staff->name }}</td>
                        <td>{{ $staff->email }}</td>
                        <td>
                            <a href="{{ route("admin.staff.detail", ["id" => $staff->id]) }}" class="detail-button">詳細</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">スタッフ情報はありません。</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection