<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['name', 'name_normalized', 'room_id'])]
class RoomResource extends Model
{
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
}
