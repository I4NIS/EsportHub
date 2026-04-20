<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Http\Requests\Event\StoreEventRequest;
use App\Http\Requests\Event\UpdateEventRequest;
use App\Http\Resources\EventResource;
use App\Http\Responses\ApiResponse;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::with('game')->orderBy('start_date', 'desc')->get();
        return ApiResponse::success('Liste des événements récupérée', EventResource::collection($events));
    }

    public function show(string $id)
    {
        $event = Event::with('game')->findOrFail($id);
        return ApiResponse::success('Détails de l\'événement récupérés', EventResource::make($event));
    }

    public function store(StoreEventRequest $request)
    {
        $event = Event::create($request->validated());
        return ApiResponse::success('Événement créé', EventResource::make($event->load('game')), 201);
    }

    public function update(UpdateEventRequest $request, string $id)
    {
        $event = Event::findOrFail($id);
        $event->update($request->validated());
        return ApiResponse::success('Événement mis à jour', EventResource::make($event->load('game')));
    }

    public function destroy(string $id)
    {
        Event::findOrFail($id)->delete();
        return ApiResponse::success('Événement supprimé');
    }
}
