<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\PlayerResource;
use App\Http\Resources\TeamResource;
use App\Http\Resources\UserResource;
use App\Http\Responses\ApiResponse;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Http\Requests\User\UpdatePasswordRequest;
use App\Http\Requests\User\UpdateEmailRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function me(Request $request)
    {
        return ApiResponse::success('Profil récupéré', UserResource::make($request->user()));
    }

    public function update(UpdateProfileRequest $request)
    {
        $user = $request->user();
        $user->update($request->validated());
        return ApiResponse::success('Profil mis à jour', UserResource::make($user));
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $request->user()->update(['password' => Hash::make($request->new_password)]);
        return ApiResponse::success('Mot de passe mis à jour');
    }

    public function updateEmail(UpdateEmailRequest $request)
    {
        $user = $request->user();
        $user->update(['email' => $request->email]);
        return ApiResponse::success('Email mis à jour', UserResource::make($user));
    }

    public function destroy(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();
        $user->refreshTokens()->delete();
        $user->delete();
        return ApiResponse::success('Votre compte a été supprimé définitivement.');
    }

    public function export(Request $request)
    {
        $user = $request->user()->load(['likedTeams.game', 'followedPlayers.currentTeam']);
        return ApiResponse::success('Export des données personnelles', [
            'profile' => UserResource::make($user),
            'liked_teams' => TeamResource::collection($user->likedTeams),
            'followed_players' => PlayerResource::collection($user->followedPlayers),
        ]);
    }

    public function likes(Request $request)
    {
        $teams = $request->user()->likedTeams()->with('game')->get();
        return ApiResponse::success('Équipes aimées', TeamResource::collection($teams));
    }

    public function follows(Request $request)
    {
        $players = $request->user()->followedPlayers()->with(['game', 'currentTeam'])->get();
        return ApiResponse::success('Joueurs suivis', PlayerResource::collection($players));
    }

    public function adminIndex(Request $request)
    {
        $query = User::query();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        if ($request->filled('is_active')) {
            $query->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('username', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('firstname', 'like', "%{$request->search}%")
                  ->orWhere('lastname', 'like', "%{$request->search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate($request->integer('per_page', 20));

        return ApiResponse::success('Liste des utilisateurs', [
            'data' => collect($users->items())->map(fn (User $user) => $this->adminUserArray($user)),
            'meta' => [
                'total' => $users->total(),
                'per_page' => $users->perPage(),
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
            ],
        ]);
    }

    public function adminShow(string $id)
    {
        return ApiResponse::success('Utilisateur récupéré', $this->adminUserArray(User::findOrFail($id)));
    }

    public function adminUpdate(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'role'      => ['sometimes', Rule::in(['user', 'admin'])],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $user->update($validated);

        return ApiResponse::success('Utilisateur mis à jour', $this->adminUserArray($user));
    }

    private function adminUserArray(User $user): array
    {
        return [
            'id'         => $user->id,
            'username'   => $user->username,
            'firstname'  => $user->firstname,
            'lastname'   => $user->lastname,
            'email'      => $user->email,
            'birthdate'  => $user->birthdate?->format('Y-m-d'),
            'avatar_url' => $user->avatar_url,
            'role'       => $user->role,
            'is_active'  => $user->is_active,
            'created_at' => $user->created_at?->toISOString(),
        ];
    }

    public function adminDestroy(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === $request->user()->id) {
            return ApiResponse::error('Vous ne pouvez pas supprimer votre propre compte via cette route.', null, 403);
        }

        $user->tokens()->delete();
        $user->refreshTokens()->delete();
        $user->delete();

        return ApiResponse::success('Utilisateur supprimé.');
    }
}
