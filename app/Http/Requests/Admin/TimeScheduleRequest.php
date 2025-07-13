<?php

namespace App\Http\Requests\Admin;

use App\Enums\StatusEnum;
use App\Rules\EnumValidation;
use Illuminate\Foundation\Http\FormRequest;

class TimeScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'start_time' => ['required'],
            'end_time'   => ['required'],
            'duration'   => ['nullable'],
            'status'     => ['required', new EnumValidation(StatusEnum::class)],
        ];
    }
}
