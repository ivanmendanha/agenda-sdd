<?php

namespace App\Reservations;

use App\Exceptions\ReservationException;
use App\Models\Reservation;
use App\Models\Room;
use Illuminate\Support\Facades\DB;

class CreateReservation
{
    /**
     * Store one confirmed reservation, or nothing at all.
     *
     * The room row is locked and both the situation and the overlap are read again
     * inside the transaction, so a concurrent writer cannot double-book the interval.
     *
     * @throws ReservationException
     */
    public function handle(Room $room, string $date, string $start, string $end, string $subject): Reservation
    {
        return DB::transaction(function () use ($room, $date, $start, $end, $subject): Reservation {
            $locked = Room::query()->whereKey($room->getKey())->lockForUpdate()->firstOrFail();

            if (! $locked->active) {
                throw ReservationException::inactiveRoom();
            }

            $overlaps = Reservation::query()
                ->where('room_id', $locked->getKey())
                ->whereDate('date', $date)
                ->where('status', 'confirmed')
                ->where('starts_at', '<', $end)
                ->where('ends_at', '>', $start)
                ->exists();

            if ($overlaps) {
                throw ReservationException::conflict();
            }

            return Reservation::create([
                'room_id' => $locked->getKey(),
                'date' => $date,
                'starts_at' => $start,
                'ends_at' => $end,
                'subject' => $subject,
                'status' => 'confirmed',
            ]);
        });
    }
}
