<?php
declare(strict_types=1);

use App\Database\Migration;
use App\Database\Schema;
use App\Database\TableBlueprint;

return new class extends Migration {
    public function up(PDO $pdo): void {
        Schema::createTableIfNotExists('ai_conversations', function (TableBlueprint $table) {
            $table->id('id');
            $table->integer('user_id');
            $table->integer('course_id', true);
            $table->integer('lesson_id', true);
            $table->string('title', 255, false, 'New Consultation');
            $table->timestamps();
            $table->index(['user_id', 'created_at']);
        }, $pdo);

        Schema::createTableIfNotExists('ai_messages', function (TableBlueprint $table) {
            $table->id('id');
            $table->integer('conversation_id');
            $table->string('role', 20, false, 'user');
            $table->text('content', false);
            $table->integer('tokens_used', false, 0);
            $table->timestamps();
            $table->index('conversation_id');
        }, $pdo);
    }

    public function down(PDO $pdo): void {
        Schema::dropTableIfExists('ai_messages', $pdo);
        Schema::dropTableIfExists('ai_conversations', $pdo);
    }
};
