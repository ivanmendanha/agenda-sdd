<?php

use App\Models\Reservation;
use App\Models\Room;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('stores one confirmed reservation', function () {
    $this->travelTo('2026-03-10 12:00:00');
    $room = Room::factory()->create();

    $response = $this->postJson('/api/reservations', [
        'room_id' => $room->id,
        'date' => '2026-03-10',
        'start' => '09:00',
        'end' => '11:00',
        'subject' => 'Planejamento',
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.room_id', $room->id)
        ->assertJsonPath('data.date', '2026-03-10')
        ->assertJsonPath('data.starts_at', '09:00')
        ->assertJsonPath('data.ends_at', '11:00')
        ->assertJsonPath('data.subject', 'Planejamento')
        ->assertJsonPath('data.status', 'confirmed');

    $this->assertDatabaseCount('reservations', 1);
    $this->assertDatabaseHas('reservations', [
        'room_id' => $room->id,
        'starts_at' => '09:00',
        'ends_at' => '11:00',
        'subject' => 'Planejamento',
        'status' => 'confirmed',
    ]);
});

it('rejects an empty subject', function () {
    $this->travelTo('2026-03-10 12:00:00');
    $room = Room::factory()->create();

    $response = $this->postJson('/api/reservations', [
        'room_id' => $room->id,
        'date' => '2026-03-10',
        'start' => '09:00',
        'end' => '11:00',
        'subject' => '   ',
    ]);

    $response->assertUnprocessable()
        ->assertJsonPath('message', 'Informe o assunto');

    $this->assertDatabaseCount('reservations', 0);
});

it('rejects a subject longer than 200 characters', function () {
    $this->travelTo('2026-03-10 12:00:00');
    $room = Room::factory()->create();

    $response = $this->postJson('/api/reservations', [
        'room_id' => $room->id,
        'date' => '2026-03-10',
        'start' => '09:00',
        'end' => '11:00',
        'subject' => '  '.str_repeat('a', 201).'  ',
    ]);

    $response->assertUnprocessable()
        ->assertJsonPath('message', 'O assunto deve ter no máximo 200 caracteres');

    $this->assertDatabaseCount('reservations', 0);
});

it('rejects a reservation for an inactive room', function () {
    $this->travelTo('2026-03-10 12:00:00');
    $room = Room::factory()->inactive()->create();

    $response = $this->postJson('/api/reservations', [
        'room_id' => $room->id,
        'date' => '2026-03-10',
        'start' => '09:00',
        'end' => '11:00',
        'subject' => 'Planejamento',
    ]);

    $response->assertUnprocessable()
        ->assertJsonPath('message', 'Sala inativa não permite nova reserva');

    $this->assertDatabaseCount('reservations', 0);
});

it('rejects an overlapping reservation', function () {
    $this->travelTo('2026-03-10 12:00:00');
    $room = Room::factory()->create();
    Reservation::factory()->for($room)->create([
        'date' => '2026-03-10',
        'starts_at' => '09:00',
        'ends_at' => '10:00',
    ]);

    $response = $this->postJson('/api/reservations', [
        'room_id' => $room->id,
        'date' => '2026-03-10',
        'start' => '09:30',
        'end' => '10:30',
        'subject' => 'Planejamento',
    ]);

    $response->assertStatus(409)
        ->assertExactJson(['message' => 'Conflito de horário']);

    $this->assertDatabaseCount('reservations', 1);
});

it('rejects a reservation whose end is not after its start', function () {
    $this->travelTo('2026-03-10 12:00:00');
    $room = Room::factory()->create();

    $response = $this->postJson('/api/reservations', [
        'room_id' => $room->id,
        'date' => '2026-03-10',
        'start' => '10:00',
        'end' => '10:00',
        'subject' => 'Planejamento',
    ]);

    $response->assertUnprocessable()
        ->assertJsonPath('message', 'Horário de fim deve ser posterior ao horário de início');

    $this->assertDatabaseCount('reservations', 0);
});

it('rejects a reservation before today', function () {
    $this->travelTo('2026-03-10 12:00:00');
    $room = Room::factory()->create();

    $response = $this->postJson('/api/reservations', [
        'room_id' => $room->id,
        'date' => '2026-03-09',
        'start' => '09:00',
        'end' => '10:00',
        'subject' => 'Planejamento',
    ]);

    $response->assertUnprocessable()
        ->assertJsonPath('message', 'A data não pode ser anterior a hoje');

    $this->assertDatabaseCount('reservations', 0);
});

it('rejects a reservation outside business hours', function () {
    $this->travelTo('2026-03-10 12:00:00');
    $room = Room::factory()->create();

    $response = $this->postJson('/api/reservations', [
        'room_id' => $room->id,
        'date' => '2026-03-10',
        'start' => '07:00',
        'end' => '09:00',
        'subject' => 'Planejamento',
    ]);

    $response->assertUnprocessable()
        ->assertJsonPath('message', 'Reservas permitidas apenas entre 8h e 18h');

    $this->assertDatabaseCount('reservations', 0);
});

it('rejects a reservation over 4 hours', function () {
    $this->travelTo('2026-03-10 12:00:00');
    $room = Room::factory()->create();

    $response = $this->postJson('/api/reservations', [
        'room_id' => $room->id,
        'date' => '2026-03-10',
        'start' => '09:00',
        'end' => '13:01',
        'subject' => 'Planejamento',
    ]);

    $response->assertUnprocessable()
        ->assertJsonPath('message', 'Duração máxima permitida é de 4 horas');

    $this->assertDatabaseCount('reservations', 0);
});

it('uses the first failing check on create', function () {
    $this->travelTo('2026-03-10 12:00:00');
    $room = Room::factory()->create();

    $this->postJson('/api/reservations', [
        'room_id' => $room->id,
        'date' => '2026-03-09',
        'start' => '19:00',
        'end' => '07:00',
        'subject' => 'Planejamento',
    ])->assertUnprocessable()
        ->assertJsonPath('message', 'Horário de fim deve ser posterior ao horário de início');

    $this->postJson('/api/reservations', [
        'room_id' => $room->id,
        'date' => '2026-03-09',
        'start' => '07:00',
        'end' => '19:00',
        'subject' => 'Planejamento',
    ])->assertUnprocessable()
        ->assertJsonPath('message', 'A data não pode ser anterior a hoje');

    $this->postJson('/api/reservations', [
        'room_id' => $room->id,
        'date' => '2026-03-10',
        'start' => '07:00',
        'end' => '19:00',
        'subject' => 'Planejamento',
    ])->assertUnprocessable()
        ->assertJsonPath('message', 'Reservas permitidas apenas entre 8h e 18h');

    $this->assertDatabaseCount('reservations', 0);
});

it('rechecks the conflict at create time', function () {
    $this->travelTo('2026-03-10 12:00:00');
    $room = Room::factory()->create();

    $this->getJson('/api/rooms?date=2026-03-10&start=09:00&end=10:00')
        ->assertOk()
        ->assertJsonPath('data.0.id', $room->id)
        ->assertJsonPath('data.0.availability', 'disponivel');

    Reservation::factory()->for($room)->create([
        'date' => '2026-03-10',
        'starts_at' => '09:30',
        'ends_at' => '10:30',
    ]);

    $response = $this->postJson('/api/reservations', [
        'room_id' => $room->id,
        'date' => '2026-03-10',
        'start' => '09:00',
        'end' => '10:00',
        'subject' => 'Planejamento',
    ]);

    $response->assertStatus(409)
        ->assertExactJson(['message' => 'Conflito de horário']);

    $this->assertDatabaseCount('reservations', 1);
});

it('confirms exactly one of two concurrent creates', function () {
    $database = sys_get_temp_dir().'/salas-reservation-api-'.bin2hex(random_bytes(6)).'.sqlite';
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
        base_path('tests/Fixtures/post_reservation.php'),
        (string) $roomId,
        now()->addDay()->toDateString(),
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

    $bodies = [];

    foreach ($outputs as $output) {
        [$status, $body] = array_pad(explode(' ', $output, 2), 2, '');

        $bodies[(int) $status] = json_decode(trim($body), true);
    }

    ksort($bodies);

    expect(array_keys($bodies))->toBe([201, 409])
        ->and($bodies[409])->toBe(['message' => 'Conflito de horário'])
        ->and($bodies[201]['data']['status'])->toBe('confirmed')
        ->and($confirmed)->toBe(1);
});

it('stores two reservations that only touch', function () {
    $this->travelTo('2026-03-10 12:00:00');
    $room = Room::factory()->create();

    $this->postJson('/api/reservations', [
        'room_id' => $room->id,
        'date' => '2026-03-10',
        'start' => '09:00',
        'end' => '10:00',
        'subject' => 'Planejamento',
    ])->assertCreated();

    $this->postJson('/api/reservations', [
        'room_id' => $room->id,
        'date' => '2026-03-10',
        'start' => '10:00',
        'end' => '11:00',
        'subject' => 'Retrospectiva',
    ])->assertCreated();

    expect(Reservation::query()->where('status', 'confirmed')->count())->toBe(2);

    $this->assertDatabaseHas('reservations', [
        'room_id' => $room->id,
        'starts_at' => '09:00',
        'ends_at' => '10:00',
        'status' => 'confirmed',
    ]);
    $this->assertDatabaseHas('reservations', [
        'room_id' => $room->id,
        'starts_at' => '10:00',
        'ends_at' => '11:00',
        'status' => 'confirmed',
    ]);
});

it('ignores an unconfirmed reservation', function () {
    $this->travelTo('2026-03-10 12:00:00');
    $room = Room::factory()->create();
    Reservation::factory()->for($room)->unconfirmed()->create([
        'date' => '2026-03-10',
        'starts_at' => '09:00',
        'ends_at' => '10:00',
    ]);

    $this->getJson('/api/rooms?date=2026-03-10&start=09:00&end=10:00')
        ->assertOk()
        ->assertJsonPath('data.0.id', $room->id)
        ->assertJsonPath('data.0.availability', 'disponivel');

    $this->postJson('/api/reservations', [
        'room_id' => $room->id,
        'date' => '2026-03-10',
        'start' => '09:30',
        'end' => '10:30',
        'subject' => 'Planejamento',
    ])->assertCreated();

    $this->assertDatabaseHas('reservations', [
        'room_id' => $room->id,
        'starts_at' => '09:30',
        'ends_at' => '10:30',
        'status' => 'confirmed',
    ]);
});

it('serves salas actions with no credentials', function () {
    $this->travelTo('2026-03-10 12:00:00');
    $room = Room::factory()->create();

    $responses = [
        'list' => $this->getJson('/api/rooms'),
        'show' => $this->getJson("/api/rooms/{$room->id}"),
        'create' => $this->postJson('/api/rooms', ['name' => 'Sala Alfa', 'capacity' => 12]),
        'update' => $this->putJson("/api/rooms/{$room->id}", ['name' => 'Sala Beta', 'capacity' => 20]),
        'inactivate' => $this->postJson("/api/rooms/{$room->id}/inactivate"),
        'activate' => $this->postJson("/api/rooms/{$room->id}/activate"),
        'availability' => $this->getJson('/api/rooms?date=2026-03-10&start=09:00&end=10:00'),
        'reservation' => $this->postJson('/api/reservations', [
            'room_id' => $room->id,
            'date' => '2026-03-10',
            'start' => '09:00',
            'end' => '10:00',
            'subject' => 'Planejamento',
        ]),
    ];

    foreach ($responses as $response) {
        expect($response->status())->not->toBeIn([401, 403]);
    }

    expect($responses['list']->status())->toBe(200)
        ->and($responses['show']->status())->toBe(200)
        ->and($responses['create']->status())->toBe(201)
        ->and($responses['update']->status())->toBe(200)
        ->and($responses['activate']->status())->toBe(200)
        ->and($responses['inactivate']->status())->toBe(200)
        ->and($responses['availability']->status())->toBe(200)
        ->and($responses['reservation']->status())->toBe(201);
});
