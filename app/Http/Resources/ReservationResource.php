<?php

namespace App\Http\Resources;

use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Reservation
 */
class ReservationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array{id: int, room_id: int, date: string, starts_at: string, ends_at: string, subject: string, status: string}
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'room_id' => $this->room_id,
            'date' => $this->date->toDateString(),
            'starts_at' => $this->starts_at,
            'ends_at' => $this->ends_at,
            'subject' => $this->subject,
            'status' => $this->status,
        ];
    }
}
