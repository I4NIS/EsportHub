<?php

namespace App\Http\Requests\Team;

use Illuminate\Foundation\Http\FormRequest;

class StoreTeamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'game_id' => ['required', 'uuid', 'exists:games,id'],
            'name' => ['required', 'string', 'max:150', 'unique:teams,name'],
            'logo_url' => ['required', 'url'],
            'region' => ['required', 'string', 'max:20'],
            'rank' => ['nullable', 'integer'],
            'earnings' => ['nullable', 'numeric'],
        ];
    }
}
