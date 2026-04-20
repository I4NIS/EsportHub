<?php

namespace App\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'game_id'    => ['sometimes', 'uuid', 'exists:games,id'],
            'name'       => ['sometimes', 'string', 'max:200'],
            'logo_url'   => ['nullable', 'url'],
            'prize_pool' => ['nullable', 'string', 'max:50'],
            'start_date' => ['nullable', 'date'],
            'end_date'   => ['nullable', 'date', 'after_or_equal:start_date'],
            'status'     => ['sometimes', 'in:upcoming,ongoing,completed'],
        ];
    }
}
