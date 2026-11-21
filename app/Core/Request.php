<?php
declare(strict_types=1);

namespace App\Core;

class Request {
    private string $method;
    private string $uri;
    private string $path;
    private array $query;
    private array $post;
    private array $cookies;
    private array $server;
    private array $headers;
    private ?array $json = null;

    public function __construct(
        ?string $method = null,
        ?string $uri = null,
        ?array $query = null,
        ?array $post = null,
        ?array $cookies = null,
        ?array $server = null
    ) {
        $this->server = $server ?? $_SERVER;
        $this->method = strtoupper($method ?? ($this->server['REQUEST_METHOD'] ?? 'GET'));
        $this->uri = $uri ?? ($this->server['REQUEST_URI'] ?? '/');
        $this->path = parse_url($this->uri, PHP_URL_PATH) ?: '/';
        $this->query = $query ?? $_GET;
        $this->post = $post ?? $_POST;
        $this->cookies = $cookies ?? $_COOKIE;
        $this->headers = $this->parseHeaders();
    }

    public static function capture(): self {
        return new self();
    }

    public function method(): string {
        return $this->method;
    }

    public function isMethod(string $method): bool {
        return strtoupper($this->method) === strtoupper($method);
    }

    public function uri(): string {
        return $this->uri;
    }

    public function path(): string {
        return $this->path;
    }

    public function query(?string $key = null, mixed $default = null): mixed {
        if ($key === null) {
            return $this->query;
        }
        return $this->query[$key] ?? $default;
    }

    public function get(?string $key = null, mixed $default = null): mixed {
        return $this->query($key, $default);
    }

    public function post(?string $key = null, mixed $default = null): mixed {
        if ($key === null) {
            return $this->post;
        }
        return $this->post[$key] ?? $default;
    }

    public function input(?string $key = null, mixed $default = null): mixed {
        $all = $this->all();
        if ($key === null) {
            return $all;
        }
        return $all[$key] ?? $default;
    }

    public function all(): array {
        return array_merge($this->query, $this->post, $this->json());
    }

    public function json(?string $key = null, mixed $default = null): mixed {
        if ($this->json === null) {
            $input = file_get_contents('php://input');
            $decoded = json_decode((string)$input, true);
            $this->json = is_array($decoded) ? $decoded : [];
        }

        if ($key === null) {
            return $this->json;
        }

        return $this->json[$key] ?? $default;
    }

    public function header(string $key, ?string $default = null): ?string {
        $key = strtolower(str_replace('_', '-', $key));
        return $this->headers[$key] ?? $default;
    }

    public function bearerToken(): ?string {
        $header = $this->header('Authorization');
        if ($header && preg_match('/Bearer\s+(.*)$/i', $header, $matches)) {
            return trim($matches[1]);
        }
        return null;
    }

    public function isAjax(): bool {
        return $this->header('X-Requested-With') === 'XMLHttpRequest' ||
            str_contains((string)$this->header('Accept'), 'application/json');
    }

    public function ip(): string {
        return $this->server['HTTP_CLIENT_IP'] ??
            $this->server['HTTP_X_FORWARDED_FOR'] ??
            $this->server['REMOTE_ADDR'] ??
            '127.0.0.1';
    }

    private function parseHeaders(): array {
        $headers = [];
        foreach ($this->server as $name => $value) {
            if (str_starts_with($name, 'HTTP_')) {
                $headerName = strtolower(str_replace('_', '-', substr($name, 5)));
                $headers[$headerName] = (string)$value;
            } elseif (in_array($name, ['CONTENT_TYPE', 'CONTENT_LENGTH'])) {
                $headerName = strtolower(str_replace('_', '-', $name));
                $headers[$headerName] = (string)$value;
            }
        }
        return $headers;
    }
}
