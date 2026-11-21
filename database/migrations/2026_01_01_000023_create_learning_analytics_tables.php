<?php
declare(strict_types=1);

use App\Database\Migration;
use App\Database\Schema;
use App\Database\TableBlueprint;

return new class extends Migration {
    public function up(PDO $pdo): void {
        Schema::createTableIfNotExists('learning_events', function (TableBlueprint $table) {
            $table->id('id');
            $table->integer('user_id');
            $table->integer('course_id', true);
            $table->string('event_type', 100, false);
            $table->integer('duration_seconds', false, 0);
            $table->text('metadata', true);
            $table->timestamps();
            $table->index(['user_id', 'created_at']);
            $table->index(['course_id', 'event_type']);
        }, $pdo);

        Schema::createTableIfNotExists('course_analytics_cache', function (TableBlueprint $table) {
            $table->id('id');
            $table->integer('course_id');
            $table->integer('enrolled_count', false, 0);
            $table->integer('active_learners_count', false, 0);
            $table->integer('completion_rate_percent', false, 0);
            $table->integer('avg_grade_percent', false, 0);
            $table->integer('at_risk_count', false, 0);
            $table->text('metrics_data', true);
            $table->timestamps();
            $table->unique('course_id');
        }, $pdo);
    }

    public function down(PDO $pdo): void {
        Schema::dropTableIfExists('course_analytics_cache', $pdo);
        Schema::dropTableIfExists('learning_events', $pdo);
    }
};
