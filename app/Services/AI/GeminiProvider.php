<?php
declare(strict_types=1);

namespace App\Services\AI;

class GeminiProvider implements AIProviderInterface {
    private string $apiKey;
    private string $model;
    private MockProvider $fallback;

    public function __construct(?string $apiKey = null, string $model = 'gemini-2.5-flash') {
        $this->apiKey = $apiKey ?? (string)env('GEMINI_API_KEY', '');
        $this->model = $model;
        $this->fallback = new MockProvider();
    }

    public function chat(array $messages, array $options = []): array {
        if ($this->apiKey === '') {
            return $this->fallback->chat($messages, $options);
        }

        // Format Gemini contents payload
        $contents = [];
        foreach ($messages as $msg) {
            $role = ($msg['role'] ?? 'user') === 'assistant' ? 'model' : 'user';
            $contents[] = [
                'role' => $role,
                'parts' => [['text' => (string)$msg['content']]],
            ];
        }

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}";
        $payload = json_encode(['contents' => $contents]);

        $opts = [
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/json\r\n",
                'content' => $payload,
                'timeout' => 15,
                'ignore_errors' => true,
            ],
        ];

        try {
            $ctx = stream_context_create($opts);
            $res = @file_get_contents($url, false, $ctx);
            if ($res === false) {
                return $this->fallback->chat($messages, $options);
            }

            $json = json_decode($res, true);
            $text = $json['candidates'][0]['content']['parts'][0]['text'] ?? null;
            if (!$text) {
                return $this->fallback->chat($messages, $options);
            }

            return [
                'content' => (string)$text,
                'tokens_used' => (int)($json['usageMetadata']['totalTokenCount'] ?? 150),
            ];
        } catch (\Throwable $e) {
            return $this->fallback->chat($messages, $options);
        }
    }

    public function summarize(string $content, array $options = []): string {
        if ($this->apiKey === '') {
            return $this->fallback->summarize($content, $options);
        }

        $messages = [
            [
                'role' => 'user',
                'content' => "Please provide a concise academic summary with key takeaways of the following curriculum content:\n\n" . substr($content, 0, 4000),
            ],
        ];

        $res = $this->chat($messages, $options);
        return $res['content'];
    }

    public function generatePracticeQuestions(string $content, int $count = 3): array {
        return $this->fallback->generatePracticeQuestions($content, $count);
    }
}
