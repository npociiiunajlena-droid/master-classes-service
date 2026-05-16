@extends('layouts.app')

@section('title', 'Подтверждение записи')

@section('before_main')
    <div class="row row--nogutter top-line">
        <div class="line"></div>
    </div>
@endsection

@section('content')
    <div class="main">
        <div class="row">
            <div class="row--small">
                <form action="{{ route('enrollments.store', $masterClass) }}" method="POST">
                    @csrf
                    <h2>Подтверждение записи</h2>

                    <div class="form-group">
                        <label>ФИО пользователя</label>
                        <input type="text" value="{{ $user->full_name }}" disabled>
                    </div>

                    <div class="form-group">
                        <label>Вид творчества</label>
                        <input type="text" value="{{ $masterClass->creativityType->name }}" disabled>
                    </div>

                    <div class="form-group">
                        <label>ФИО мастера</label>
                        <input type="text" value="{{ $masterClass->master->full_name }}" disabled>
                    </div>

                    <div class="form-group">
                        <label>Дата и время</label>
                        <input type="text" value="{{ $masterClass->class_date->format('d.m.Y') }} {{ $masterClass->slotsLabel() }}" disabled>
                    </div>

                    <div class="form-group">
                        <button class="btn" type="submit">Подтвердить</button>
                    </div>
                </form>

                <form action="{{ route('enrollments.cancel', $masterClass) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <button class="btn" type="submit">Отменить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
