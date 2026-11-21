<?php
declare(strict_types=1);

use App\Database\Migration;
use App\Database\Schema;
use App\Database\TableBlueprint;

return new class extends Migration {
    public function up(PDO $pdo): void {
        Schema::createTableIfNotExists('lesson_completions', function (TableBlueprint $table) {
            $table->id('id');
            $table->integer('user_id');
            $table->integer('course_id');
            $table->integer('lesson_id');
            $table->integer('time_spent_seconds', false, 0);
            $table->timestamp('completed_at', true);
            $table->timestamps();
            $table->unique(['user_id', 'lesson_id']);
            $table->index(['user_id', 'course_id']);
        }, $pdo);
    }

    public function down(PDO $pdo): void {
        Schema::dropTableIfExists('lesson_completions', $pdo);
    }
};
