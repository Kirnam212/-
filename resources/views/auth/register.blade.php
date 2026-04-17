@extends('layouts.app')

@section('content')
    <section class="panel auth-panel">
        <h1>Регистрация</h1>

        <form action="{{ route('register') }}" method="POST" class="form-grid">
            @csrf

            <div>
                <label for="name">Имя</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="Как вас зовут?">
            </div>

            <div>
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="Введите email">
            </div>

            <div>
                <label for="bio">Кратко о себе</label>
                <textarea id="bio" name="bio" rows="4" placeholder="Например: люблю выпечку и домашние соусы">{{ old('bio') }}</textarea>
            </div>

            <div>
                <label for="password">Пароль</label>
                <input id="password" type="password" name="password" placeholder="Минимум 6 символов">
            </div>

            <div>
                <label for="password_confirmation">Подтверждение пароля</label>
                <input id="password_confirmation" type="password" name="password_confirmation" placeholder="Повторите пароль">
            </div>

            <button type="submit" class="button">Создать аккаунт</button>
        </form>
    </section>
@endsection
