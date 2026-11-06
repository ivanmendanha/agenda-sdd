<?php

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
