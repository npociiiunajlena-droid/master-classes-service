@extends('layouts.app')

@section('title', 'Добавление мастер-класса')

@section('before_main')
    <div class="row row--nogutter top-line">
        <div class="line"></div>
    </div>
@endsection

@section('content')
    <div class="main">
        <div class="row">
            <div class="row--small">
                <form action="{{ route('master-classes.store') }}" method="POST">
                    @csrf
                    <h2>Форма добавления мастер-класса</h2>

                    <div class="form-group">
                        <label for="creativity_type_id">Вид творчества</label>
                        <select id="creativity_type_id" name="creativity_type_id" required>
                            <option value="">Выберите направление</option>
                            @foreach($types as $type)
                                <option value="{{ $type->id }}" @selected(old('creativity_type_id') == $type->id)>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="title">Название мастер-класса</label>
                        <input id="title" type="text" name="title" value="{{ old('title') }}" maxlength="200" required>
                    </div>

                    <div class="form-group">
                        <label for="description">Описание мастер-класса</label>
                        <textarea id="description" name="description" required>{{ old('description') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="class_date">Дата</label>
                        <input id="class_date" type="date" name="class_date" value="{{ old('class_date') }}" min="{{ now()->toDateString() }}" required>
                    </div>

                    <div class="form-group">
                        <label for="class_time">Время (фиксированные слоты 9-11, 11-13, 13-15, 15-17)</label>
                        <select id="class_time" name="class_time" required>
                            <option value="">Выберите слот</option>
                            @foreach($slots as $slot)
                                <option value="{{ $slot }}" @selected(old('class_time') === $slot)>
                                    {{ $slot }} - {{ \Carbon\CarbonImmutable::createFromFormat('H:i', $slot)->addHours(2)->format('H:i') }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="max_participants">Количество человек в группе</label>
                        <input id="max_participants" type="number" name="max_participants" min="1" max="50" value="{{ old('max_participants') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="price">Стоимость мастер-класса (₽)</label>
                        <input id="price" type="number" step="0.01" min="0" name="price" value="{{ old('price') }}" required>
                    </div>

                    <div class="form-group">
                        <button class="btn" type="submit">Отправить</button>
                        <a class="btn" href="{{ route('cabinet.index') }}">Назад</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var busySlotsByDate = @json($busySlotsByDate);
            var dateInput = document.getElementById('class_date');
            var timeSelect = document.getElementById('class_time');

            function updateDisabledSlots() {
                var selectedDate = dateInput.value;
                var busySlots = busySlotsByDate[selectedDate] || [];

                Array.from(timeSelect.options).forEach(function (option) {
                    if (!option.value) {
                        return;
                    }

                    option.disabled = busySlots.includes(option.value);
                });

                if (timeSelect.selectedOptions.length > 0 && timeSelect.selectedOptions[0].disabled) {
                    timeSelect.value = '';
                }
            }

            dateInput.addEventListener('change', updateDisabledSlots);
            updateDisabledSlots();
        });
    </script>
@endpush
