<?php

namespace App\Models;

use App\Core\Database;
use Throwable;

class Setting
{
    private static ?array $cache = null;

    public static function get(string $key, $default = null)
    {
        if (self::$cache === null) {
            self::load();
        }

        return self::$cache[$key] ?? $default;
    }

    public static function set(string $key, string $value): void
    {
        $stmt = Database::connect()->prepare(
            'INSERT INTO settings (key, value) VALUES (:key, :value)
             ON CONFLICT(key) DO UPDATE SET value = excluded.value'
        );
        $stmt->execute([
            ':key' => $key,
            ':value' => $value,
        ]);

        self::$cache[$key] = $value;
    }

    private static function load(): void
    {
        self::$cache = [];

        try {
            $rows = Database::connect()->query('SELECT key, value FROM settings')->fetchAll();
        } catch (Throwable $e) {
            return;
        }

        foreach ($rows as $row) {
            self::$cache[$row['key']] = $row['value'];
        }
    }
}
