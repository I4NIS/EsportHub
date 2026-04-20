<?php

namespace App\Http\Requests\Match;

use Illuminate\Foundation\Http\FormRequest;

class StoreMatchRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'event_id'     => ['nullable', 'uuid', 'exists:events,id'],
            'team1_id'     => ['required', 'uuid', 'exists:teams,id', 'different:team2_id'],
            'team2_id'     => ['required', 'uuid', 'exists:teams,id'],
            'score_team1'  => ['nullable', 'integer', 'min:0'],
            'score_team2'  => ['nullable', 'integer', 'min:0'],
            'status'       => ['required', 'in:upcoming,live,completed'],
            'scheduled_at' => ['nullable', 'date'],
        ];
    }
}
