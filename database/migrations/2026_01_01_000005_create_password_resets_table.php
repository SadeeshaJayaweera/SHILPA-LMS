<?php
declare(strict_types=1);

use App\Database\Migration;
use App\Database\Schema;
use App\Database\TableBlueprint;

return new class extends Migration {
    public function up(PDO $pdo): void {
        Schema::createTableIfNotExists('password_resets', function (TableBlueprint $table) {
            $table->string('email', 255);
            $table->string('token', 255);
            $table->timestamp('created_at', true);
            $table->index('email');
        }, $pdo);
    }

    public function down(PDO $pdo): void {
        Schema::dropTableIfExists('password_resets', $pdo);
    }
};
