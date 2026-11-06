<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoomRequest;
use App\Http\Requests\UpdateRoomRequest;
use App\Http\Resources\RoomResource;
use App\Models\Room;
use App\Reservations\IntervalValidator;
use App\Rooms\ListRooms;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, ListRooms $listRooms, IntervalValidator $intervalValidator): AnonymousResourceCollection
    {
        $filters = $request->validate([
            'min_capacity' => ['nullable', 'integer', 'between:1,999'],
            'resource' => ['nullable', 'string'],
            'date' => ['nullable', 'required_with:start,end', 'date_format:Y-m-d'],
            'start' => ['nullable', 'required_with:date,end', 'date_format:H:i'],
            'end' => ['nullable', 'required_with:date,start', 'date_format:H:i'],
        ], [
            'min_capacity.integer' => 'A capacidade deve ser um número inteiro de 1 a 999',
            'min_capacity.between' => 'A capacidade deve ser um número inteiro de 1 a 999',
            'date.required_with' => 'Informe data e horários válidos',
            'date.date_format' => 'Informe data e horários válidos',
            'start.required_with' => 'Informe data e horários válidos',
            'start.date_format' => 'Informe data e horários válidos',
            'end.required_with' => 'Informe data e horários válidos',
            'end.date_format' => 'Informe data e horários válidos',
        ]);

        if (filled($filters['date'] ?? null)) {
            $failure = $intervalValidator->firstFailure($filters['date'], $filters['start'], $filters['end']);

            if ($failure !== null) {
                throw ValidationException::withMessages(['date' => [$failure]]);
            }
        }

        return RoomResource::collection($listRooms->handle($filters));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoomRequest $request): JsonResponse
    {
        $data = $request->validated();

        $room = Room::create([
            'name' => $data['name'],
            'capacity' => $data['capacity'],
            'active' => $data['active'] ?? true,
        ]);

        $this->syncResources($room, $data['resources'] ?? []);

        return RoomResource::make($room->load('resources'))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Room $room): RoomResource
    {
        return RoomResource::make($room->load('resources'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoomRequest $request, Room $room): RoomResource
    {
        $data = $request->validated();

        $room->update([
            'name' => $data['name'],
            'capacity' => $data['capacity'],
            'active' => $data['active'] ?? $room->active,
        ]);

        $this->syncResources($room, $data['resources'] ?? []);

        return RoomResource::make($room->load('resources'));
    }

    /**
     * Replace the room's resource tags, keeping the first spelling of a duplicate.
     *
     * @param  array<int, string|null>  $names
     */
    private function syncResources(Room $room, array $names): void
    {
        $tags = [];

        foreach ($names as $name) {
            $trimmed = trim((string) $name);

            if ($trimmed === '') {
                continue;
            }

            $normalized = Str::lower($trimmed);

            if (! array_key_exists($normalized, $tags)) {
                $tags[$normalized] = $trimmed;
            }
        }

        $room->resources()->delete();

        foreach ($tags as $normalized => $name) {
            $room->resources()->create([
                'name' => $name,
                'name_normalized' => (string) $normalized,
            ]);
        }
    }
}
