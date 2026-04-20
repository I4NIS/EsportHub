<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\RefreshToken;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RefreshRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'username' => $request->username,
            'birthdate' => $request->birthdate,
            'avatar_url' => $request->avatar_url,
        ]);

        return $this->generateTokens($user, 201, 'Inscription réussie');
    }

    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Les identifiants sont incorrects.'],
            ]);
        }

        return $this->generateTokens($user, 200, 'Connexion réussie');
    }

    public function refresh(RefreshRequest $request)
    {
        $hashedToken = hash('sha256', $request->refresh_token);

        $refreshToken = RefreshToken::where('token', $hashedToken)
            ->where('revoked', false)
            ->where('expires_at', '>', now())
            ->first();

        if (!$refreshToken) {
            throw ValidationException::withMessages([
                'refresh_token' => ['Token invalide ou expiré.']
            ]);
        }

        $user = $refreshToken->user;

        $refreshToken->update(['revoked' => true]);
        $user->tokens()->delete();

        return $this->generateTokens($user, 200, 'Token rafraîchi');
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        $refreshCount = $user->refreshTokens()->where('revoked', false)->count();
        $accessCount = $user->tokens()->count();

        if ($accessCount === 0 && $refreshCount === 0) {
            return ApiResponse::success("Aucune session active n'a été trouvée.");
        }

        $user->refreshTokens()->update(['revoked' => true]);
        $user->tokens()->delete();

        $message = sprintf(
            "Déconnexion réussie. %d accès supprimé(s) et %d session(s) de rafraîchissement révoquée(s).",
            $accessCount,
            $refreshCount
        );

        return ApiResponse::success($message);
    }

    private function generateTokens(User $user, int $statusCode, string $message)
    {
        $accessToken = $user->createToken('auth_token')->plainTextToken;

        $refreshTokenString = Str::random(64);

        $user->refreshTokens()->create([
            'token' => hash('sha256', $refreshTokenString),
            'expires_at' => now()->addDays(30),
            'revoked' => false,
        ]);

        return ApiResponse::success($message, [
            'user' => UserResource::make($user),
            'access_token' => $accessToken,
            'refresh_token' => $refreshTokenString,
            'token_type' => 'Bearer',
        ], $statusCode);
    }
}
