<?php
declare(strict_types=1);

use App\Database\Migration;
use App\Database\Schema;
use App\Database\TableBlueprint;

return new class extends Migration {
    public function up(PDO $pdo): void {
        $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
        $roleSql = $driver === 'sqlite'
            ? 'ALTER TABLE "enrollments" ADD COLUMN "role" VARCHAR(20) DEFAULT \'student\''
            : 'ALTER TABLE `enrollments` ADD COLUMN `role` VARCHAR(20) DEFAULT \'student\'';
        $expiresSql = $driver === 'sqlite'
            ? 'ALTER TABLE "enrollments" ADD COLUMN "expires_at" DATETIME NULL'
            : 'ALTER TABLE `enrollments` ADD COLUMN `expires_at` DATETIME NULL';

        try {
            $pdo->exec($roleSql);
        } catch (\Throwable $e) {}

        try {
            $pdo->exec($expiresSql);
        } catch (\Throwable $e) {}

        Schema::createTableIfNotExists('course_waitlists', function (TableBlueprint $table) {
            $table->id('id');
            $table->integer('course_id');
            $table->integer('user_id');
            $table->integer('position', false, 1);
            $table->string('status', 20, false);
            $table->timestamps();
            $table->unique(['course_id', 'user_id']);
            $table->index(['course_id', 'status']);
        }, $pdo);
    }

    public function down(PDO $pdo): void {
        Schema::dropTableIfExists('course_waitlists', $pdo);
    }
};
