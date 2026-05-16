<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\MasterClass;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreMasterClassRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->isMaster();
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'creativity_type_id' => ['required', 'integer', 'exists:creativity_types,id'],
            'title' => ['required', 'string', 'min:5', 'max:200'],
            'description' => ['required', 'string', 'min:20', 'max:4000'],
            'class_date' => ['required', 'date', 'date_format:Y-m-d', 'after_or_equal:today'],
            'class_time' => ['required', 'string', 'in:'.implode(',', MasterClass::AVAILABLE_SLOTS)],
            'max_participants' => ['required', 'integer', 'min:1', 'max:50'],
            'price' => ['required', 'numeric', 'min:0', 'max:100000'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if (! $this->user()) {
                return;
            }

            $date = $this->string('class_date')->toString();
            $time = $this->string('class_time')->toString();

            if ($date === '' || $time === '') {
                return;
            }

            $alreadyBusy = MasterClass::query()
                ->where('master_id', $this->user()->id)
                ->whereDate('class_date', $date)
                ->whereTime('class_time', MasterClass::normalizeSlot($time))
                ->exists();

            if ($alreadyBusy) {
                $validator->errors()->add('class_time', 'На выбранные дату и время у вас уже запланирован мастер-класс.');
            }
        });
    }
}
