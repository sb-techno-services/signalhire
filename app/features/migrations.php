<?php
declare(strict_types=1);

function migrate(PDO $db): void
{
    $db->exec('CREATE TABLE IF NOT EXISTS schema_migrations (filename TEXT PRIMARY KEY)');
    foreach (glob(dirname(__DIR__, 2) . '/database/migrations/*.sql') as $file) {
        $name = basename($file);
        $db->exec('BEGIN IMMEDIATE');
        try {
            if (!one('SELECT filename FROM schema_migrations WHERE filename=?', [$name])) {
                $db->exec(file_get_contents($file));
                query('INSERT INTO schema_migrations(filename) VALUES(?)', [$name]);
            }
            $db->exec('COMMIT');
        } catch (Throwable $error) {
            $db->exec('ROLLBACK');
            throw $error;
        }
    }
}
