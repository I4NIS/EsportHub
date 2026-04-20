<?php

namespace App\Http\Requests\Team;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTeamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:150', 'unique:teams,name,' . $this->route('id')],
            'logo_url' => ['sometimes', 'url'],
            'region' => ['sometimes', 'string', 'max:20'],
            'rank' => ['sometimes', 'integer'],
            'earnings' => ['sometimes', 'numeric'],
        ];
    }
}
