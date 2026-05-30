<?php

namespace App\Models;

use App\Core\Database;

class Section
{
    public static function all(): array
    {
        return Database::connect()->query('SELECT * FROM sections ORDER BY sort_order, name')->fetchAll();
    }

    public static function navigable(): array
    {
        return Database::connect()->query('SELECT * FROM sections WHERE is_active = 1 ORDER BY sort_order, name')->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::connect()->prepare('SELECT * FROM sections WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function findBySlug(string $slug): ?array
    {
        $stmt = Database::connect()->prepare('SELECT * FROM sections WHERE slug = :slug AND is_active = 1');
        $stmt->execute([':slug' => $slug]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(array $data): void
    {
        $stmt = Database::connect()->prepare(
            'INSERT INTO sections (name, slug, nav_label, emoji, description, sort_order, is_active)
             VALUES (:name, :slug, :nav_label, :emoji, :description, :sort_order, :is_active)'
        );
        $stmt->execute($data);
    }

    public static function update(int $id, array $data): void
    {
        $data[':id'] = $id;
        $stmt = Database::connect()->prepare(
            'UPDATE sections SET name = :name, slug = :slug, nav_label = :nav_label, emoji = :emoji, description = :description, sort_order = :sort_order, is_active = :is_active WHERE id = :id'
        );
        $stmt->execute($data);
    }

    public static function delete(int $id): void
    {
        $stmt = Database::connect()->prepare('DELETE FROM sections WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }

    public static function slugExists(string $slug, ?int $ignoreId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM sections WHERE slug = :slug';
        $params = [':slug' => $slug];
        if ($ignoreId) {
            $sql .= ' AND id != :id';
            $params[':id'] = $ignoreId;
        }

        $stmt = Database::connect()->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn() > 0;
    }
}
