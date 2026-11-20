<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;

class LegacyBridgeController extends Controller {
    public function handle(Request $request, string $file): Response {
        $base = dirname(__DIR__, 2);
        $target = $base . '/' . basename($file);
        if (!file_exists($target)) {
            return new Response("Not found", 404);
        }

        ob_start();
        include $target;
        $content = ob_get_clean();

        return new Response((string)$content);
    }
}
