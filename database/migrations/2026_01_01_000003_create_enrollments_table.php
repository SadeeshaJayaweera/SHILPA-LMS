<?php
declare(strict_types=1);

use App\Database\Migration;
use App\Database\Schema;
use App\Database\TableBlueprint;

return new class extends Migration {
    public function up(PDO $pdo): void {
        Schema::createTableIfNotExists('enrollments', function (TableBlueprint $table) {
            $table->id('id');
            $table->integer('user_id');
            $table->integer('course_id');
            $table->string('status', 30, false, 'active');
            $table->integer('progress_percent', false, 0);
            $table->timestamp('enrolled_at', true);
            $table->timestamp('completed_at', false);
            $table->unique(['user_id', 'course_id']);
        }, $pdo);
    }

    public function down(PDO $pdo): void {
        Schema::dropTableIfExists('enrollments', $pdo);
    }
};
