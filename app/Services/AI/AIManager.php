<?php
declare(strict_types=1);

namespace App\Services\AI;

class AIManager {
    private static ?AIProviderInterface $provider = null;

    public static function provider(): AIProviderInterface {
        if (self::$provider !== null) {
            return self::$provider;
        }

        $driver = env('AI_PROVIDER', 'mock');
        $apiKey = env('GEMINI_API_KEY', '');

        if ($driver === 'gemini' && !empty($apiKey)) {
            self::$provider = new GeminiProvider($apiKey);
        } else {
            self::$provider = new MockProvider();
        }

        return self::$provider;
    }

    public static function setProvider(AIProviderInterface $provider): void {
        self::$provider = $provider;
    }
}
