<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'CookOverflow' }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <header class="site-header">
        <div class="container header-row">
            <a href="{{ route('home') }}" class="logo">CookOverflow</a>

            <form action="{{ route('home') }}" method="GET" class="search-form">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Поиск по вопросам, ответам и тегам"
                    class="search-input"
                >
                <button type="submit" class="button button-light">Найти</button>
            </form>

            <nav class="nav-links">
                <a href="{{ route('home') }}">Главная</a>

                @auth
                    <a href="{{ route('questions.create') }}">Задать вопрос</a>
                    <a href="{{ route('profile.show', auth()->user()) }}">Профиль</a>

                    <form action="{{ route('logout') }}" method="POST" class="inline-form">
                        @csrf
                        <button type="submit" class="link-button">Выйти</button>
                    </form>
                @else
                    <a href="{{ route('login') }}">Вход</a>
                    <a href="{{ route('register') }}">Регистрация</a>
                @endauth
            </nav>
        </div>
    </header>

    <main class="container page-content">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-error">
                <strong>Пожалуйста, исправьте ошибки:</strong>
                <ul class="error-list">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{ $slot ?? '' }}
        @yield('content')
    </main>
</body>
</html>
