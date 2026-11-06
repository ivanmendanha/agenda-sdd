<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoomRequest;
use App\Http\Resources\RoomResource;
use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class RoomController extends Controller
{
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
