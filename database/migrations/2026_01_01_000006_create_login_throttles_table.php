<?php
declare(strict_types=1);

use App\Database\Migration;
use App\Database\Schema;
use App\Database\TableBlueprint;

return new class extends Migration {
    public function up(PDO $pdo): void {
        Schema::createTableIfNotExists('login_throttles', function (TableBlueprint $table) {
            $table->string('key', 255);
            $table->integer('attempts', false, 1);
            $table->integer('last_attempt_at', false, 0);
            $table->integer('locked_until', false, 0);
            $table->unique('key');
        }, $pdo);
    }

    public function down(PDO $pdo): void {
        Schema::dropTableIfExists('login_throttles', $pdo);
    }
};
