<?php
declare(strict_types=1);

use App\Database\Migration;
use App\Database\Schema;
use App\Database\TableBlueprint;

return new class extends Migration {
    public function up(PDO $pdo): void {
        Schema::createTableIfNotExists('roles', function (TableBlueprint $table) {
            $table->id('id');
            $table->string('name', 50, false);
            $table->string('display_name', 100, false);
            $table->string('description', 255, true);
            $table->unique('name');
        }, $pdo);

        Schema::createTableIfNotExists('permissions', function (TableBlueprint $table) {
            $table->id('id');
            $table->string('name', 100, false);
            $table->string('description', 255, true);
            $table->unique('name');
        }, $pdo);

        Schema::createTableIfNotExists('role_permissions', function (TableBlueprint $table) {
            $table->integer('role_id');
            $table->integer('permission_id');
            $table->unique(['role_id', 'permission_id']);
        }, $pdo);

        Schema::createTableIfNotExists('user_roles', function (TableBlueprint $table) {
            $table->integer('user_id');
            $table->integer('role_id');
            $table->unique(['user_id', 'role_id']);
        }, $pdo);

        // Seed core roles and permissions
        $roles = [
            ['student', 'Student Learner', 'Access enrolled courses, submit assignments, and take quizzes'],
            ['lecturer', 'Faculty Instructor', 'Create and manage courses, grade submissions, and author assessments'],
            ['admin', 'Platform Administrator', 'Manage users, courses, system settings, and audit logs'],
            ['superadmin', 'Institutional Superadmin', 'Full platform-wide governance and security access'],
        ];

        foreach ($roles as [$name, $display, $desc]) {
            $stmt = $pdo->prepare("INSERT OR IGNORE INTO roles (`name`, `display_name`, `description`) VALUES (?, ?, ?)");
            if ($pdo->getAttribute(PDO::ATTR_DRIVER_NAME) !== 'sqlite') {
                $stmt = $pdo->prepare("INSERT IGNORE INTO roles (`name`, `display_name`, `description`) VALUES (?, ?, ?)");
            }
            $stmt->execute([$name, $display, $desc]);
        }
    }

    public function down(PDO $pdo): void {
        Schema::dropTableIfExists('user_roles', $pdo);
        Schema::dropTableIfExists('role_permissions', $pdo);
        Schema::dropTableIfExists('permissions', $pdo);
        Schema::dropTableIfExists('roles', $pdo);
    }
};
