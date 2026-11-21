<?php
declare(strict_types=1);

use App\Database\Migration;
use App\Database\Schema;
use App\Database\TableBlueprint;

return new class extends Migration {
    public function up(PDO $pdo): void {
        Schema::createTableIfNotExists('discussion_threads', function (TableBlueprint $table) {
            $table->id('id');
            $table->integer('course_id');
            $table->integer('user_id');
            $table->string('title', 255, false);
            $table->text('body', false);
            $table->integer('is_pinned', false, 0);
            $table->integer('is_locked', false, 0);
            $table->integer('replies_count', false, 0);
            $table->timestamps();
            $table->index('course_id');
            $table->index('user_id');
        }, $pdo);

        Schema::createTableIfNotExists('discussion_replies', function (TableBlueprint $table) {
            $table->id('id');
            $table->integer('thread_id');
            $table->integer('user_id');
            $table->text('body', false);
            $table->integer('is_solution', false, 0);
            $table->integer('parent_reply_id', true);
            $table->timestamps();
            $table->index('thread_id');
            $table->index('user_id');
        }, $pdo);
    }

    public function down(PDO $pdo): void {
        Schema::dropTableIfExists('discussion_replies', $pdo);
        Schema::dropTableIfExists('discussion_threads', $pdo);
    }
};
