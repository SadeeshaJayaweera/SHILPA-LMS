<?php
declare(strict_types=1);

namespace App\Services\AI;

interface AIProviderInterface {
    /**
     * Complete a chat conversation with a list of messages.
     *
     * @param array<int, array{role: string, content: string}> $messages
     * @param array<string, mixed> $options
     * @return array{content: string, tokens_used: int}
     */
    public function chat(array $messages, array $options = []): array;

    /**
     * Generate a concise academic summary of lesson content.
     */
    public function summarize(string $content, array $options = []): string;

    /**
     * Generate practice questions with answers and explanations.
     *
     * @return array<int, array{question: string, options: array<string, string>, answer: string, explanation: string}>
     */
    public function generatePracticeQuestions(string $content, int $count = 3): array;
}
