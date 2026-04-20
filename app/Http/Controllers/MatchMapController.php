<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use Illuminate\Http\Request;

class MatchMapController extends Controller
{
    public function store(Request $request, string $matchId)
    {
        return ApiResponse::error('Not implemented', null, 501)->toResponse($request);
    }

    public function update(Request $request, string $matchId, string $mapId)
    {
        return ApiResponse::error('Not implemented', null, 501)->toResponse($request);
    }

    public function destroy(string $matchId, string $mapId)
    {
        return ApiResponse::error('Not implemented', null, 501);
    }
}
