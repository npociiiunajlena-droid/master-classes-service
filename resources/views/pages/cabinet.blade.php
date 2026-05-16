@extends('layouts.app')

@section('title', 'Личный кабинет ведущего')

@section('content')
    <div class="main">
        <div class="row">
            <div class="hover"></div>
            <div class="title"></div>
            <div class="row--small grid between">
                <div class="content driver-page">
                    <div class="driver-page-photo">
                        <img src="{{ asset($master->photo_path ?? 'assets/img/driver-page.png') }}" alt="{{ $master->full_name }}">
                    </div>
                    <div class="driver-page-name">{{ $master->full_name }}</div>
                    <div class="driver-page-text">
                        <div class="driver-page-my">Мои мастер-классы</div>
                        @if($masterClasses->isEmpty())
                            <p>Список пока пуст. Добавьте первый мастер-класс.</p>
                        @else
                            <table class="driver-page-table">
                                <tbody>
                                @foreach($masterClasses as $masterClass)
                                    <tr>
                                        <td>{{ $masterClass->class_date->format('d.m.Y') }} {{ $masterClass->slotsLabel() }}</td>
                                        <td>
                                            <b>{{ $masterClass->creativityType->name }}</b><br>
                                            <b>{{ $masterClass->title }}</b><br>
                                            {{ $masterClass->description }}<br>
                                            Стоимость: {{ number_format((float) $masterClass->price, 2, ',', ' ') }} ₽<br>
                                            Свободно мест: {{ $masterClass->seatsLeft() }}
                                            <p>
                                                <a href="{{ route('master-classes.edit', $masterClass) }}">Редактировать описание и стоимость</a>
                                            </p>

                                            @if($masterClass->participants->isEmpty())
                                                <p>Участников пока нет.</p>
                                            @else
                                                @foreach($masterClass->participants as $index => $participant)
                                                    <p>
                                                        {{ $index + 1 }}. {{ $participant->full_name }}<br>
                                                        email: {{ $participant->email }}<br>
                                                        tel: {{ $participant->phone }}
                                                    </p>
                                                @endforeach
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>

                    <div class="driver-page-btn-wrapper">
                        <a class="driver-page-btn btn" href="{{ route('master-classes.create') }}">
                            Добавить мастер-класс
                        </a>
                    </div>
                </div>

                @include('partials.menu', ['types' => $types, 'currentType' => null])
            </div>
        </div>
    </div>
@endsection
