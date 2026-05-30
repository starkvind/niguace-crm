<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Article
{
    public static function allForAdmin(): array
    {
        $sql = 'SELECT articles.*, categories.name AS category_name, categories.slug AS category_slug, users.name AS author_name
                FROM articles
                LEFT JOIN categories ON categories.id = articles.category_id
                LEFT JOIN users ON users.id = articles.created_by
                ORDER BY articles.updated_at DESC';

        return Database::connect()->query($sql)->fetchAll();
    }

    public static function published(?string $section = null): array
    {
        $params = [];
        $where = "articles.status = 'published'";
        if ($section) {
            $where .= ' AND articles.section = :section';
            $params[':section'] = $section;
        }

        $stmt = Database::connect()->prepare(
            "SELECT articles.*, categories.name AS category_name, categories.slug AS category_slug, users.name AS author_name
             FROM articles
             LEFT JOIN categories ON categories.id = articles.category_id
             LEFT JOIN users ON users.id = articles.created_by
             WHERE {$where}
             ORDER BY COALESCE(articles.published_at, articles.created_at) DESC"
        );
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public static function publishedByCategory(string $section, string $categorySlug): array
    {
        $stmt = Database::connect()->prepare(
            "SELECT articles.*, categories.name AS category_name, categories.slug AS category_slug, users.name AS author_name
             FROM articles
             INNER JOIN categories ON categories.id = articles.category_id
             LEFT JOIN users ON users.id = articles.created_by
             WHERE articles.status = 'published'
                AND articles.section = :section
                AND categories.slug = :category_slug
             ORDER BY COALESCE(articles.published_at, articles.created_at) DESC"
        );
        $stmt->execute([':section' => $section, ':category_slug' => $categorySlug]);

        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::connect()->prepare(
            'SELECT articles.*, users.name AS author_name
             FROM articles
             LEFT JOIN users ON users.id = articles.created_by
             WHERE articles.id = :id'
        );
        $stmt->execute([':id' => $id]);
        $article = $stmt->fetch();

        return $article ?: null;
    }

    public static function findPublishedBySlug(string $slug): ?array
    {
        $stmt = Database::connect()->prepare(
            "SELECT articles.*, categories.name AS category_name, categories.slug AS category_slug, users.name AS author_name
             FROM articles
             LEFT JOIN categories ON categories.id = articles.category_id
             LEFT JOIN users ON users.id = articles.created_by
             WHERE articles.slug = :slug AND articles.status = 'published'"
        );
        $stmt->execute([':slug' => $slug]);
        $article = $stmt->fetch();

        return $article ?: null;
    }

    public static function searchPublished(string $query): array
    {
        $query = trim($query);
        if ($query === '') {
            return [];
        }

        $escapedQuery = str_replace(['%', '_'], ['\\%', '\\_'], $query);
        $stmt = Database::connect()->prepare(
            "SELECT articles.*, categories.name AS category_name, categories.slug AS category_slug
             FROM articles
             LEFT JOIN categories ON categories.id = articles.category_id
             WHERE articles.status = 'published'
               AND (
                    articles.title LIKE :term ESCAPE '\\'
                    OR articles.excerpt LIKE :term ESCAPE '\\'
                    OR articles.body LIKE :term ESCAPE '\\'
               )
             ORDER BY
                CASE WHEN articles.title LIKE :prefix ESCAPE '\\' THEN 0 ELSE 1 END,
                COALESCE(articles.published_at, articles.created_at) DESC"
        );
        $stmt->execute([
            ':term' => '%' . $escapedQuery . '%',
            ':prefix' => $escapedQuery . '%',
        ]);

        return $stmt->fetchAll();
    }

    public static function create(array $data): int
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare(
            'INSERT INTO articles
            (section, category_id, title, slug, excerpt, featured_image, body, status, published_at, created_by, updated_at)
            VALUES
            (:section, :category_id, :title, :slug, :excerpt, :featured_image, :body, :status, :published_at, :created_by, CURRENT_TIMESTAMP)'
        );
        $stmt->execute($data);

        return (int) $pdo->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $data[':id'] = $id;
        $stmt = Database::connect()->prepare(
            'UPDATE articles SET
                section = :section,
                category_id = :category_id,
                created_by = :created_by,
                title = :title,
                slug = :slug,
                excerpt = :excerpt,
                featured_image = :featured_image,
                body = :body,
                status = :status,
                published_at = :published_at,
                updated_at = CURRENT_TIMESTAMP
             WHERE id = :id'
        );
        $stmt->execute($data);
    }

    public static function delete(int $id): void
    {
        $stmt = Database::connect()->prepare('DELETE FROM articles WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }

    public static function slugExists(string $slug, ?int $ignoreId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM articles WHERE slug = :slug';
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
