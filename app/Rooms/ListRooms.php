<?php

namespace App\Rooms;

use App\Models\Reservation;
use App\Models\Room;
use App\Reservations\IntervalValidator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class ListRooms
{
    public function __construct(private readonly IntervalValidator $intervalValidator) {}

    /**
     * List rooms narrowed by the filters, and mark availability when the interval passes.
     *
     * @param  array{min_capacity?: int|string|null, resource?: string|null, date?: string|null, start?: string|null, end?: string|null}  $filters
     * @return Collection<int, Room>
     */
    public function handle(array $filters): Collection
    {
        $resource = Str::lower(trim((string) ($filters['resource'] ?? '')));

        $rooms = Room::query()
            ->with('resources')
            ->when(
                filled($filters['min_capacity'] ?? null),
                fn (Builder $query) => $query->where('capacity', '>=', (int) $filters['min_capacity']),
            )
            ->when(
                $resource !== '',
                fn (Builder $query) => $query->whereHas(
                    'resources',
                    fn (Builder $tags) => $tags->where('name_normalized', $resource),
                ),
            )
            ->orderBy('id')
            ->get();

        $date = $filters['date'] ?? null;
        $start = $filters['start'] ?? null;
        $end = $filters['end'] ?? null;

        if (blank($date) || blank($start) || blank($end)) {
            return $rooms;
        }

        if ($this->intervalValidator->firstFailure($date, $start, $end) !== null) {
            return $rooms;
        }

        return $this->markAvailability($rooms, $date, $start, $end);
    }

    /**
     * @param  Collection<int, Room>  $rooms
     * @return Collection<int, Room>
     */
    private function markAvailability(Collection $rooms, string $date, string $start, string $end): Collection
    {
        $busyRoomIds = Reservation::query()
            ->whereIn('room_id', $rooms->where('active', true)->modelKeys())
            ->whereDate('date', $date)
            ->where('status', 'confirmed')
            ->where('starts_at', '<', $end)
            ->where('ends_at', '>', $start)
            ->pluck('room_id')
            ->all();

        return $rooms->each(function (Room $room) use ($busyRoomIds): void {
            if (! $room->active) {
                return;
            }

            $room->availability = in_array($room->getKey(), $busyRoomIds, true)
                ? 'indisponivel'
                : 'disponivel';
        });
    }
}
