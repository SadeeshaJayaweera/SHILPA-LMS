<?php
declare(strict_types=1);

use App\Database\Migration;
use App\Database\Schema;
use App\Database\TableBlueprint;

return new class extends Migration {
    public function up(PDO $pdo): void {
        Schema::createTableIfNotExists('assignments', function (TableBlueprint $table) {
            $table->id('id');
            $table->integer('course_id');
            $table->integer('created_by');
            $table->string('title', 255, false);
            $table->text('description', true);
            $table->integer('max_score', false, 100);
            $table->timestamp('due_at', true);
            $table->integer('is_published', false, 1);
            $table->timestamps();
            $table->index('course_id');
        }, $pdo);

        Schema::createTableIfNotExists('assignment_submissions', function (TableBlueprint $table) {
            $table->id('id');
            $table->integer('assignment_id');
            $table->integer('student_id');
            $table->text('submission_text', true);
            $table->string('attachment_path', 255, true);
            $table->integer('score', true);
            $table->text('feedback', true);
            $table->string('status', 50, false, 'submitted');
            $table->timestamp('submitted_at', true);
            $table->timestamps();
            $table->index(['assignment_id', 'student_id']);
        }, $pdo);
    }

    public function down(PDO $pdo): void {
        Schema::dropTableIfExists('assignment_submissions', $pdo);
        Schema::dropTableIfExists('assignments', $pdo);
    }
};
