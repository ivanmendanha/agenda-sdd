<?php

test('health endpoint returns ok', function () {
    $response = $this->getJson('/api/health');

    $response->assertOk()
        ->assertExactJson(['status' => 'ok']);
});

test('health allows unauthenticated access', function () {
    $response = $this->getJson('/api/health');

    $response->assertOk()
        ->assertJsonPath('status', 'ok');
});

test('unknown api path returns json', function () {
    $response = $this->getJson('/api/does-not-exist');

    $response->assertNotFound()
        ->assertHeader('content-type', 'application/json');
});

test('root serves react shell', function () {
    $response = $this->get('/');

    $response->assertOk()
        ->assertSee('id="app"', false)
        ->assertSee('type="module"', false)
        ->assertDontSee("Let's get started");
});
