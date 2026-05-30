<?php

namespace App\Models;

use App\Core\Database;

class User
{
    public static function allAssignable(): array
    {
        return Database::connect()
            ->query('SELECT id, name, email, role FROM users ORDER BY name, email')
            ->fetchAll();
    }

    public static function all(): array
    {
        return Database::connect()
            ->query('SELECT id, name, email, role, created_at FROM users ORDER BY name, email')
            ->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::connect()->prepare('SELECT * FROM users WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $user = $stmt->fetch();

        return $user ?: null;
    }

    public static function findByEmail(string $email): ?array
    {
        $stmt = Database::connect()->prepare('SELECT * FROM users WHERE email = :email');
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        return $user ?: null;
    }

    public static function create(array $data): void
    {
        $stmt = Database::connect()->prepare(
            'INSERT INTO users (name, email, password_hash, role) VALUES (:name, :email, :password_hash, :role)'
        );
        $stmt->execute($data);
    }
}
