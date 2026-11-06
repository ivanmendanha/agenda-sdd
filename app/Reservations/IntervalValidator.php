<?php

namespace App\Reservations;

use Illuminate\Support\Carbon;

class IntervalValidator
{
    /**
     * Return the first business rule the interval breaks, or null when it passes.
     *
     * Times are zero-padded `H:i`, so string comparison matches clock order.
     */
    public function firstFailure(string $date, string $start, string $end): ?string
    {
        if ($end <= $start) {
            return 'Horário de fim deve ser posterior ao horário de início';
        }

        $timezone = config('app.timezone');

        if (Carbon::parse($date, $timezone)->startOfDay()->lessThan(Carbon::today($timezone))) {
            return 'A data não pode ser anterior a hoje';
        }

        if ($start < config('reservas.opens_at') || $end > config('reservas.closes_at')) {
            return 'Reservas permitidas apenas entre 8h e 18h';
        }

        if ($this->minutesOf($end) - $this->minutesOf($start) > config('reservas.max_duration_minutes')) {
            return 'Duração máxima permitida é de 4 horas';
        }

        return null;
    }

    private function minutesOf(string $time): int
    {
        [$hours, $minutes] = explode(':', $time);

        return ((int) $hours * 60) + (int) $minutes;
    }
}
