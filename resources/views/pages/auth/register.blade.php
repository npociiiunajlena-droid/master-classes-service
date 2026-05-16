@extends('layouts.app')

@section('title', 'Регистрация')

@section('before_main')
    <div class="row row--nogutter top-line">
        <div class="line"></div>
    </div>
@endsection

@section('content')
    <div class="main">
        <div class="row">
            <div class="row--small">
                <form action="{{ route('auth.register.store') }}" method="POST">
                    @csrf
                    <h2>Форма регистрации</h2>

                    <div class="form-group">
                        <label for="full_name">ФИО</label>
                        <input id="full_name" type="text" name="full_name" value="{{ old('full_name') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Пароль</label>
                        <input id="password" type="password" name="password" minlength="8" required>
                    </div>

                    <div class="form-group">
                        <label for="phone">Номер телефона</label>
                        <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" placeholder="+79991234567" pattern="^\+?[0-9]{10,15}$" title="Формат: +79991234567" required>
                    </div>

                    <div class="form-group">
                        <button class="btn" type="submit">Отправить</button>
                    </div>

                    <div class="form-group">
                        Уже есть аккаунт? <a href="{{ route('auth.login') }}">Войти</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
