<?php
declare(strict_types=1);

use App\Database\Migration;
use App\Database\Schema;
use App\Database\TableBlueprint;

return new class extends Migration {
    public function up(PDO $pdo): void {
        Schema::createTableIfNotExists('student_activities', function (TableBlueprint $table) {
            $table->id('id');
            $table->integer('user_id');
            $table->string('activity_type', 50, false, 'general');
            $table->string('title', 255, false);
            $table->string('description', 500, true);
            $table->string('icon', 50, false, 'book');
            $table->timestamp('created_at', true);
        }, $pdo);

        Schema::createTableIfNotExists('student_deadlines', function (TableBlueprint $table) {
            $table->id('id');
            $table->integer('user_id');
            $table->integer('course_id');
            $table->string('title', 255, false);
            $table->string('due_date', 50, false);
            $table->string('type', 50, false, 'assignment');
            $table->string('status', 50, false, 'pending');
            $table->integer('weight_percent', false, 10);
            $table->timestamp('created_at', true);
        }, $pdo);
    }

    public function down(PDO $pdo): void {
        Schema::dropTableIfExists('student_deadlines', $pdo);
        Schema::dropTableIfExists('student_activities', $pdo);
    }
};
