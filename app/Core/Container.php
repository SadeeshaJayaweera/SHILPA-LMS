<?php
declare(strict_types=1);

namespace App\Core;

use Closure;
use ReflectionClass;
use ReflectionNamedType;
use RuntimeException;

class Container {
    private array $bindings = [];
    private array $instances = [];

    public function bind(string $abstract, mixed $concrete = null, bool $shared = false): void {
        $concrete = $concrete ?? $abstract;
        $this->bindings[$abstract] = [
            'concrete' => $concrete,
            'shared' => $shared,
        ];
    }

    public function singleton(string $abstract, mixed $concrete = null): void {
        $this->bind($abstract, $concrete, true);
    }

    public function instance(string $abstract, mixed $instance): void {
        $this->instances[$abstract] = $instance;
    }

    public function make(string $abstract): mixed {
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        if (!isset($this->bindings[$abstract])) {
            return $this->build($abstract);
        }

        $concrete = $this->bindings[$abstract]['concrete'];

        if ($concrete instanceof Closure) {
            $object = $concrete($this);
        } elseif (is_string($concrete)) {
            $object = $this->build($concrete);
        } else {
            $object = $concrete;
        }

        if ($this->bindings[$abstract]['shared']) {
            $this->instances[$abstract] = $object;
        }

        return $object;
    }

    private function build(string $concrete): mixed {
        if (!class_exists($concrete)) {
            throw new RuntimeException("Target class [$concrete] does not exist.");
        }

        $reflector = new ReflectionClass($concrete);
        if (!$reflector->isInstantiable()) {
            throw new RuntimeException("Target [$concrete] is not instantiable.");
        }

        $constructor = $reflector->getConstructor();
        if ($constructor === null) {
            return new $concrete();
        }

        $dependencies = [];
        foreach ($constructor->getParameters() as $parameter) {
            $type = $parameter->getType();
            if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
                $dependencies[] = $this->make($type->getName());
            } elseif ($parameter->isDefaultValueAvailable()) {
                $dependencies[] = $parameter->getDefaultValue();
            } else {
                throw new RuntimeException("Cannot resolve parameter [{$parameter->getName()}] in class [$concrete].");
            }
        }

        return $reflector->newInstanceArgs($dependencies);
    }
}
