<?php
declare(strict_types=1);

namespace App\Core;

class Application extends Container {
    private static ?self $instance = null;
    private string $basePath;
    private bool $booted = false;

    public function __construct(string $basePath) {
        $this->basePath = rtrim($basePath, '/');
        self::$instance = $this;
        $this->registerBaseBindings();
    }

    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self(dirname(__DIR__, 2));
        }
        return self::$instance;
    }

    public function basePath(string $path = ''): string {
        return $this->basePath . ($path !== '' ? '/' . ltrim($path, '/') : '');
    }

    public function bootstrap(): self {
        if ($this->booted) {
            return $this;
        }

        // 1. Load environment
        Env::load($this->basePath('.env'));

        // 2. Load configurations
        Config::load($this->basePath('config'));

        // 3. Initialize View system
        View::init($this->basePath('resources/views'));

        // 4. Start secure session
        $this->make(Session::class);

        // 5. Load routes
        $router = $this->make(Router::class);
        $webRoutes = $this->basePath('routes/web.php');
        if (file_exists($webRoutes)) {
            $registerWeb = require $webRoutes;
            if (is_callable($registerWeb)) {
                $registerWeb($router);
            }
        }

        $apiRoutes = $this->basePath('routes/api.php');
        if (file_exists($apiRoutes)) {
            $registerApi = require $apiRoutes;
            if (is_callable($registerApi)) {
                $registerApi($router);
            }
        }

        $this->booted = true;
        return $this;
    }

    public function handle(Request $request): Response {
        $this->bootstrap();
        $this->instance(Request::class, $request);

        $router = $this->make(Router::class);
        return $router->dispatch($request);
    }

    private function registerBaseBindings(): void {
        $this->singleton(self::class, $this);
        $this->singleton(Container::class, $this);
        $this->singleton(Router::class);
        $this->singleton(Session::class);
    }
}
