<?php

namespace App\Http\Controllers;

use App\Models\GameMatch;
use App\Models\Team;
use App\Models\Transaction;
use App\Http\Resources\MatchResource;
use App\Http\Resources\PlayerResource;
use App\Http\Resources\TeamResource;
use App\Http\Resources\TransactionResource;
use App\Http\Responses\ApiResponse;
use App\Http\Requests\Team\StoreTeamRequest;
use App\Http\Requests\Team\UpdateTeamRequest;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index(Request $request)
    {
        $teams = Team::with('game')
            ->filter($request->only(['game', 'region', 'sort']))
            ->paginate($request->query('limit', 15));

        return ApiResponse::success('Liste des équipes récupérée', TeamResource::collection($teams));
    }

    public function rankings(Request $request)
    {
        $teams = Team::with('game')
            ->filter($request->only(['game', 'region']))
            ->whereNotNull('rank')
            ->orderBy('rank', 'asc')
            ->paginate($request->query('limit', 15));

        return ApiResponse::success('Classement des équipes récupéré', TeamResource::collection($teams));
    }

    public function search(Request $request)
    {
        $q = $request->query('q', '');
        $teams = Team::with('game')
            ->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($q) . '%'])
            ->get();
        return ApiResponse::success('Résultats de recherche', TeamResource::collection($teams));
    }

    public function show(string $id)
    {
        $team = Team::withCount('likedByUsers')->with(['game', 'players'])->findOrFail($id);
        return ApiResponse::success('Fiche équipe récupérée', TeamResource::make($team));
    }

    public function liveMatches(string $id)
    {
        Team::findOrFail($id);
        $matches = GameMatch::with(['event', 'team1', 'team2'])
            ->where(fn($q) => $q->where('team1_id', $id)->orWhere('team2_id', $id))
            ->where('status', 'live')
            ->get();
        return ApiResponse::success('Matchs en direct de l\'équipe', MatchResource::collection($matches));
    }

    public function matches(string $id)
    {
        Team::findOrFail($id);
        $matches = GameMatch::with(['event', 'team1', 'team2'])
            ->where(fn($q) => $q->where('team1_id', $id)->orWhere('team2_id', $id))
            ->orderBy('scheduled_at', 'desc')
            ->paginate(15);
        return ApiResponse::success('Matchs de l\'équipe récupérés', MatchResource::collection($matches));
    }

    public function players(string $id)
    {
        $team = Team::with(['players.game'])->findOrFail($id);
        return ApiResponse::success('Joueurs de l\'équipe récupérés', PlayerResource::collection($team->players));
    }

    public function transactions(string $id)
    {
        Team::findOrFail($id);
        $transactions = Transaction::with('player')
            ->where('team_id', $id)
            ->orderBy('transaction_date', 'desc')
            ->get();
        return ApiResponse::success('Transactions de l\'équipe récupérées', TransactionResource::collection($transactions));
    }

    public function store(StoreTeamRequest $request)
    {
        $team = Team::create($request->validated());
        return ApiResponse::success('Équipe créée', TeamResource::make($team), 201);
    }

    public function update(UpdateTeamRequest $request, string $id)
    {
        $team = Team::findOrFail($id);
        $team->update($request->validated());
        return ApiResponse::success('Équipe mise à jour', TeamResource::make($team));
    }

    public function destroy(string $id)
    {
        Team::findOrFail($id)->delete();
        return ApiResponse::success('Équipe supprimée');
    }

    public function like(Request $request, string $id)
    {
        $user = $request->user();
        Team::findOrFail($id);
        if ($user->likedTeams()->where('team_id', $id)->exists()) {
            return ApiResponse::error('Équipe déjà likée', null, 400);
        }
        $user->likedTeams()->attach($id, ['liked_at' => now()]);
        return ApiResponse::success('Équipe ajoutée aux favoris');
    }

    public function unlike(Request $request, string $id)
    {
        $request->user()->likedTeams()->detach($id);
        return ApiResponse::success('Like retiré');
    }
}
