<?php
declare(strict_types=1);

use App\Database\Migration;
use App\Database\Schema;
use App\Database\TableBlueprint;

return new class extends Migration {
    public function up(PDO $pdo): void {
        Schema::createTableIfNotExists('notifications', function (TableBlueprint $table) {
            $table->id('id');
            $table->integer('user_id');
            $table->string('type', 100, false, 'system.alert');
            $table->string('title', 255, false);
            $table->text('message', false);
            $table->string('action_url', 255, true);
            $table->string('icon', 50, false, 'bell');
            $table->integer('is_read', false, 0);
            $table->timestamp('read_at', true);
            $table->timestamps();
            $table->index(['user_id', 'is_read']);
            $table->index(['user_id', 'created_at']);
        }, $pdo);

        Schema::createTableIfNotExists('notification_preferences', function (TableBlueprint $table) {
            $table->id('id');
            $table->integer('user_id');
            $table->integer('email_enabled', false, 1);
            $table->integer('assignment_alerts', false, 1);
            $table->integer('announcement_alerts', false, 1);
            $table->integer('grade_alerts', false, 1);
            $table->integer('discussion_alerts', false, 1);
            $table->timestamps();
            $table->unique('user_id');
        }, $pdo);

        Schema::createTableIfNotExists('email_logs', function (TableBlueprint $table) {
            $table->id('id');
            $table->string('to_email', 255, false);
            $table->string('to_name', 255, true);
            $table->string('subject', 255, false);
            $table->text('body_html', false);
            $table->string('status', 50, false, 'sent');
            $table->timestamp('sent_at', true);
            $table->timestamps();
            $table->index('to_email');
        }, $pdo);
    }

    public function down(PDO $pdo): void {
        Schema::dropTableIfExists('email_logs', $pdo);
        Schema::dropTableIfExists('notification_preferences', $pdo);
        Schema::dropTableIfExists('notifications', $pdo);
    }
};
