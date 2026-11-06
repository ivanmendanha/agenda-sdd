<?php

use App\Models\Reservation;
use App\Models\Room;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates a room', function () {
    $response = $this->postJson('/api/rooms', [
        'name' => 'Sala Alfa',
        'capacity' => 12,
        'resources' => ['Projetor', 'Quadro'],
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.name', 'Sala Alfa')
        ->assertJsonPath('data.capacity', 12)
        ->assertJsonPath('data.resources', ['Projetor', 'Quadro'])
        ->assertJsonPath('data.active', true)
        ->assertJsonPath('data.availability', null);

    $this->assertDatabaseHas('rooms', [
        'name' => 'Sala Alfa',
        'capacity' => 12,
        'active' => true,
    ]);
    $this->assertDatabaseCount('room_resources', 2);
});

it('rejects an empty room name', function () {
    $response = $this->postJson('/api/rooms', [
        'name' => '   ',
        'capacity' => 12,
    ]);

    $response->assertUnprocessable()
        ->assertJsonPath('message', 'Informe o nome da sala');

    $this->assertDatabaseCount('rooms', 0);
});

it('rejects a room name over 100 characters', function () {
    $response = $this->postJson('/api/rooms', [
        'name' => str_repeat('a', 101),
        'capacity' => 12,
    ]);

    $response->assertUnprocessable()
        ->assertJsonPath('message', 'O nome da sala deve ter no máximo 100 caracteres');

    $this->assertDatabaseCount('rooms', 0);
});

it('rejects a capacity outside 1 to 999', function () {
    foreach ([0, 1000, 'dez'] as $capacity) {
        $this->postJson('/api/rooms', ['name' => 'Sala Alfa', 'capacity' => $capacity])
            ->assertUnprocessable()
            ->assertJsonPath('message', 'A capacidade deve ser um número inteiro de 1 a 999');
    }

    $this->assertDatabaseCount('rooms', 0);
});

it('stores a room without resources', function () {
    $response = $this->postJson('/api/rooms', [
        'name' => 'Sala Alfa',
        'capacity' => 12,
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.resources', []);

    $this->assertDatabaseCount('room_resources', 0);
});

it('stores one tag for a repeated resource', function () {
    $response = $this->postJson('/api/rooms', [
        'name' => 'Sala Alfa',
        'capacity' => 12,
        'resources' => ['Projetor', '  projetor  ', 'PROJETOR'],
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.resources', ['Projetor']);

    $this->assertDatabaseCount('room_resources', 1);
    $this->assertDatabaseHas('room_resources', [
        'name' => 'Projetor',
        'name_normalized' => 'projetor',
    ]);
});

it('allows two rooms to share a name', function () {
    $this->postJson('/api/rooms', ['name' => 'Sala Alfa', 'capacity' => 12])->assertCreated();

    $response = $this->postJson('/api/rooms', ['name' => 'Sala Alfa', 'capacity' => 30]);

    $response->assertCreated();

    $this->assertDatabaseCount('rooms', 2);
});

it('does not expose a room delete route', function () {
    $room = Room::factory()->create();

    $response = $this->deleteJson("/api/rooms/{$room->id}");

    expect($response->status())->toBeIn([404, 405]);
    $this->assertModelExists($room);
});

it('lists rooms over HTTP', function () {
    $active = Room::factory()->create(['name' => 'Sala Ativa', 'capacity' => 12]);
    $active->resources()->create(['name' => 'Projetor', 'name_normalized' => 'projetor']);
    $inactive = Room::factory()->inactive()->create(['name' => 'Sala Inativa']);

    $response = $this->getJson('/api/rooms');

    $response->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonPath('data.0.id', $active->id)
        ->assertJsonPath('data.0.name', 'Sala Ativa')
        ->assertJsonPath('data.0.capacity', 12)
        ->assertJsonPath('data.0.resources', ['Projetor'])
        ->assertJsonPath('data.0.active', true)
        ->assertJsonPath('data.0.availability', null)
        ->assertJsonPath('data.1.id', $inactive->id)
        ->assertJsonPath('data.1.active', false);
});

it('filters rooms by minimum capacity', function () {
    Room::factory()->create(['capacity' => 9]);
    $exact = Room::factory()->create(['capacity' => 10]);
    $large = Room::factory()->create(['capacity' => 11]);

    $response = $this->getJson('/api/rooms?min_capacity=10');

    $response->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonPath('data.0.id', $exact->id)
        ->assertJsonPath('data.1.id', $large->id);
});

it('filters rooms by resource ignoring case', function () {
    $withProjector = Room::factory()->create();
    $withProjector->resources()->create(['name' => 'Projetor', 'name_normalized' => 'projetor']);
    $withBoard = Room::factory()->create();
    $withBoard->resources()->create(['name' => 'Quadro', 'name_normalized' => 'quadro']);

    $response = $this->getJson('/api/rooms?resource='.urlencode(' PROJETOR '));

    $response->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $withProjector->id);
});

it('applies capacity and resource together', function () {
    $match = Room::factory()->create(['capacity' => 20]);
    $match->resources()->create(['name' => 'Projetor', 'name_normalized' => 'projetor']);
    $tooSmall = Room::factory()->create(['capacity' => 4]);
    $tooSmall->resources()->create(['name' => 'Projetor', 'name_normalized' => 'projetor']);
    $wrongResource = Room::factory()->create(['capacity' => 20]);
    $wrongResource->resources()->create(['name' => 'Quadro', 'name_normalized' => 'quadro']);

    $response = $this->getJson('/api/rooms?min_capacity=10&resource=projetor');

    $response->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $match->id);
});

it('returns every room when filters are blank', function () {
    $first = Room::factory()->create(['capacity' => 2]);
    $second = Room::factory()->inactive()->create(['capacity' => 50]);

    $response = $this->getJson('/api/rooms?min_capacity=&resource=');

    $response->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonPath('data.0.id', $first->id)
        ->assertJsonPath('data.1.id', $second->id);
});

it('rejects an invalid minimum capacity', function () {
    Room::factory()->create(['capacity' => 10]);

    $response = $this->getJson('/api/rooms?min_capacity=0');

    $response->assertUnprocessable()
        ->assertJsonPath('message', 'A capacidade deve ser um número inteiro de 1 a 999');

    expect($response->content())->not->toContain('disponivel');
});

it('marks an overlapping active room indisponivel', function () {
    $this->travelTo('2026-03-10 12:00:00');
    $room = Room::factory()->create();
    Reservation::factory()->for($room)->create([
        'date' => '2026-03-10',
        'starts_at' => '09:30',
        'ends_at' => '10:30',
    ]);

    $response = $this->getJson('/api/rooms?date=2026-03-10&start=09:00&end=10:00');

    $response->assertOk()
        ->assertJsonPath('data.0.id', $room->id)
        ->assertJsonPath('data.0.availability', 'indisponivel');
});

it('marks a free active room disponivel', function () {
    $this->travelTo('2026-03-10 12:00:00');
    $room = Room::factory()->create();

    $response = $this->getJson('/api/rooms?date=2026-03-10&start=09:00&end=10:00');

    $response->assertOk()
        ->assertJsonPath('data.0.id', $room->id)
        ->assertJsonPath('data.0.availability', 'disponivel');
});

it('leaves an inactive room without availability', function () {
    $this->travelTo('2026-03-10 12:00:00');
    $room = Room::factory()->inactive()->create();

    $response = $this->getJson('/api/rooms?date=2026-03-10&start=09:00&end=10:00');

    $response->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $room->id)
        ->assertJsonPath('data.0.active', false)
        ->assertJsonPath('data.0.availability', null);
});

it('treats a touching reservation as free', function () {
    $this->travelTo('2026-03-10 12:00:00');
    $room = Room::factory()->create();
    Reservation::factory()->for($room)->create([
        'date' => '2026-03-10',
        'starts_at' => '08:00',
        'ends_at' => '09:00',
    ]);
    Reservation::factory()->for($room)->create([
        'date' => '2026-03-10',
        'starts_at' => '10:00',
        'ends_at' => '11:00',
    ]);

    $response = $this->getJson('/api/rooms?date=2026-03-10&start=09:00&end=10:00');

    $response->assertOk()
        ->assertJsonPath('data.0.id', $room->id)
        ->assertJsonPath('data.0.availability', 'disponivel');
});

it('ignores a reservation on another room', function () {
    $this->travelTo('2026-03-10 12:00:00');
    $room = Room::factory()->create();
    $other = Room::factory()->create();
    Reservation::factory()->for($other)->create([
        'date' => '2026-03-10',
        'starts_at' => '09:30',
        'ends_at' => '10:30',
    ]);

    $response = $this->getJson('/api/rooms?date=2026-03-10&start=09:00&end=10:00');

    $response->assertOk()
        ->assertJsonPath('data.0.id', $room->id)
        ->assertJsonPath('data.0.availability', 'disponivel')
        ->assertJsonPath('data.1.id', $other->id)
        ->assertJsonPath('data.1.availability', 'indisponivel');
});

it('ignores a reservation on another date', function () {
    $this->travelTo('2026-03-10 12:00:00');
    $room = Room::factory()->create();
    Reservation::factory()->for($room)->create([
        'date' => '2026-03-11',
        'starts_at' => '09:30',
        'ends_at' => '10:30',
    ]);

    $response = $this->getJson('/api/rooms?date=2026-03-10&start=09:00&end=10:00');

    $response->assertOk()
        ->assertJsonPath('data.0.id', $room->id)
        ->assertJsonPath('data.0.availability', 'disponivel');
});

it('rejects an availability query outside business hours', function () {
    $this->travelTo('2026-03-10 12:00:00');
    Room::factory()->create();

    $response = $this->getJson('/api/rooms?date=2026-03-10&start=07:00&end=09:00');

    $response->assertUnprocessable()
        ->assertJsonPath('message', 'Reservas permitidas apenas entre 8h e 18h');

    expect($response->content())->not->toContain('disponivel');
});

it('rejects an availability query over 4 hours', function () {
    $this->travelTo('2026-03-10 12:00:00');
    Room::factory()->create();

    $response = $this->getJson('/api/rooms?date=2026-03-10&start=09:00&end=13:01');

    $response->assertUnprocessable()
        ->assertJsonPath('message', 'Duração máxima permitida é de 4 horas');

    expect($response->content())->not->toContain('disponivel');
});

it('rejects an availability query whose end is not after its start', function () {
    $this->travelTo('2026-03-10 12:00:00');
    Room::factory()->create();

    $response = $this->getJson('/api/rooms?date=2026-03-10&start=10:00&end=10:00');

    $response->assertUnprocessable()
        ->assertJsonPath('message', 'Horário de fim deve ser posterior ao horário de início');

    expect($response->content())->not->toContain('disponivel');
});

it('rejects an availability query before today', function () {
    $this->travelTo('2026-03-10 12:00:00');
    Room::factory()->create();

    $response = $this->getJson('/api/rooms?date=2026-03-09&start=09:00&end=10:00');

    $response->assertUnprocessable()
        ->assertJsonPath('message', 'A data não pode ser anterior a hoje');

    expect($response->content())->not->toContain('disponivel');
});

it('reports only the first failing availability check', function () {
    $this->travelTo('2026-03-10 12:00:00');
    Room::factory()->create();

    $this->getJson('/api/rooms?date=2026-03-09&start=19:00&end=07:00')
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Horário de fim deve ser posterior ao horário de início');

    $this->getJson('/api/rooms?date=2026-03-09&start=07:00&end=19:00')
        ->assertUnprocessable()
        ->assertJsonPath('message', 'A data não pode ser anterior a hoje');

    $this->getJson('/api/rooms?date=2026-03-10&start=07:00&end=19:00')
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Reservas permitidas apenas entre 8h e 18h');
});

it('accepts a duration of exactly 4 hours', function () {
    $this->travelTo('2026-03-10 12:00:00');
    $room = Room::factory()->create();

    $response = $this->getJson('/api/rooms?date=2026-03-10&start=09:00&end=13:00');

    $response->assertOk()
        ->assertJsonPath('data.0.id', $room->id)
        ->assertJsonPath('data.0.availability', 'disponivel');
});

it('accepts a start of 08:00', function () {
    $this->travelTo('2026-03-10 12:00:00');
    $room = Room::factory()->create();

    $response = $this->getJson('/api/rooms?date=2026-03-10&start=08:00&end=12:00');

    $response->assertOk()
        ->assertJsonPath('data.0.id', $room->id)
        ->assertJsonPath('data.0.availability', 'disponivel');
});

it('accepts an end of 18:00', function () {
    $this->travelTo('2026-03-10 12:00:00');
    $room = Room::factory()->create();

    $response = $this->getJson('/api/rooms?date=2026-03-10&start=14:00&end=18:00');

    $response->assertOk()
        ->assertJsonPath('data.0.id', $room->id)
        ->assertJsonPath('data.0.availability', 'disponivel');
});

it('shows one room', function () {
    $room = Room::factory()->create(['name' => 'Sala Alfa', 'capacity' => 12]);
    $room->resources()->create(['name' => 'Projetor', 'name_normalized' => 'projetor']);

    $response = $this->getJson("/api/rooms/{$room->id}");

    $response->assertOk()
        ->assertJsonPath('data.id', $room->id)
        ->assertJsonPath('data.name', 'Sala Alfa')
        ->assertJsonPath('data.capacity', 12)
        ->assertJsonPath('data.resources', ['Projetor'])
        ->assertJsonPath('data.active', true)
        ->assertJsonPath('data.availability', null);
});

it('returns 404 when the room does not exist', function () {
    $response = $this->getJson('/api/rooms/404');

    $response->assertNotFound()
        ->assertExactJson(['message' => 'Sala não encontrada']);
});

it('updates a room', function () {
    $room = Room::factory()->create(['name' => 'Sala Alfa', 'capacity' => 10]);
    $room->resources()->create(['name' => 'Quadro', 'name_normalized' => 'quadro']);

    $response = $this->putJson("/api/rooms/{$room->id}", [
        'name' => 'Sala Beta',
        'capacity' => 30,
        'resources' => ['Projetor', 'PROJETOR'],
        'active' => false,
    ]);

    $response->assertOk()
        ->assertJsonPath('data.id', $room->id)
        ->assertJsonPath('data.name', 'Sala Beta')
        ->assertJsonPath('data.capacity', 30)
        ->assertJsonPath('data.resources', ['Projetor'])
        ->assertJsonPath('data.active', false);

    $this->assertDatabaseHas('rooms', [
        'id' => $room->id,
        'name' => 'Sala Beta',
        'capacity' => 30,
        'active' => false,
    ]);
    $this->assertDatabaseCount('room_resources', 1);
    $this->assertDatabaseHas('room_resources', [
        'room_id' => $room->id,
        'name' => 'Projetor',
        'name_normalized' => 'projetor',
    ]);
});

it('activates a room', function () {
    $room = Room::factory()->inactive()->create();

    $response = $this->postJson("/api/rooms/{$room->id}/activate");

    $response->assertOk()
        ->assertJsonPath('data.id', $room->id)
        ->assertJsonPath('data.active', true);

    $this->assertDatabaseHas('rooms', ['id' => $room->id, 'active' => true]);
});

it('inactivates a room', function () {
    $room = Room::factory()->create();

    $response = $this->postJson("/api/rooms/{$room->id}/inactivate");

    $response->assertOk()
        ->assertJsonPath('data.id', $room->id)
        ->assertJsonPath('data.active', false);

    $this->assertDatabaseHas('rooms', ['id' => $room->id, 'active' => false]);
});

it('keeps confirmed reservations when a room is inactivated', function () {
    $room = Room::factory()->create();
    $reservation = Reservation::factory()->for($room)->create();

    $this->postJson("/api/rooms/{$room->id}/inactivate")->assertOk();

    $this->assertDatabaseHas('reservations', [
        'id' => $reservation->id,
        'room_id' => $room->id,
        'status' => 'confirmed',
    ]);
});

it('keeps the room row when a room is inactivated', function () {
    $room = Room::factory()->create();

    $this->postJson("/api/rooms/{$room->id}/inactivate")->assertOk();

    $this->assertModelExists($room);
});
