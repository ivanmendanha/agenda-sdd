<?php

namespace Tests\Feature;

use Tests\TestCase;

class AppLayoutTest extends TestCase
{
    public function test_spa_shell_serves_dashboard_route(): void
    {
        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('id="app"', false);
    }

    public function test_spa_shell_serves_salas_route(): void
    {
        $response = $this->get('/salas');

        $response->assertOk()
            ->assertSee('id="app"', false);
    }

    public function test_spa_shell_serves_minhas_reservas_route(): void
    {
        $response = $this->get('/minhas-reservas');

        $response->assertOk()
            ->assertSee('id="app"', false);
    }

    public function test_spa_shell_serves_unknown_path(): void
    {
        $response = $this->get('/caminho-inexistente');

        $response->assertOk()
            ->assertSee('id="app"', false);
    }

    public function test_api_health_still_returns_json_with_spa_fallback(): void
    {
        $response = $this->getJson('/api/health');

        $response->assertOk()
            ->assertExactJson(['status' => 'ok']);
    }
}
