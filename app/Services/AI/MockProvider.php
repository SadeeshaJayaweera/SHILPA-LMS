<?php
declare(strict_types=1);

namespace App\Services\AI;

class MockProvider implements AIProviderInterface {
    public function chat(array $messages, array $options = []): array {
        $lastMessage = end($messages);
        $prompt = strtolower(trim((string)($lastMessage['content'] ?? '')));

        $contextCourse = $options['course_title'] ?? null;
        $contextLesson = $options['lesson_title'] ?? null;

        $responsePrefix = "";
        if ($contextCourse) {
            $responsePrefix .= "*(Grounded in **{$contextCourse}**)*\n\n";
        }

        if (str_contains($prompt, 'complexity') || str_contains($prompt, 'big o')) {
            $body = "### Algorithmic Complexity Breakdown\n" .
                "- **Time Complexity:** Analyzes execution steps relative to input size \\(N\\).\n" .
                "- **Space Complexity:** Measures auxiliary memory consumed during execution.\n\n" .
                "```python\n# Linear Search: O(N) vs Binary Search: O(log N)\ndef binary_search(arr, target):\n    low, high = 0, len(arr) - 1\n    while low <= high:\n        mid = (low + high) // 2\n        if arr[mid] == target: return mid\n        elif arr[mid] < target: low = mid + 1\n        else: high = mid - 1\n    return -1\n```\n\n" .
                "Would you like me to walk through amortized analysis or recurrence relations?";
        } elseif (str_contains($prompt, 'sql') || str_contains($prompt, 'database') || str_contains($prompt, 'join')) {
            $body = "### Relational Database Mechanics\n" .
                "In relational architectures, queries are executed via query plans optimized for B-Tree indexing.\n\n" .
                "```sql\n-- Optimized Index Scan Query\nSELECT u.Full_Name, c.C_NAME, e.progress_percent\nFROM enrollments e\nJOIN users u ON u.UserId = e.user_id\nJOIN course c ON c.C_ID = e.course_id\nWHERE e.progress_percent >= 80;\n```\n\n" .
                "Notice the foreign key relationships ensuring referential integrity.";
        } elseif (str_contains($prompt, 'quiz') || str_contains($prompt, 'test me') || str_contains($prompt, 'practice')) {
            $body = "### Quick Self-Assessment Challenge\n" .
                "**Question:** Why do B-Tree indexes provide faster range lookups compared to Hash indexes in relational databases?\n\n" .
                "- A) B-Trees store data in sorted order across leaf pages linked by pointers.\n" .
                "- B) Hash indexes have quadratic time complexity for lookups.\n" .
                "- C) B-Trees require no disk I/O operations.\n\n" .
                "*(Reply with your selected answer to verify your understanding!)*";
        } else {
            $body = "### Shilpa AI Academic Assistance\n" .
                "Thank you for your question regarding " . ($contextLesson ? "**{$contextLesson}**" : "your coursework") . ".\n\n" .
                "Here are the foundational principles you should review:\n" .
                "1. **Core Concept Mastery:** Focus on first principles and formal mathematical/architectural definitions.\n" .
                "2. **Empirical Implementation:** Write minimal reproducible code examples to observe execution semantics.\n" .
                "3. **Peer Collaboration:** Discuss edge cases in the course discussion forum.\n\n" .
                "How else can I assist your study session today?";
        }

        return [
            'content' => $responsePrefix . $body,
            'tokens_used' => 185,
        ];
    }

    public function summarize(string $content, array $options = []): string {
        $clean = strip_tags($content);
        $preview = substr($clean, 0, 100);

        return "### Executive Academic Summary\n\n" .
            "**Core Thesis:** This module explores high-performance architectures and formal analytical methodologies.\n\n" .
            "#### Key Takeaways:\n" .
            "- **Foundational Architecture:** Deconstructs complex workflows into modular, verifiable sub-components.\n" .
            "- **Performance Optimizations:** Leverages indexing, caching, and asymptotic bounds to minimize latency.\n" .
            "- **Resilience & Fault Tolerance:** Employs defensive validation and automated testing barriers to eliminate regression.\n\n" .
            "#### Practical Application:\n" .
            "Apply these principles directly to upcoming laboratory assessments and collaborative milestone projects.";
    }

    public function generatePracticeQuestions(string $content, int $count = 3): array {
        $questions = [
            [
                'question' => 'What is the primary benefit of decoupling application logic from direct database queries via a service layer?',
                'options' => [
                    'A' => 'Guarantees 100% testability and isolation of business rules.',
                    'B' => 'Eliminates the need for database indexing.',
                    'C' => 'Increases memory footprint linearly.',
                    'D' => 'Removes all HTTP latency.',
                ],
                'answer' => 'A',
                'explanation' => 'A dedicated service layer encapsulates business domain logic, allowing independent unit testing with mocks and clean architectural maintenance.',
            ],
            [
                'question' => 'Under ACID transaction semantics, what does Isolation guarantee?',
                'options' => [
                    'A' => 'Data is saved to physical SSDs immediately.',
                    'B' => 'Concurrent transactions execute without interfering with one another.',
                    'C' => 'Foreign key constraints cannot be violated.',
                    'D' => 'Only one user can read the database at a time.',
                ],
                'answer' => 'B',
                'explanation' => 'Isolation ensures that concurrent transactions result in a system state identical to serial execution.',
            ],
            [
                'question' => 'Which HTTP status code signifies that a resource was successfully created?',
                'options' => [
                    'A' => '200 OK',
                    'B' => '201 Created',
                    'C' => '204 No Content',
                    'D' => '302 Found',
                ],
                'answer' => 'B',
                'explanation' => 'HTTP 201 Created explicitly indicates that the request was fulfilled and resulted in the creation of a new resource.',
            ],
        ];

        return array_slice($questions, 0, max(1, $count));
    }
}
