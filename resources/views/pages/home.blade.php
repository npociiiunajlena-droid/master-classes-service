@extends('layouts.app')

@section('title', 'Главная')

@section('content')
    <div class="main">
        <div class="row">
            <div class="hover"></div>
            <div class="title">Сервис записи на мастер-классы</div>
            <div class="row--small grid between">
                <div class="content">
                    <img class="content-photo content-photo--small" src="{{ asset('assets/img/elifant.png') }}" alt="Творчество">
                    <p>
                        Компания «Очумелые ручки» проводит мастер-классы по направлениям: архитектурное моделирование, кулинария и резьба по дереву.
                        На сайте доступно расписание занятий, регистрация участников и личный кабинет ведущего.
                    </p>
                    <p>
                        После авторизации пользователь может записываться на занятия, а ведущий создавать собственные мастер-классы и управлять описанием и стоимостью.
                    </p>
                    @auth
                        @if(auth()->user()->isVisitor())
                            <p>
                                Ваши подтверждённые записи доступны на отдельной странице
                                <a href="{{ route('enrollments.index') }}">«Мои записи»</a>.
                            </p>
                        @endif
                    @endauth
                </div>

                @include('partials.menu', ['types' => $types, 'currentType' => null])
            </div>
        </div>
    </div>
@endsection
