<?php

namespace App\Http\Requests\Player;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePlayerRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'game_id'         => ['sometimes', 'uuid', 'exists:games,id'],
            'current_team_id' => ['nullable', 'uuid', 'exists:teams,id'],
            'pseudo'          => ['sometimes', 'string', 'max:100'],
            'real_name'       => ['nullable', 'string', 'max:150'],
            'nationality'     => ['nullable', 'string', 'max:10'],
            'photo_url'       => ['nullable', 'url'],
        ];
    }
}
