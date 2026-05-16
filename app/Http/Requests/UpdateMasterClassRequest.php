<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateMasterClassRequest extends FormRequest
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
            'description' => ['required', 'string', 'min:20', 'max:4000'],
            'price' => ['required', 'numeric', 'min:0', 'max:100000'],
        ];
    }
}
