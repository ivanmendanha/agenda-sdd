<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReservationRequest;
use App\Http\Resources\ReservationResource;
use App\Models\Room;
use App\Reservations\CreateReservation;
use App\Reservations\IntervalValidator;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class ReservationController extends Controller
{
    /**
     * Store one confirmed reservation for the selected room.
     *
     * A missing or unknown `room_id` answers 404 `Sala não encontrada`. The interval checks run
     * in the shared order, then `CreateReservation` reads the situation and the conflict again
     * inside its transaction.
     */
    public function store(
        StoreReservationRequest $request,
        IntervalValidator $intervalValidator,
        CreateReservation $createReservation,
    ): JsonResponse {
        $data = $request->validated();

        $room = Room::query()->findOrFail($request->integer('room_id'));

        $failure = $intervalValidator->firstFailure($data['date'], $data['start'], $data['end']);

        if ($failure !== null) {
            throw ValidationException::withMessages(['date' => [$failure]]);
        }

        $reservation = $createReservation->handle(
            $room,
            $data['date'],
            $data['start'],
            $data['end'],
            $data['subject'],
        );

        return ReservationResource::make($reservation)
            ->response()
            ->setStatusCode(201);
    }
}
