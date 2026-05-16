@extends('layouts.app')

@section('title', 'Вход')

@section('before_main')
    <div class="row row--nogutter top-line">
        <div class="line"></div>
    </div>
@endsection

@section('content')
    <div class="main">
        <div class="row">
            <div class="row--small">
                <form action="{{ route('auth.login.attempt') }}" method="POST">
                    @csrf
                    <h2>Форма авторизации</h2>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Пароль</label>
                        <input id="password" type="password" name="password" required>
                    </div>

                    <div class="form-group">
                        <button class="btn" type="submit">Войти</button>
                    </div>

                    <div class="form-group">
                        Нет аккаунта? <a href="{{ route('auth.register') }}">Перейти к регистрации</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
