<?php
declare(strict_types=1);

use App\Database\Migration;
use App\Database\Schema;
use App\Database\TableBlueprint;

return new class extends Migration {
    public function up(PDO $pdo): void {
        Schema::createTableIfNotExists('course', function (TableBlueprint $table) {
            $table->id('C_ID');
            $table->string('C_NAME', 255);
            $table->text('C_CARD_DESCRIPTION', true);
            $table->text('C_DESCRIPTION', true);
            $table->string('C_CARD_IMAGE', 500, true);
            $table->string('C_DONE_BY', 255, true);
            $table->string('C_LINK', 500, true);
            $table->string('TOP_COURSE', 20, false, 'No');
            $table->timestamp('C_ADD_DATE', true);
        }, $pdo);
    }

    public function down(PDO $pdo): void {
        Schema::dropTableIfExists('course', $pdo);
    }
};
