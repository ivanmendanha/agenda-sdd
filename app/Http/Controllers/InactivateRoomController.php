<?php

namespace App\Http\Controllers;

use App\Http\Resources\RoomResource;
use App\Models\Room;

class InactivateRoomController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * The gesture applies immediately: no confirmation field, and the room row and
     * its confirmed reservations stay stored.
     */
    public function __invoke(Room $room): RoomResource
    {
        $room->update(['active' => false]);

        return RoomResource::make($room->load('resources'));
    }
}
