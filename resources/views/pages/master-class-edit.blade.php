@extends('layouts.app')

@section('title', 'Редактирование мастер-класса')

@section('before_main')
    <div class="row row--nogutter top-line">
        <div class="line"></div>
    </div>
@endsection

@section('content')
    <div class="main">
        <div class="row">
            <div class="row--small">
                <form action="{{ route('master-classes.update', $masterClass) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <h2>Редактирование мастер-класса</h2>

                    <div class="form-group">
                        <label>Вид творчества</label>
                        <input type="text" value="{{ $masterClass->creativityType->name }}" disabled>
                    </div>

                    <div class="form-group">
                        <label>Название</label>
                        <input type="text" value="{{ $masterClass->title }}" disabled>
                    </div>

                    <div class="form-group">
                        <label>Дата и время</label>
                        <input type="text" value="{{ $masterClass->class_date->format('d.m.Y') }} {{ $masterClass->slotsLabel() }}" disabled>
                    </div>

                    <div class="form-group">
                        <label for="description">Описание мастер-класса</label>
                        <textarea id="description" name="description" required>{{ old('description', $masterClass->description) }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="price">Стоимость мастер-класса (₽)</label>
                        <input id="price" type="number" name="price" step="0.01" min="0" value="{{ old('price', $masterClass->price) }}" required>
                    </div>

                    <div class="form-group">
                        <button class="btn" type="submit">Сохранить</button>
                        <a class="btn" href="{{ route('cabinet.index') }}">Назад</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
