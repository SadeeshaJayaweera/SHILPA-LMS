<?php
declare(strict_types=1);

use App\Database\Migration;
use App\Database\Schema;
use App\Database\TableBlueprint;

return new class extends Migration {
    public function up(PDO $pdo): void {
        Schema::createTableIfNotExists('modules', function (TableBlueprint $table) {
            $table->id('id');
            $table->integer('course_id');
            $table->string('title', 255, false);
            $table->text('description', true);
            $table->integer('order_index', false, 0);
            $table->integer('is_published', false, 1);
            $table->timestamps();
            $table->index(['course_id', 'order_index']);
        }, $pdo);

        Schema::createTableIfNotExists('lessons', function (TableBlueprint $table) {
            $table->id('id');
            $table->integer('module_id');
            $table->string('title', 255, false);
            $table->string('slug', 255, false);
            $table->string('content_type', 50, false);
            $table->text('content_body', true);
            $table->string('video_url', 255, true);
            $table->integer('duration_minutes', false, 15);
            $table->integer('order_index', false, 0);
            $table->integer('is_free_preview', false, 0);
            $table->timestamps();
            $table->index(['module_id', 'order_index']);
        }, $pdo);
    }

    public function down(PDO $pdo): void {
        Schema::dropTableIfExists('lessons', $pdo);
        Schema::dropTableIfExists('modules', $pdo);
    }
};
