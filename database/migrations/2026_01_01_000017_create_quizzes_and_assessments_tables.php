<?php
declare(strict_types=1);

use App\Database\Migration;
use App\Database\Schema;
use App\Database\TableBlueprint;

return new class extends Migration {
    public function up(PDO $pdo): void {
        Schema::createTableIfNotExists('quizzes', function (TableBlueprint $table) {
            $table->id('id');
            $table->integer('course_id');
            $table->integer('created_by');
            $table->string('title', 255, false);
            $table->text('description', true);
            $table->integer('time_limit_minutes', false, 30);
            $table->integer('passing_score_percent', false, 70);
            $table->integer('max_attempts', false, 3);
            $table->integer('is_published', false, 1);
            $table->timestamps();
            $table->index('course_id');
        }, $pdo);

        Schema::createTableIfNotExists('quiz_questions', function (TableBlueprint $table) {
            $table->id('id');
            $table->integer('quiz_id');
            $table->string('question_type', 50, false, 'single_choice');
            $table->text('question_text', false);
            $table->text('options_json', true);
            $table->text('correct_answer_json', false);
            $table->integer('points', false, 10);
            $table->integer('order_index', false, 0);
            $table->timestamps();
            $table->index(['quiz_id', 'order_index']);
        }, $pdo);

        Schema::createTableIfNotExists('quiz_attempts', function (TableBlueprint $table) {
            $table->id('id');
            $table->integer('quiz_id');
            $table->integer('student_id');
            $table->integer('attempt_number', false, 1);
            $table->integer('score_achieved', false, 0);
            $table->integer('max_score', false, 0);
            $table->integer('is_passed', false, 0);
            $table->text('answers_json', true);
            $table->timestamp('started_at', true);
            $table->timestamp('submitted_at', true);
            $table->timestamps();
            $table->index(['quiz_id', 'student_id']);
        }, $pdo);
    }

    public function down(PDO $pdo): void {
        Schema::dropTableIfExists('quiz_attempts', $pdo);
        Schema::dropTableIfExists('quiz_questions', $pdo);
        Schema::dropTableIfExists('quizzes', $pdo);
    }
};
