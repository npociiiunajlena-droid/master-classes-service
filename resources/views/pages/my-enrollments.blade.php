@extends('layouts.app')

@section('title', 'Мои записи')

@section('content')
    <div class="main">
        <div class="row">
            <div class="hover"></div>
            <div class="title">Мои записи</div>
            <div class="row--small grid between">
                <div class="content">
                    <h2>Подтверждённые записи пользователя</h2>

                    @if($masterClasses->isEmpty())
                        <p>У вас пока нет подтверждённых записей на мастер-классы.</p>
                    @else
                        <table class="driver-page-table">
                            <tbody>
                                @foreach($masterClasses as $masterClass)
                                    <tr>
                                        <td>{{ $masterClass->class_date->format('d.m.Y') }} {{ $masterClass->slotsLabel() }}</td>
                                        <td>
                                            <b>{{ $masterClass->title }}</b><br>
                                            Вид творчества: {{ $masterClass->creativityType->name }}<br>
                                            Ведущий: {{ $masterClass->master->full_name }}<br>
                                            Стоимость: {{ number_format((float) $masterClass->price, 2, ',', ' ') }} ₽
                                            <div class="enrollment-actions">
                                                <form action="{{ route('enrollments.destroy', $masterClass) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn" type="submit" onclick="return confirm('Отменить эту запись?');">
                                                        Отменить запись
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>

                @include('partials.menu', ['types' => $types, 'currentType' => null])
            </div>
        </div>
    </div>
@endsection
