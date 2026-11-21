<?php
declare(strict_types=1);

use App\Database\Migration;
use App\Database\Schema;
use App\Database\TableBlueprint;

return new class extends Migration {
    public function up(PDO $pdo): void {
        Schema::createTableIfNotExists('course_resources', function (TableBlueprint $table) {
            $table->id('id');
            $table->integer('course_id');
            $table->integer('lesson_id', true);
            $table->integer('uploader_id');
            $table->string('title', 255, false);
            $table->string('original_filename', 255, false);
            $table->string('storage_path', 255, false);
            $table->string('mime_type', 100, false);
            $table->integer('file_size_bytes', false, 0);
            $table->integer('download_count', false, 0);
            $table->integer('is_public_preview', false, 0);
            $table->timestamps();
            $table->index(['course_id', 'lesson_id']);
        }, $pdo);
    }

    public function down(PDO $pdo): void {
        Schema::dropTableIfExists('course_resources', $pdo);
    }
};
