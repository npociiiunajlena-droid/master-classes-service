@extends('layouts.app')

@section('title', $currentType->name)

@section('content')
    <div class="main">
        <div class="row">
            <div class="hover"></div>
            <div class="title">{{ $currentType->name }}</div>
            <div class="row--small grid between">
                <div class="content">
                    @if($currentType->hero_image_path)
                        <img class="content-photo" src="{{ asset($currentType->hero_image_path) }}" alt="{{ $currentType->name }}">
                    @endif
                    <p>{{ $currentType->description }}</p>
                </div>

                @include('partials.menu', ['types' => $types, 'currentType' => $currentType])
            </div>

            <div class="row shedule">
                <div class="row--small">
                    <h2>Расписание</h2>

                    @if($masterClasses->isEmpty())
                        <p>Пока нет запланированных мастер-классов.</p>
                    @else
                        <div class="drivers">
                            @foreach($masterClasses as $masterClass)
                                <div class="driver grid">
                                    <div class="driver-left grid">
                                        <div class="driver-photo">
                                            <img src="{{ asset($masterClass->master->photo_path ?? 'assets/img/driver1.png') }}" alt="{{ $masterClass->master->full_name }}">
                                        </div>
                                        <div class="driver-text">
                                            <div class="driver-name">{{ $masterClass->master->full_name }}</div>
                                            <div class="driver-desc">
                                                <b>{{ $masterClass->title }}</b><br>
                                                {{ $masterClass->description }}<br>
                                                Стоимость: {{ number_format((float) $masterClass->price, 2, ',', ' ') }} ₽<br>
                                                Свободных мест: {{ $masterClass->seatsLeft() }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="driver-right">
                                        @guest
                                            <div>Авторизуйтесь для записи</div>
                                        @else
                                            @if(auth()->user()->isMaster())
                                                <button class="driver-btn" type="button" disabled>недоступно</button>
                                            @elseif(in_array($masterClass->id, $userEnrollmentIds, true))
                                                <button class="driver-btn" type="button" disabled>уже записаны</button>
                                            @elseif(!$masterClass->hasFreeSeats())
                                                <button class="driver-btn" type="button" disabled>мест нет</button>
                                            @else
                                                <form action="{{ route('enrollments.confirm', $masterClass) }}" method="GET">
                                                    <button class="driver-btn" type="submit">записаться</button>
                                                </form>
                                            @endif
                                        @endguest
                                        <div class="driver-time">
                                            {{ $masterClass->class_date->format('d.m.Y') }} {{ $masterClass->slotsLabel() }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
