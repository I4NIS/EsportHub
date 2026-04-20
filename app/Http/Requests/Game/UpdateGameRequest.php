<?php

namespace App\Http\Requests\Game;

use App\Models\Game;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class UpdateGameRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        if ($this->has('name')) {
            $this->merge([
                'slug' => Str::slug($this->name),
            ]);
        }
    }

    public function rules(): array
    {
        $identifier = $this->route('id');

        $game = Game::where('id', $identifier)
            ->orWhere('slug', $identifier)
            ->first();

        $gameId = $game?->id;

        return [
            'name' => ['sometimes', 'string', 'max:100', 'unique:games,name,' . $gameId],
            'slug' => ['sometimes', 'string', 'max:50', 'unique:games,slug,' . $gameId],
            'logo_url' => ['sometimes', 'url'],
        ];
    }
}
