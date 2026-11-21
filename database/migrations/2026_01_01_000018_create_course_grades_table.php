<?php
declare(strict_types=1);

use App\Database\Migration;
use App\Database\Schema;
use App\Database\TableBlueprint;

return new class extends Migration {
    public function up(PDO $pdo): void {
        Schema::createTableIfNotExists('course_grades', function (TableBlueprint $table) {
            $table->id('id');
            $table->integer('course_id');
            $table->integer('student_id');
            $table->integer('total_score', false, 0);
            $table->integer('max_score', false, 0);
            $table->integer('percentage', false, 0);
            $table->string('letter_grade', 5, false, 'F');
            $table->integer('is_published', false, 0);
            $table->timestamp('published_at', true);
            $table->timestamps();
            $table->unique(['course_id', 'student_id']);
            $table->index(['student_id', 'is_published']);
        }, $pdo);
    }

    public function down(PDO $pdo): void {
        Schema::dropTableIfExists('course_grades', $pdo);
    }
};
