<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Http\Resources\GameResource;
use App\Http\Responses\ApiResponse;
use App\Http\Requests\Game\StoreGameRequest;
use App\Http\Requests\Game\UpdateGameRequest;

class GameController extends Controller
{
    public function index()
    {
        return ApiResponse::success(
            'Liste des jeux récupérée',
            GameResource::collection(Game::all())
        );
    }

    public function show(string $identifier)
    {
        return ApiResponse::success(
            'Détails du jeu récupérés',
            GameResource::make($this->findGame($identifier))
        );
    }

    public function store(StoreGameRequest $request)
    {
        $game = Game::create($request->validated());

        return ApiResponse::success(
            'Jeu créé avec succès',
            GameResource::make($game),
            201
        );
    }

    public function update(UpdateGameRequest $request, string $identifier)
    {
        $game = $this->findGame($identifier);
        $game->update($request->validated());

        return ApiResponse::success(
            'Jeu mis à jour avec succès',
            GameResource::make($game)
        );
    }

    public function destroy(string $identifier)
    {
        $this->findGame($identifier)->delete();

        return ApiResponse::success('Jeu supprimé avec succès');
    }

    private function findGame(string $identifier): Game
    {
        return Game::where('id', $identifier)
            ->orWhere('slug', $identifier)
            ->firstOrFail();
    }
}
