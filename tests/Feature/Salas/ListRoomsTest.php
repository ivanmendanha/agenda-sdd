<?php

use App\Models\Reservation;
use App\Models\Room;
use App\Rooms\ListRooms;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('query returns every stored room', function () {
    $active = Room::factory()->create(['name' => 'Sala Ativa']);
    $inactive = Room::factory()->inactive()->create(['name' => 'Sala Inativa']);

    $rooms = app(ListRooms::class)->handle([]);

    expect($rooms->pluck('id')->all())->toBe([$active->id, $inactive->id]);
});

test('query filters by minimum capacity', function () {
    $small = Room::factory()->create(['capacity' => 9]);
    $exact = Room::factory()->create(['capacity' => 10]);
    $large = Room::factory()->create(['capacity' => 11]);

    $rooms = app(ListRooms::class)->handle(['min_capacity' => 10]);

    expect($rooms->pluck('id')->all())->toBe([$exact->id, $large->id])
        ->and($rooms->pluck('id')->all())->not->toContain($small->id);
});

test('query filters by resource ignoring case', function () {
    $withProjector = Room::factory()->create();
    $withProjector->resources()->create(['name' => 'Projetor', 'name_normalized' => 'projetor']);
    $withBoard = Room::factory()->create();
    $withBoard->resources()->create(['name' => 'Quadro', 'name_normalized' => 'quadro']);

    $rooms = app(ListRooms::class)->handle(['resource' => '  PROJETOR  ']);

    expect($rooms->pluck('id')->all())->toBe([$withProjector->id]);
});

test('query applies capacity and resource together', function () {
    $match = Room::factory()->create(['capacity' => 20]);
    $match->resources()->create(['name' => 'Projetor', 'name_normalized' => 'projetor']);
    $tooSmall = Room::factory()->create(['capacity' => 4]);
    $tooSmall->resources()->create(['name' => 'Projetor', 'name_normalized' => 'projetor']);
    $wrongResource = Room::factory()->create(['capacity' => 20]);
    $wrongResource->resources()->create(['name' => 'Quadro', 'name_normalized' => 'quadro']);

    $rooms = app(ListRooms::class)->handle(['min_capacity' => 10, 'resource' => 'projetor']);

    expect($rooms->pluck('id')->all())->toBe([$match->id]);
});

test('query returns every room when filters are blank', function () {
    $first = Room::factory()->create(['capacity' => 2]);
    $second = Room::factory()->inactive()->create(['capacity' => 50]);

    $rooms = app(ListRooms::class)->handle(['min_capacity' => '', 'resource' => '']);

    expect($rooms->pluck('id')->all())->toBe([$first->id, $second->id]);
});

test('query marks an overlapping active room indisponivel', function () {
    $this->travelTo('2026-03-10 12:00:00');
    $room = Room::factory()->create();
    Reservation::factory()->for($room)->create([
        'date' => '2026-03-10',
        'starts_at' => '09:30',
        'ends_at' => '10:30',
    ]);

    $rooms = app(ListRooms::class)->handle([
        'date' => '2026-03-10',
        'start' => '09:00',
        'end' => '10:00',
    ]);

    expect($rooms->firstWhere('id', $room->id)->availability)->toBe('indisponivel');
});

test('query marks a free active room disponivel', function () {
    $this->travelTo('2026-03-10 12:00:00');
    $room = Room::factory()->create();

    $rooms = app(ListRooms::class)->handle([
        'date' => '2026-03-10',
        'start' => '09:00',
        'end' => '10:00',
    ]);

    expect($rooms->firstWhere('id', $room->id)->availability)->toBe('disponivel');
});

test('query leaves an inactive room without availability', function () {
    $this->travelTo('2026-03-10 12:00:00');
    $room = Room::factory()->inactive()->create();

    $rooms = app(ListRooms::class)->handle([
        'date' => '2026-03-10',
        'start' => '09:00',
        'end' => '10:00',
    ]);

    expect($rooms->pluck('id')->all())->toBe([$room->id])
        ->and($rooms->firstWhere('id', $room->id)->availability)->toBeNull();
});

test('query treats a touching reservation as free', function () {
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

    $rooms = app(ListRooms::class)->handle([
        'date' => '2026-03-10',
        'start' => '09:00',
        'end' => '10:00',
    ]);

    expect($rooms->firstWhere('id', $room->id)->availability)->toBe('disponivel');
});

test('query ignores another room', function () {
    $this->travelTo('2026-03-10 12:00:00');
    $room = Room::factory()->create();
    $other = Room::factory()->create();
    Reservation::factory()->for($other)->create([
        'date' => '2026-03-10',
        'starts_at' => '09:30',
        'ends_at' => '10:30',
    ]);

    $rooms = app(ListRooms::class)->handle([
        'date' => '2026-03-10',
        'start' => '09:00',
        'end' => '10:00',
    ]);

    expect($rooms->firstWhere('id', $room->id)->availability)->toBe('disponivel')
        ->and($rooms->firstWhere('id', $other->id)->availability)->toBe('indisponivel');
});

test('query ignores another date', function () {
    $this->travelTo('2026-03-10 12:00:00');
    $room = Room::factory()->create();
    Reservation::factory()->for($room)->create([
        'date' => '2026-03-11',
        'starts_at' => '09:30',
        'ends_at' => '10:30',
    ]);

    $rooms = app(ListRooms::class)->handle([
        'date' => '2026-03-10',
        'start' => '09:00',
        'end' => '10:00',
    ]);

    expect($rooms->firstWhere('id', $room->id)->availability)->toBe('disponivel');
});
