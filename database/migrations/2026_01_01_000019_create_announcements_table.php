<?php
declare(strict_types=1);

use App\Database\Migration;
use App\Database\Schema;
use App\Database\TableBlueprint;

return new class extends Migration {
    public function up(PDO $pdo): void {
        Schema::createTableIfNotExists('announcements', function (TableBlueprint $table) {
            $table->id('id');
            $table->integer('course_id', true); // Nullable for global campus-wide announcements
            $table->integer('author_id');
            $table->string('title', 255, false);
            $table->text('content', false);
            $table->integer('is_pinned', false, 0);
            $table->timestamps();
            $table->index('course_id');
            $table->index('author_id');
        }, $pdo);
    }

    public function down(PDO $pdo): void {
        Schema::dropTableIfExists('announcements', $pdo);
    }
};
