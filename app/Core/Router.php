<?php
declare(strict_types=1);

namespace App\Core;

use Closure;
use RuntimeException;

class Router {
    private array $routes = [];
    private array $namedRoutes = [];
    private array $groupStack = [];

    public function get(string $uri, mixed $action): self {
        return $this->addRoute(['GET', 'HEAD'], $uri, $action);
    }

    public function post(string $uri, mixed $action): self {
        return $this->addRoute(['POST'], $uri, $action);
    }

    public function put(string $uri, mixed $action): self {
        return $this->addRoute(['PUT'], $uri, $action);
    }

    public function delete(string $uri, mixed $action): self {
        return $this->addRoute(['DELETE'], $uri, $action);
    }

    public function any(string $uri, mixed $action): self {
        return $this->addRoute(['GET', 'POST', 'PUT', 'DELETE', 'HEAD', 'PATCH'], $uri, $action);
    }

    public function group(array $attributes, Closure $callback): void {
        $this->groupStack[] = $attributes;
        $callback($this);
        array_pop($this->groupStack);
    }

    public function addRoute(array $methods, string $uri, mixed $action): self {
        $prefix = '';
        $middleware = [];

        foreach ($this->groupStack as $group) {
            if (isset($group['prefix'])) {
                $prefix .= '/' . trim($group['prefix'], '/');
            }
            if (isset($group['middleware'])) {
                $m = is_array($group['middleware']) ? $group['middleware'] : [$group['middleware']];
                $middleware = array_merge($middleware, $m);
            }
        }

        $fullUri = rtrim($prefix, '/') . '/' . ltrim($uri, '/');
        $fullUri = $fullUri === '' ? '/' : $fullUri;
        $regex = $this->compileUriRegex($fullUri);

        $route = [
            'methods' => $methods,
            'uri' => $fullUri,
            'regex' => $regex,
            'action' => $action,
            'middleware' => $middleware,
        ];

        $this->routes[] = $route;
        return $this;
    }

    public function dispatch(Request $request): Response {
        $method = $request->method();
        $path = $request->path();

        // Support _method spoofing for PUT/DELETE in POST forms
        if ($method === 'POST' && $request->post('_method')) {
            $method = strtoupper((string)$request->post('_method'));
        }

        foreach ($this->routes as $route) {
            if (!in_array($method, $route['methods'], true)) {
                continue;
            }

            if (preg_match($route['regex'], $path, $matches)) {
                $params = array_filter($matches, fn($k) => !is_numeric($k), ARRAY_FILTER_USE_KEY);
                $response = $this->runRoute($route, $request, $params);
                return \App\Security\SecurityHeaders::apply($response);
            }
        }

        $response = new Response(
            View::render('errors/404', ['path' => $path]),
            404,
            ['Content-Type' => 'text/html; charset=UTF-8']
        );
        return \App\Security\SecurityHeaders::apply($response);
    }

    private function runRoute(array $route, Request $request, array $params): Response {
        $action = $route['action'];

        // Execute route middleware pipeline if any
        if (!empty($route['middleware'])) {
            foreach ($route['middleware'] as $mw) {
                if (is_string($mw) && class_exists($mw)) {
                    $instance = app($mw);
                    $result = $instance->handle($request);
                    if ($result instanceof Response) {
                        return $result;
                    }
                }
            }
        }

        if ($action instanceof Closure) {
            $response = $action($request, ...$params);
        } elseif (is_array($action)) {
            [$controllerClass, $method] = $action;
            $controller = app($controllerClass);
            $response = $controller->$method($request, ...$params);
        } elseif (is_string($action) && str_contains($action, '@')) {
            [$controllerClass, $method] = explode('@', $action);
            $controller = app($controllerClass);
            $response = $controller->$method($request, ...$params);
        } else {
            throw new RuntimeException("Invalid route action specified.");
        }

        if ($response instanceof Response) {
            return $response;
        }

        if (is_array($response) || is_object($response)) {
            return Response::json($response);
        }

        return new Response((string)$response);
    }

    private function compileUriRegex(string $uri): string {
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $uri);
        return '#^' . $pattern . '$#';
    }
}
