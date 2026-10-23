<?php
/**
 * חיבור למסד הנתונים + יצירת הטבלאות אוטומטית בפעם הראשונה.
 */

function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $config = require __DIR__ . '/config.php';
    $c = $config['db'];

    $dsn = "mysql:host={$c['host']};dbname={$c['name']};charset={$c['charset']}";
    try {
        $pdo = new PDO($dsn, $c['user'], $c['password'], [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        die('שגיאת התחברות למסד הנתונים: ' . htmlspecialchars($e->getMessage()));
    }

    init_schema($pdo);
    return $pdo;
}

function init_schema(PDO $pdo): void
{
    $pdo->exec(<<<SQL
        CREATE TABLE IF NOT EXISTS teams (
            id            INT AUTO_INCREMENT PRIMARY KEY,
            slot          TINYINT NOT NULL UNIQUE,
            team_name     VARCHAR(190) NOT NULL,
            participants  VARCHAR(500) NOT NULL DEFAULT '',
            stage         TINYINT NOT NULL DEFAULT 1,
            forefather_ok TINYINT NOT NULL DEFAULT 0,
            code_ok       TINYINT NOT NULL DEFAULT 0,
            limerick      TEXT NULL,
            attempts      INT NOT NULL DEFAULT 0,
            created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    SQL);
}
