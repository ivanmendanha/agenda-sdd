<?php

namespace App\Http\Resources;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Room
 */
class RoomResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * `availability` stays null unless the room query marked it for a valid interval.
     *
     * @return array{id: int, name: string, capacity: int, resources: list<string>, active: bool, availability: string|null}
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'capacity' => $this->capacity,
            'resources' => $this->resources->pluck('name')->all(),
            'active' => $this->active,
            'availability' => $this->availability,
        ];
    }
}
