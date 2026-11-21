<?php
declare(strict_types=1);

use App\Database\Migration;
use App\Database\Schema;
use App\Database\TableBlueprint;

return new class extends Migration {
    public function up(PDO $pdo): void {
        Schema::createTableIfNotExists('security_audit_logs', function (TableBlueprint $table) {
            $table->id('id');
            $table->string('event', 100, false);
            $table->string('severity', 20, false);
            $table->integer('user_id', true);
            $table->string('actor_email', 255, true);
            $table->string('ip_address', 45, true);
            $table->string('user_agent', 255, true);
            $table->text('payload', true);
            $table->string('previous_hash', 64, true);
            $table->string('integrity_hash', 64, false);
            $table->timestamp('created_at', true);
            $table->index(['event', 'severity']);
            $table->index('user_id');
        }, $pdo);
    }

    public function down(PDO $pdo): void {
        Schema::dropTableIfExists('security_audit_logs', $pdo);
    }
};
