@extends('layouts.admin')

@section("css")
    <link rel="stylesheet" href="{{ asset('css/admin/login.css') }}">
@endsection

@section("content")
    <div class="login-container">
        <h2 class="login-title">管理者ログイン</h2>
        <form method="post" action="{{ route("admin.authenticate") }}" class="login-form" novalidate>
            @csrf
            <div class="form-group">
                <label for="email" class="form-label">メールアドレス</label>
                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}"
                    autocomplete="email" autofocus>
                @error('email')
                    <span class="error-message" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label">パスワード</label>
                <input id="password" type="password" class="form-control" name="password" autocomplete="current-password">
                @error('password')
                    <span class="error-message" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <button type="submit" class="btn-primary">
                管理者ログインする
            </button>
        </form>
    </div>
@endsection