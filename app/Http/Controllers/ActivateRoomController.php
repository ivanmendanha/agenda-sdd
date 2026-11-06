<?php

namespace App\Http\Controllers;

use App\Http\Resources\RoomResource;
use App\Models\Room;

class ActivateRoomController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Room $room): RoomResource
    {
        $room->update(['active' => true]);

        return RoomResource::make($room->load('resources'));
    }
}
