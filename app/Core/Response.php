<?php
declare(strict_types=1);

namespace App\Core;

class Response {
    private string $content;
    private int $statusCode;
    private array $headers;

    public function __construct(string $content = '', int $statusCode = 200, array $headers = []) {
        $this->content = $content;
        $this->statusCode = $statusCode;
        $this->headers = $headers;
    }

    public static function json(mixed $data, int $statusCode = 200, array $headers = []): self {
        $headers['Content-Type'] = 'application/json; charset=UTF-8';
        $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return new self($json ?: '{}', $statusCode, $headers);
    }

    public static function redirect(string $url, int $statusCode = 302): self {
        return new self('', $statusCode, ['Location' => $url]);
    }

    public static function html(string $html, int $statusCode = 200): self {
        return new self($html, $statusCode, ['Content-Type' => 'text/html; charset=UTF-8']);
    }

    public function header(string $key, string $value): self {
        $this->headers[$key] = $value;
        return $this;
    }

    public function setHeader(string $key, string $value): self {
        $this->headers[$key] = $value;
        return $this;
    }

    public function status(int $code): self {
        $this->statusCode = $code;
        return $this;
    }

    public function send(): void {
        if (!headers_sent()) {
            http_response_code($this->statusCode);
            foreach ($this->headers as $key => $value) {
                header("$key: $value");
            }
        }
        echo $this->content;
    }

    public function getContent(): string {
        return $this->content;
    }

    public function getStatusCode(): int {
        return $this->statusCode;
    }

    public function getHeaders(): array {
        return $this->headers;
    }

    public function getHeader(string $key): ?string {
        foreach ($this->headers as $k => $v) {
            if (strcasecmp($k, $key) === 0) {
                return (string)$v;
            }
        }
        return null;
    }
}
