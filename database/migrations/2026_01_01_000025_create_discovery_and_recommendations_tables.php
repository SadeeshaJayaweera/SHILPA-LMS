<?php
declare(strict_types=1);

use App\Database\Migration;
use App\Database\Schema;
use App\Database\TableBlueprint;

return new class extends Migration {
    public function up(PDO $pdo): void {
        Schema::createTableIfNotExists('course_bookmarks', function (TableBlueprint $table) {
            $table->id('id');
            $table->integer('user_id');
            $table->integer('course_id');
            $table->timestamps();
            $table->unique(['user_id', 'course_id']);
            $table->index('user_id');
        }, $pdo);

        Schema::createTableIfNotExists('learning_pathways', function (TableBlueprint $table) {
            $table->id('id');
            $table->string('title', 255, false);
            $table->string('slug', 255, false);
            $table->text('description', false);
            $table->string('target_role', 100, false, 'Software Engineer');
            $table->text('course_ids', false); // JSON array of course IDs
            $table->string('icon', 50, false, 'compass');
            $table->integer('is_published', false, 1);
            $table->timestamps();
            $table->unique('slug');
        }, $pdo);
    }

    public function down(PDO $pdo): void {
        Schema::dropTableIfExists('learning_pathways', $pdo);
        Schema::dropTableIfExists('course_bookmarks', $pdo);
    }
};
