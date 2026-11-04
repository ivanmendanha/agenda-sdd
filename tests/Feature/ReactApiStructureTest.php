<?php

namespace Tests\Feature;

use Tests\TestCase;

class ReactApiStructureTest extends TestCase
{
    public function test_health_endpoint_returns_ok(): void
    {
        $response = $this->getJson('/api/health');

        $response->assertOk()
            ->assertExactJson(['status' => 'ok']);
    }

    public function test_health_allows_unauthenticated_access(): void
    {
        $response = $this->getJson('/api/health');

        $response->assertOk()
            ->assertJsonPath('status', 'ok');
    }

    public function test_unknown_api_path_returns_json(): void
    {
        $response = $this->getJson('/api/does-not-exist');

        $response->assertNotFound()
            ->assertHeader('content-type', 'application/json');
    }
}
