<?php

namespace App\Http\Controllers;

use App\Http\Resources\EventResource;
use App\Http\Resources\MatchResource;
use App\Http\Resources\PlayerResource;
use App\Http\Resources\PlayerStatResource;
use App\Http\Resources\TeamResource;
use App\Http\Resources\TransactionResource;
use App\Http\Requests\Player\StorePlayerRequest;
use App\Http\Requests\Player\UpdatePlayerRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Event;
use App\Models\GameMatch;
use App\Models\Player;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    public function index(Request $request)
    {
        $players = Player::with(['game', 'currentTeam'])
            ->when($request->query('game'), fn($q, $game) =>
                $q->whereHas('game', fn($gq) => $gq->where('slug', $game))
            )
            ->when($request->query('region'), fn($q, $region) =>
                $q->whereHas('currentTeam', fn($tq) => $tq->where('region', $region))
            )
            ->when($request->query('sort') === 'pseudo', fn($q) => $q->orderBy('pseudo'))
            ->paginate(15);

        return ApiResponse::success('Liste des joueurs récupérée', PlayerResource::collection($players));
    }

    public function search(Request $request)
    {
        $q = $request->query('q', '');
        $players = Player::with(['game', 'currentTeam'])
            ->where(fn($query) =>
                $query->whereRaw('LOWER(pseudo) LIKE ?', ['%' . strtolower($q) . '%'])
                    ->orWhereRaw('LOWER(real_name) LIKE ?', ['%' . strtolower($q) . '%'])
            )
            ->get();

        return ApiResponse::success('Résultats de recherche', PlayerResource::collection($players));
    }

    public function show(string $id)
    {
        $player = Player::with(['game', 'currentTeam'])->findOrFail($id);
        return ApiResponse::success('Fiche joueur récupérée', PlayerResource::make($player));
    }

    public function stats(string $id)
    {
        $player = Player::findOrFail($id);
        $stats = $player->stats()->with(['match', 'matchMap', 'team'])->get();
        return ApiResponse::success('Statistiques du joueur', PlayerStatResource::collection($stats));
    }

    public function teams(string $id)
    {
        $player = Player::findOrFail($id);
        $transactions = $player->transactions()->with('team')->orderBy('transaction_date', 'desc')->get();
        return ApiResponse::success('Équipes du joueur', TransactionResource::collection($transactions));
    }

    public function events(string $id)
    {
        Player::findOrFail($id);
        $events = Event::with('game')
            ->whereHas('matches.playerStats', fn($q) => $q->where('player_id', $id))
            ->orderBy('start_date', 'desc')
            ->get();
        return ApiResponse::success('Événements du joueur', EventResource::collection($events));
    }

    public function matches(string $id)
    {
        Player::findOrFail($id);
        $matches = GameMatch::with(['event', 'team1', 'team2'])
            ->whereHas('playerStats', fn($q) => $q->where('player_id', $id))
            ->orderBy('scheduled_at', 'desc')
            ->paginate(15);
        return ApiResponse::success('Matchs du joueur', MatchResource::collection($matches));
    }

    public function follow(Request $request, string $id)
    {
        $user = $request->user();
        Player::findOrFail($id);

        if ($user->followedPlayers()->where('player_id', $id)->exists()) {
            return ApiResponse::error('Joueur déjà suivi', null, 400);
        }

        $user->followedPlayers()->attach($id, ['followed_at' => now()]);
        return ApiResponse::success('Joueur ajouté aux suivis');
    }

    public function unfollow(Request $request, string $id)
    {
        $request->user()->followedPlayers()->detach($id);
        return ApiResponse::success('Suivi retiré');
    }

    public function store(StorePlayerRequest $request)
    {
        $player = Player::create($request->validated());
        return ApiResponse::success('Joueur créé', PlayerResource::make($player->load(['game', 'currentTeam'])), 201);
    }

    public function update(UpdatePlayerRequest $request, string $id)
    {
        $player = Player::findOrFail($id);
        $player->update($request->validated());
        return ApiResponse::success('Joueur mis à jour', PlayerResource::make($player->load(['game', 'currentTeam'])));
    }

    public function destroy(string $id)
    {
        Player::findOrFail($id)->delete();
        return ApiResponse::success('Joueur supprimé');
    }
}
