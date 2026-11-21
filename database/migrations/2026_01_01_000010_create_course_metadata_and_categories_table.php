<?php
declare(strict_types=1);

use App\Database\Migration;
use App\Database\Schema;
use App\Database\TableBlueprint;

return new class extends Migration {
    public function up(PDO $pdo): void {
        Schema::createTableIfNotExists('course_categories', function (TableBlueprint $table) {
            $table->id('id');
            $table->string('name', 100, false);
            $table->string('slug', 100, false);
            $table->text('description', true);
            $table->string('icon', 50, true);
            $table->integer('display_order', false, 0);
            $table->timestamps();
            $table->unique('slug');
        }, $pdo);

        Schema::createTableIfNotExists('course_metadata', function (TableBlueprint $table) {
            $table->id('id');
            $table->integer('course_id');
            $table->integer('category_id', true);
            $table->string('status', 20, false);
            $table->string('level', 20, false);
            $table->text('prerequisites', true);
            $table->text('tags', true);
            $table->text('syllabus', true);
            $table->text('learning_outcomes', true);
            $table->integer('instructor_id', true);
            $table->integer('featured', false, 0);
            $table->timestamps();
            $table->unique('course_id');
        }, $pdo);

        // Seed default academic categories
        $categories = [
            ['Software Engineering', 'software-engineering', 'Architecture, clean code, enterprise patterns, and systems design', 'code', 1],
            ['Artificial Intelligence & Data', 'ai-data-science', 'Deep learning, NLP, statistical modeling, and data pipelines', 'cpu', 2],
            ['Cybersecurity & Networks', 'cybersecurity', 'Defensive security, penetration testing, cryptography, and protocols', 'shield', 3],
            ['Cloud & DevOps', 'cloud-devops', 'Kubernetes, distributed microservices, CI/CD, and site reliability', 'cloud', 4],
            ['UI/UX & Product Design', 'design-product', 'User research, interface psychology, design systems, and heuristics', 'layout', 5],
        ];

        foreach ($categories as [$name, $slug, $desc, $icon, $order]) {
            $stmt = $pdo->prepare("INSERT OR IGNORE INTO course_categories (`name`, `slug`, `description`, `icon`, `display_order`, `created_at`, `updated_at`) VALUES (?, ?, ?, ?, ?, datetime('now'), datetime('now'))");
            if ($pdo->getAttribute(PDO::ATTR_DRIVER_NAME) !== 'sqlite') {
                $stmt = $pdo->prepare("INSERT IGNORE INTO course_categories (`name`, `slug`, `description`, `icon`, `display_order`, `created_at`, `updated_at`) VALUES (?, ?, ?, ?, ?, NOW(), NOW())");
            }
            $stmt->execute([$name, $slug, $desc, $icon, $order]);
        }
    }

    public function down(PDO $pdo): void {
        Schema::dropTableIfExists('course_metadata', $pdo);
        Schema::dropTableIfExists('course_categories', $pdo);
    }
};
