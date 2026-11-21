<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Testing\TestCase;

class HealthcheckTest extends TestCase {
    public function testHealthEndpointReturnsOk(): void {
        $response = $this->get('/health');
        $response->assertOk()
                 ->assertJson(['status' => 'healthy', 'app' => 'SHILPA LMS']);
    }

    public function testApiGatewayPing(): void {
        $response = $this->get('/api/v1/ping');
        $response->assertOk()
                 ->assertJson(['success' => true, 'version' => '1.0.0']);
    }

    public function testUnknownEndpointReturns404(): void {
        $response = $this->get('/unknown-system-path-404');
        $response->assertNotFound()
                 ->assertSee('404');
    }
}
