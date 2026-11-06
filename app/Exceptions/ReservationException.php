<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Contracts\Debug\ShouldntReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReservationException extends Exception implements ShouldntReport
{
    public function __construct(string $message, private readonly int $status)
    {
        parent::__construct($message);
    }

    public static function inactiveRoom(): self
    {
        return new self('Sala inativa não permite nova reserva', 422);
    }

    public static function conflict(): self
    {
        return new self('Conflito de horário', 409);
    }

    public function render(Request $request): JsonResponse
    {
        return response()->json(['message' => $this->getMessage()], $this->status);
    }
}
