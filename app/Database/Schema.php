<?php
declare(strict_types=1);

namespace App\Database;

use PDO;

class Schema {
    public static function createTableIfNotExists(string $table, callable $callback, ?PDO $pdo = null): void {
        $pdo = $pdo ?? Connection::get();
        $blueprint = new TableBlueprint($table, $pdo);
        $callback($blueprint);
        $blueprint->build();
    }

    public static function dropTableIfExists(string $table, ?PDO $pdo = null): void {
        $pdo = $pdo ?? Connection::get();
        $pdo->exec("DROP TABLE IF EXISTS `$table`");
    }
}
