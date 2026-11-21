<?php
declare(strict_types=1);

use App\Database\Migration;
use App\Database\Schema;
use App\Database\TableBlueprint;

return new class extends Migration {
    public function up(PDO $pdo): void {
        Schema::createTableIfNotExists('direct_messages', function (TableBlueprint $table) {
            $table->id('id');
            $table->integer('sender_id');
            $table->integer('recipient_id');
            $table->text('message', false);
            $table->integer('is_read', false, 0);
            $table->timestamp('read_at', true);
            $table->timestamps();
            $table->index(['sender_id', 'recipient_id']);
            $table->index(['recipient_id', 'is_read']);
        }, $pdo);
    }

    public function down(PDO $pdo): void {
        Schema::dropTableIfExists('direct_messages', $pdo);
    }
};
