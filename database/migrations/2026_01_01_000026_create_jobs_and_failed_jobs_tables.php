<?php
declare(strict_types=1);

use App\Database\Migration;
use App\Database\Schema;
use App\Database\TableBlueprint;

return new class extends Migration {
    public function up(PDO $pdo): void {
        Schema::createTableIfNotExists('jobs', function (TableBlueprint $table) {
            $table->id('id');
            $table->string('queue', 50, false, 'default');
            $table->text('payload', false);
            $table->integer('attempts', false, 0);
            $table->timestamp('reserved_at', false);
            $table->timestamp('available_at', false);
            $table->timestamps();
            $table->index('queue');
        }, $pdo);

        Schema::createTableIfNotExists('failed_jobs', function (TableBlueprint $table) {
            $table->id('id');
            $table->string('connection', 50, false, 'database');
            $table->string('queue', 50, false, 'default');
            $table->text('payload', false);
            $table->text('exception', false);
            $table->timestamp('failed_at', true);
            $table->index('queue');
        }, $pdo);
    }

    public function down(PDO $pdo): void {
        Schema::dropTableIfExists('failed_jobs', $pdo);
        Schema::dropTableIfExists('jobs', $pdo);
    }
};
