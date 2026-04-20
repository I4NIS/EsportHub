<?php

namespace App\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'game_id'    => ['required', 'uuid', 'exists:games,id'],
            'name'       => ['required', 'string', 'max:200'],
            'logo_url'   => ['nullable', 'url'],
            'prize_pool' => ['nullable', 'string', 'max:50'],
            'start_date' => ['nullable', 'date'],
            'end_date'   => ['nullable', 'date', 'after_or_equal:start_date'],
            'status'     => ['required', 'in:upcoming,ongoing,completed'],
        ];
    }
}
