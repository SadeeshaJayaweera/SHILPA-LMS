<?php
declare(strict_types=1);

use App\Database\Migration;
use App\Database\Schema;
use App\Database\TableBlueprint;

return new class extends Migration {
    public function up(PDO $pdo): void {
        Schema::createTableIfNotExists('users', function (TableBlueprint $table) {
            $table->id('UserId');
            $table->string('Full_Name', 255);
            $table->string('Email', 255);
            $table->string('Password', 255);
            $table->string('user_type', 30, false, 'student');
            $table->timestamp('created_at', true);
            $table->unique('Email');
        }, $pdo);
    }

    public function down(PDO $pdo): void {
        Schema::dropTableIfExists('users', $pdo);
    }
};
