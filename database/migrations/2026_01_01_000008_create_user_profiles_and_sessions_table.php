<?php
declare(strict_types=1);

use App\Database\Migration;
use App\Database\Schema;
use App\Database\TableBlueprint;

return new class extends Migration {
    public function up(PDO $pdo): void {
        Schema::createTableIfNotExists('user_profiles', function (TableBlueprint $table) {
            $table->id('id');
            $table->integer('user_id');
            $table->string('headline', 255, true);
            $table->text('bio', true);
            $table->string('phone', 50, true);
            $table->string('avatar_url', 255, true);
            $table->string('language', 10, false);
            $table->string('timezone', 50, false);
            $table->string('theme_preference', 20, false);
            $table->integer('email_notifications');
            $table->integer('security_alerts');
            $table->timestamps();
            $table->unique('user_id');
        }, $pdo);

        Schema::createTableIfNotExists('active_sessions', function (TableBlueprint $table) {
            $table->string('id', 64, false);
            $table->integer('user_id');
            $table->string('ip_address', 45, true);
            $table->string('user_agent', 255, true);
            $table->string('device_type', 50, true);
            $table->string('location', 100, true);
            $table->string('last_activity', 50, false);
            $table->timestamps();
            $table->primary('id');
        }, $pdo);
    }

    public function down(PDO $pdo): void {
        Schema::dropTableIfExists('active_sessions', $pdo);
        Schema::dropTableIfExists('user_profiles', $pdo);
    }
};
