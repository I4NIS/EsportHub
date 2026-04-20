<?php

namespace App\Http\Controllers;

use App\Http\Requests\Match\StoreMatchRequest;
use App\Http\Requests\Match\UpdateMatchRequest;
use App\Http\Resources\MatchResource;
use App\Http\Resources\PlayerStatResource;
use App\Http\Responses\ApiResponse;
use App\Models\GameMatch;
use Illuminate\Http\Request;

class MatchController extends Controller
{
    public function index(Request $request)
    {
        $matches = GameMatch::with(['event', 'team1', 'team2'])
            ->when($request->query('status'), fn($q, $status) => $q->where('status', $status))
            ->when($request->query('game'), fn($q, $game) =>
                $q->whereHas('event.game', fn($gq) => $gq->where('slug', $game))
            )
            ->orderBy('scheduled_at', 'desc')
            ->paginate(15);

        return ApiResponse::success('Liste des matchs récupérée', MatchResource::collection($matches));
    }

    public function live(Request $request)
    {
        $matches = GameMatch::with(['event', 'team1', 'team2'])
            ->where('status', 'live')
            ->when($request->query('game'), fn($q, $game) =>
                $q->whereHas('event.game', fn($gq) => $gq->where('slug', $game))
            )
            ->get();

        return ApiResponse::success('Matchs en direct récupérés', MatchResource::collection($matches));
    }

    public function show(string $id)
    {
        $match = GameMatch::with(['event', 'team1', 'team2', 'maps'])->findOrFail($id);
        return ApiResponse::success('Détails du match récupérés', MatchResource::make($match));
    }

    public function stats(string $id)
    {
        $match = GameMatch::findOrFail($id);
        $stats = $match->playerStats()->with(['player', 'team', 'matchMap'])->get();
        return ApiResponse::success('Statistiques du match récupérées', PlayerStatResource::collection($stats));
    }

    public function store(StoreMatchRequest $request)
    {
        $match = GameMatch::create($request->validated());
        return ApiResponse::success('Match créé', MatchResource::make($match->load(['event', 'team1', 'team2'])), 201);
    }

    public function update(UpdateMatchRequest $request, string $id)
    {
        $match = GameMatch::findOrFail($id);
        $match->update($request->validated());
        return ApiResponse::success('Match mis à jour', MatchResource::make($match->load(['event', 'team1', 'team2'])));
    }

    public function destroy(string $id)
    {
        GameMatch::findOrFail($id)->delete();
        return ApiResponse::success('Match supprimé');
    }
}
