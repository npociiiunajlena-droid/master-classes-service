<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Сервис записи на мастер-классы')</title>
    <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/responsive.css') }}">
    <style>
        .header .row {
            gap: 20px;
            padding-bottom: 14px;
            padding-top: 14px;
        }

        .header .logo {
            flex-shrink: 0;
        }

        .header .logo img {
            display: block;
            height: auto;
            max-width: 260px;
            width: 100%;
        }

        .header .title {
            flex: 1 1 auto;
            line-height: 1.15;
            min-width: 0;
        }

        .header .auth {
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            gap: 8px;
            justify-content: center;
            min-width: 250px;
        }

        .header .auth-user {
            color: #00044c;
            font-size: 15px;
            font-weight: bold;
            line-height: 1.15;
            margin: 0;
            text-align: right;
        }

        .auth .auth-links {
            align-items: center;
            display: flex;
            flex-wrap: wrap;
            gap: 10px 16px;
            justify-content: flex-end;
        }

        .auth .auth-links a {
            color: #00044c;
            font-size: 13px;
            font-weight: bold;
            line-height: 1;
            padding: 0;
            text-decoration: none;
        }

        .auth .auth-links form {
            margin: 0;
            padding: 0;
            width: auto;
        }

        .link-btn {
            background: transparent;
            border: 0;
            color: #00044c;
            cursor: pointer;
            font-size: 13px;
            font-weight: bold;
            padding: 0;
        }

        .flash-wrapper {
            background: #fff;
            margin: 0 auto;
            max-width: 1100px;
            min-width: 640px;
            padding: 0 10px;
        }

        .flash-item {
            border: 1px solid #20416c;
            color: #20416c;
            margin-bottom: 10px;
            padding: 10px 12px;
        }

        .flash-item--error {
            border-color: #9d1d1d;
            color: #9d1d1d;
        }

        .header .auth::before {
            display: none;
        }

        @media (max-width: 1050px) {
            .header .row {
                align-items: flex-start;
                flex-wrap: wrap;
            }

            .header .title {
                flex-basis: 100%;
                text-align: center;
            }

            .header .auth {
                align-items: center;
                margin-left: auto;
                min-width: 0;
            }

            .header .auth-user,
            .auth .auth-links {
                justify-content: center;
                text-align: center;
            }
        }

        .enrollment-actions {
            margin-top: 12px;
        }

        .enrollment-actions form {
            margin: 0;
            padding: 0;
            width: auto;
        }
    </style>
</head>
<body class="{{ $bodyClass ?? '' }}">
    <div class="header">
        <div class="row grid middle between">
            <div class="logo">
                <a href="{{ route('home') }}">
                    <img src="{{ asset('assets/img/logo.png') }}" alt="Логотип">
                </a>
            </div>
            <div class="title">
                Клуб любителей творчества «ОчУмелые ручки»
            </div>
            <div class="auth">
                @auth
                    <div class="auth-user">{{ auth()->user()->full_name }}</div>
                @endauth
                <div class="auth-links">
                    @auth
                        <a href="{{ route('home') }}">Главная</a>
                        @if(auth()->user()->isVisitor())
                            <a href="{{ route('enrollments.index') }}">Мои записи</a>
                        @endif
                        @if(auth()->user()->isMaster())
                            <a href="{{ route('cabinet.index') }}">Кабинет</a>
                        @endif
                        <form action="{{ route('auth.logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="link-btn">Выход</button>
                        </form>
                    @else
                        <a href="{{ route('auth.login') }}">Вход</a>
                        <a href="{{ route('auth.register') }}">Регистрация</a>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <div class="row row--nogutter">
        <div class="menu-burger">
            <div class="burger">
                <div></div>
                <div></div>
                <div></div>
            </div>
        </div>
    </div>

    @yield('before_main')

    @include('partials.flash')

    @yield('content')

    <div class="row row--nogutter">
        <div class="line"></div>
    </div>

    <div class="footer">
        <div class="row">
            <div class="row--small grid between">
                <div class="address">Наш адрес: ВДНХ, 120в</div>
                <div class="tel">Тел: 89123456765</div>
                <div class="copy">(с) Copyright, 2026</div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var burger = document.querySelector('.burger');

            if (!burger) {
                return;
            }

            burger.addEventListener('click', function () {
                document.querySelectorAll('.main .menu').forEach(function (menu) {
                    if (menu.style.display === 'block') {
                        menu.style.display = 'none';
                    } else {
                        menu.style.display = 'block';
                    }
                });
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
