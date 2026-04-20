<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\MatchMapController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\UserController;

Route::middleware('throttle:public')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
    });

    Route::get('/games', [GameController::class, 'index']);
    Route::get('/games/{id}', [GameController::class, 'show']);

    Route::get('/events', [EventController::class, 'index']);

    Route::get('/rankings', [TeamController::class, 'rankings']);
    Route::get('/teams/search', [TeamController::class, 'search']);
    Route::get('/teams', [TeamController::class, 'index']);
    Route::get('/teams/{id}', [TeamController::class, 'show']);
    Route::get('/teams/{id}/matches/live', [TeamController::class, 'liveMatches']);
    Route::get('/teams/{id}/matches', [TeamController::class, 'matches']);
    Route::get('/teams/{id}/players', [TeamController::class, 'players']);
    Route::get('/teams/{id}/transactions', [TeamController::class, 'transactions']);

    Route::get('/matches/live', [MatchController::class, 'live']);
    Route::get('/matches', [MatchController::class, 'index']);
    Route::get('/matches/{id}', [MatchController::class, 'show']);
    Route::get('/matches/{id}/stats', [MatchController::class, 'stats']);

    Route::get('/players/search', [PlayerController::class, 'search']);
    Route::get('/players', [PlayerController::class, 'index']);
    Route::get('/players/{id}', [PlayerController::class, 'show']);
    Route::get('/players/{id}/stats', [PlayerController::class, 'stats']);
    Route::get('/players/{id}/teams', [PlayerController::class, 'teams']);
    Route::get('/players/{id}/events', [PlayerController::class, 'events']);
    Route::get('/players/{id}/matches', [PlayerController::class, 'matches']);
});

Route::middleware(['auth:sanctum', 'throttle:authenticated'])->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    Route::prefix('users/me')->group(function () {
        Route::get('/', [UserController::class, 'me']);
        Route::patch('/', [UserController::class, 'update']);
        Route::patch('/password', [UserController::class, 'updatePassword']);
        Route::patch('/email', [UserController::class, 'updateEmail']);
        Route::delete('/', [UserController::class, 'destroy']);
        Route::get('/export', [UserController::class, 'export']);
        Route::get('/likes', [UserController::class, 'likes']);
        Route::get('/follows', [UserController::class, 'follows']);
    });

    Route::post('/teams/{id}/like', [TeamController::class, 'like']);
    Route::delete('/teams/{id}/like', [TeamController::class, 'unlike']);

    Route::post('/players/{id}/follow', [PlayerController::class, 'follow']);
    Route::delete('/players/{id}/follow', [PlayerController::class, 'unfollow']);

    Route::get('/events/{id}', [EventController::class, 'show']);

    Route::middleware('admin')->group(function () {
        Route::post('/games', [GameController::class, 'store']);
        Route::patch('/games/{id}', [GameController::class, 'update']);
        Route::delete('/games/{id}', [GameController::class, 'destroy']);

        Route::post('/events', [EventController::class, 'store']);
        Route::patch('/events/{id}', [EventController::class, 'update']);
        Route::delete('/events/{id}', [EventController::class, 'destroy']);

        Route::post('/teams', [TeamController::class, 'store']);
        Route::patch('/teams/{id}', [TeamController::class, 'update']);
        Route::delete('/teams/{id}', [TeamController::class, 'destroy']);

        Route::post('/players', [PlayerController::class, 'store']);
        Route::patch('/players/{id}', [PlayerController::class, 'update']);
        Route::delete('/players/{id}', [PlayerController::class, 'destroy']);

        Route::post('/matches', [MatchController::class, 'store']);
        Route::patch('/matches/{id}', [MatchController::class, 'update']);
        Route::delete('/matches/{id}', [MatchController::class, 'destroy']);

        Route::post('/matches/{id}/map-results', [MatchMapController::class, 'store']);
        Route::patch('/matches/{id}/map-results/{mapId}', [MatchMapController::class, 'update']);
        Route::delete('/matches/{id}/map-results/{mapId}', [MatchMapController::class, 'destroy']);

        Route::prefix('admin/users')->group(function () {
            Route::get('/', [UserController::class, 'adminIndex']);
            Route::get('/{id}', [UserController::class, 'adminShow']);
            Route::patch('/{id}', [UserController::class, 'adminUpdate']);
            Route::delete('/{id}', [UserController::class, 'adminDestroy']);
        });
    });
});
