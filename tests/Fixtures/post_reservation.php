<?php

/**
 * Sends one `POST /api/reservations` in its own PHP process so the concurrency test can run
 * two HTTP writers against a single sqlite file. Prints the status code and the body.
 *
 * Usage: php tests/Fixtures/post_reservation.php {roomId} {date} {start} {end} {subject}
 */

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

require __DIR__.'/../../vendor/autoload.php';

/** @var Application $app */
$app = require __DIR__.'/../../bootstrap/app.php';

[, $roomId, $date, $start, $end, $subject] = $argv;

$request = Request::create('/api/reservations', 'POST', [
    'room_id' => (int) $roomId,
    'date' => $date,
    'start' => $start,
    'end' => $end,
    'subject' => $subject,
]);

$request->headers->set('Accept', 'application/json');

$response = $app->make(Kernel::class)->handle($request);

echo $response->getStatusCode().' '.$response->getContent();
