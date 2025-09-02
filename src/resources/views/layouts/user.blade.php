<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset("css/layouts/sanitize.css") }}">
    <link rel="stylesheet" href="{{ asset('css/layouts/common.css') }}">
    @yield('css')
    <title>coachtech 勤怠管理アプリ</title>
</head>

<body>
    <header class="header">
        <div class="header-inner">
            <div class="header-logo">
                <a href="{{ route("attendance.index") }}"><img src="{{ asset("img/logo.svg") }}" alt="coachtech"></a>
            </div>
            @if (Auth::check())
                <button class="menu-toggle" id="menu-toggle">
                    <span class="bar"></span>
                    <span class="bar"></span>
                    <span class="bar"></span>
                </button>
                <div class="header-menu-container" id="header-menu-container">
                    <nav class="header-nav">
                        <ul class="header-nav_list">
                            <li><a href="{{ route("attendance.index") }}">勤怠</a></li>
                            <li><a href="{{ route("attendance.list") }}">勤怠一覧</a></li>
                            <li><a href="{{ route("application.list") }}">申請</a></li>
                            <li>
                                <form action="/logout" method="POST">
                                    @csrf
                                    <button class="header-nav_logout-button" type="submit">ログアウト</button>
                                </form>
                            </li>
                        </ul>
                    </nav>
                </div>
            @else

            @endif
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const menuToggle = document.getElementById("menu-toggle");
            const headerMenuContainer = document.getElementById("header-menu-container");

            if (menuToggle && headerMenuContainer) {
                menuToggle.addEventListener("click", () => {
                    headerMenuContainer.classList.toggle("is-open");
                    menuToggle.classList.toggle("is-active");
                });
            }
        });
    </script>

    @yield('scripts')

</body>

</html>