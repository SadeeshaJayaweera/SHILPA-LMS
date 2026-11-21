<?php
declare(strict_types=1);

use App\Database\Migration;
use App\Database\Schema;
use App\Database\TableBlueprint;

return new class extends Migration {
    public function up(PDO $pdo): void {
        Schema::createTableIfNotExists('lesson_blocks', function (TableBlueprint $table) {
            $table->id('id');
            $table->integer('lesson_id');
            $table->string('block_type', 50, false);
            $table->text('content', true);
            $table->integer('order_index', false, 0);
            $table->timestamps();
            $table->index(['lesson_id', 'order_index']);
        }, $pdo);
    }

    public function down(PDO $pdo): void {
        Schema::dropTableIfExists('lesson_blocks', $pdo);
    }
};
