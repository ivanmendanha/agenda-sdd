<?php

/**
 * Creates one reservation in its own PHP process so the concurrency test can run two
 * writers against a single sqlite file. Prints `created` or the refusal message.
 *
 * Usage: php tests/Fixtures/create_reservation.php {roomId} {date} {start} {end} {subject}
 */

use App\Models\Room;
use App\Reservations\CreateReservation;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;

require __DIR__.'/../../vendor/autoload.php';

/** @var Application $app */
$app = require __DIR__.'/../../bootstrap/app.php';

$app->make(Kernel::class)->bootstrap();

[, $roomId, $date, $start, $end, $subject] = $argv;

try {
    $app->make(CreateReservation::class)->handle(
        Room::query()->findOrFail((int) $roomId),
        $date,
        $start,
        $end,
        $subject,
    );

    echo 'created';
} catch (Throwable $exception) {
    echo $exception->getMessage();
}
