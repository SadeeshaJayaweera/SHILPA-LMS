<?php
declare(strict_types=1);

namespace App\Testing;

use App\Core\Response;
use RuntimeException;

class TestResponse {
    private Response $response;

    public function __construct(Response $response) {
        $this->response = $response;
    }

    public function getStatusCode(): int {
        return $this->response->getStatusCode();
    }

    public function assertStatus(int $expectedStatus): self {
        $actual = $this->response->getStatusCode();
        if ($actual !== $expectedStatus) {
            throw new RuntimeException("Expected status code $expectedStatus, got $actual. Response: " . substr($this->response->getContent(), 0, 300));
        }
        return $this;
    }

    public function assertOk(): self {
        return $this->assertStatus(200);
    }

    public function assertRedirect(?string $expectedUrl = null): self {
        $actual = $this->response->getStatusCode();
        if ($actual !== 301 && $actual !== 302) {
            throw new RuntimeException("Expected redirect status (301/302), got $actual. Content: " . substr($this->response->getContent(), 0, 200));
        }
        if ($expectedUrl !== null) {
            $location = $this->response->getHeader('Location');
            if ($location !== $expectedUrl) {
                throw new RuntimeException("Expected redirect to [$expectedUrl], got [$location].");
            }
        }
        return $this;
    }

    public function assertNotFound(): self {
        return $this->assertStatus(404);
    }

    public function assertSee(string $value): self {
        $content = $this->response->getContent();
        if (!str_contains($content, $value)) {
            throw new RuntimeException("Failed asserting that response contains [$value].");
        }
        return $this;
    }

    public function assertDontSee(string $value): self {
        $content = $this->response->getContent();
        if (str_contains($content, $value)) {
            throw new RuntimeException("Failed asserting that response does not contain [$value].");
        }
        return $this;
    }

    public function assertJson(array $expected): self {
        $data = json_decode($this->response->getContent(), true);
        if (!is_array($data)) {
            throw new RuntimeException("Response is not valid JSON: " . $this->response->getContent());
        }

        foreach ($expected as $key => $val) {
            if (!array_key_exists($key, $data) || $data[$key] !== $val) {
                $actual = json_encode($data[$key] ?? null);
                $exp = json_encode($val);
                throw new RuntimeException("Failed asserting JSON key [$key] equals $exp. Actual: $actual");
            }
        }
        return $this;
    }

    public function getContent(): string {
        return $this->response->getContent();
    }

    public function json(?string $key = null): mixed {
        $data = json_decode($this->response->getContent(), true);
        if ($key === null) {
            return $data;
        }
        return $data[$key] ?? null;
    }
}
