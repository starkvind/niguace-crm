<?php

namespace App\Models;

use App\Core\Database;

class Category
{
    public static function all(): array
    {
        return Database::connect()
            ->query('SELECT * FROM categories ORDER BY section, sort_order, name')
            ->fetchAll();
    }

    public static function forSection(string $section): array
    {
        $stmt = Database::connect()->prepare('SELECT * FROM categories WHERE section = :section ORDER BY sort_order, name');
        $stmt->execute([':section' => $section]);

        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::connect()->prepare('SELECT * FROM categories WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $category = $stmt->fetch();

        return $category ?: null;
    }

    public static function findBySectionAndSlug(string $section, string $slug): ?array
    {
        $stmt = Database::connect()->prepare('SELECT * FROM categories WHERE section = :section AND slug = :slug');
        $stmt->execute([':section' => $section, ':slug' => $slug]);
        $category = $stmt->fetch();

        return $category ?: null;
    }

    public static function create(array $data): void
    {
        $stmt = Database::connect()->prepare(
            'INSERT INTO categories (section, name, slug, sort_order) VALUES (:section, :name, :slug, :sort_order)'
        );
        $stmt->execute($data);
    }

    public static function update(int $id, array $data): void
    {
        $data[':id'] = $id;
        $stmt = Database::connect()->prepare(
            'UPDATE categories SET section = :section, name = :name, slug = :slug, sort_order = :sort_order WHERE id = :id'
        );
        $stmt->execute($data);
    }

    public static function delete(int $id): void
    {
        $stmt = Database::connect()->prepare('DELETE FROM categories WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }

    public static function slugExists(string $section, string $slug, ?int $ignoreId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM categories WHERE section = :section AND slug = :slug';
        $params = [':section' => $section, ':slug' => $slug];
        if ($ignoreId) {
            $sql .= ' AND id != :id';
            $params[':id'] = $ignoreId;
        }

        $stmt = Database::connect()->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn() > 0;
    }
}
