<?php

use App\Exceptions\ReservationException;
use App\Models\Reservation;
use App\Models\Room;
use App\Reservations\CreateReservation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

test('action stores one confirmed reservation', function () {
    $room = Room::factory()->create();

    $reservation = app(CreateReservation::class)
        ->handle($room, '2026-03-10', '09:00', '11:00', 'Planejamento');

    expect($reservation->status)->toBe('confirmed')
        ->and($reservation->date->toDateString())->toBe('2026-03-10');

    $this->assertDatabaseCount('reservations', 1);
    $this->assertDatabaseHas('reservations', [
        'room_id' => $room->id,
        'starts_at' => '09:00',
        'ends_at' => '11:00',
        'subject' => 'Planejamento',
        'status' => 'confirmed',
    ]);
});

test('action refuses an inactive room', function () {
    $room = Room::factory()->inactive()->create();

    expect(fn () => app(CreateReservation::class)->handle($room, '2026-03-10', '09:00', '11:00', 'Planejamento'))
        ->toThrow(ReservationException::class, 'Sala inativa não permite nova reserva');

    $this->assertDatabaseCount('reservations', 0);
});

test('action refuses an overlapping confirmed reservation', function () {
    $room = Room::factory()->create();
    Reservation::factory()->for($room)->create([
        'date' => '2026-03-10',
        'starts_at' => '09:00',
        'ends_at' => '10:00',
    ]);

    expect(fn () => app(CreateReservation::class)->handle($room, '2026-03-10', '09:30', '10:30', 'Planejamento'))
        ->toThrow(ReservationException::class, 'Conflito de horário');

    $this->assertDatabaseCount('reservations', 1);
});

test('action allows a touching reservation', function () {
    $room = Room::factory()->create();
    Reservation::factory()->for($room)->create([
        'date' => '2026-03-10',
        'starts_at' => '09:00',
        'ends_at' => '10:00',
    ]);
    Reservation::factory()->for($room)->create([
        'date' => '2026-03-10',
        'starts_at' => '11:00',
        'ends_at' => '12:00',
    ]);

    $reservation = app(CreateReservation::class)
        ->handle($room, '2026-03-10', '10:00', '11:00', 'Planejamento');

    expect($reservation->status)->toBe('confirmed');

    $this->assertDatabaseCount('reservations', 3);
    $this->assertDatabaseHas('reservations', [
        'room_id' => $room->id,
        'starts_at' => '10:00',
        'ends_at' => '11:00',
        'subject' => 'Planejamento',
        'status' => 'confirmed',
    ]);
});

test('action ignores an unconfirmed reservation', function () {
    $room = Room::factory()->create();
    Reservation::factory()->for($room)->unconfirmed()->create([
        'date' => '2026-03-10',
        'starts_at' => '09:00',
        'ends_at' => '10:00',
    ]);

    $reservation = app(CreateReservation::class)
        ->handle($room, '2026-03-10', '09:30', '10:30', 'Planejamento');

    expect($reservation->status)->toBe('confirmed');

    $this->assertDatabaseCount('reservations', 2);
    $this->assertDatabaseHas('reservations', [
        'room_id' => $room->id,
        'starts_at' => '09:30',
        'ends_at' => '10:30',
        'status' => 'confirmed',
    ]);
});

test('action confirms exactly one concurrent overlap', function () {
    $database = sys_get_temp_dir().'/salas-concurrency-'.bin2hex(random_bytes(6)).'.sqlite';
    touch($database);

    config(['database.connections.salas_concurrency' => array_merge(
        config('database.connections.sqlite'),
        ['database' => $database],
    )]);

    Artisan::call('migrate', ['--database' => 'salas_concurrency', '--force' => true]);

    $roomId = DB::connection('salas_concurrency')->table('rooms')->insertGetId([
        'name' => 'Sala Concorrente',
        'capacity' => 10,
        'active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $command = [
        PHP_BINARY,
        base_path('tests/Fixtures/create_reservation.php'),
        (string) $roomId,
        '2026-03-10',
        '09:00',
        '11:00',
        'Planejamento',
    ];
    $environment = array_merge(getenv(), [
        'DB_CONNECTION' => 'sqlite',
        'DB_DATABASE' => $database,
    ]);

    $writers = [];

    foreach ([0, 1] as $index) {
        $handle = proc_open(
            $command,
            [1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $pipes,
            base_path(),
            $environment,
        );

        $writers[$index] = ['handle' => $handle, 'pipes' => $pipes];
    }

    $outputs = [];

    foreach ($writers as $writer) {
        $output = trim((string) stream_get_contents($writer['pipes'][1]));
        $errors = trim((string) stream_get_contents($writer['pipes'][2]));

        fclose($writer['pipes'][1]);
        fclose($writer['pipes'][2]);
        proc_close($writer['handle']);

        $outputs[] = $output !== '' ? $output : $errors;
    }

    $confirmed = DB::connection('salas_concurrency')
        ->table('reservations')
        ->where('status', 'confirmed')
        ->count();

    DB::purge('salas_concurrency');

    foreach ([$database, $database.'-wal', $database.'-shm'] as $file) {
        if (file_exists($file)) {
            unlink($file);
        }
    }

    expect($outputs)->toContain('created')
        ->and($outputs)->toContain('Conflito de horário')
        ->and($confirmed)->toBe(1);
});
