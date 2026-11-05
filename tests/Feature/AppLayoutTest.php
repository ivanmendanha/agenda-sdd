<?php

test('spa shell serves dashboard route', function () {
    $response = $this->get('/');

    $response->assertOk()
        ->assertSee('id="app"', false);
});

test('spa shell serves salas route', function () {
    $response = $this->get('/salas');

    $response->assertOk()
        ->assertSee('id="app"', false);
});

test('spa shell serves minhas reservas route', function () {
    $response = $this->get('/minhas-reservas');

    $response->assertOk()
        ->assertSee('id="app"', false);
});

test('spa shell serves unknown path', function () {
    $response = $this->get('/caminho-inexistente');

    $response->assertOk()
        ->assertSee('id="app"', false);
});

test('api health still returns json with spa fallback', function () {
    $response = $this->getJson('/api/health');

    $response->assertOk()
        ->assertExactJson(['status' => 'ok']);
});
