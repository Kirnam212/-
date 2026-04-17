@extends('layouts.app')

@section('content')
    <section class="panel auth-panel">
        <h1>Вход</h1>

        <form action="{{ route('login') }}" method="POST" class="form-grid">
            @csrf

            <div>
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="Введите email">
            </div>

            <div>
                <label for="password">Пароль</label>
                <input id="password" type="password" name="password" placeholder="Введите пароль">
            </div>

            <label class="checkbox-line">
                <input type="checkbox" name="remember" value="1">
                <span>Запомнить меня</span>
            </label>

            <button type="submit" class="button">Войти</button>
        </form>
    </section>
@endsection
