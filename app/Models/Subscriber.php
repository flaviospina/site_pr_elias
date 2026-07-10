<?php
namespace App\Models;

use App\Core\Database;

class Subscriber
{
    public static function subscribe(?string $name, string $email): bool
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return false;
        Database::run(
            'INSERT INTO subscribers (name, email) VALUES (?, ?) ON DUPLICATE KEY UPDATE name = COALESCE(VALUES(name), name)',
            [$name ?: null, $email]
        );
        return true;
    }

    public static function allForAdmin(): array
    {
        return Database::run('SELECT * FROM subscribers ORDER BY created_at DESC')->fetchAll();
    }

    public static function count(): int
    {
        return (int) Database::run('SELECT COUNT(*) AS t FROM subscribers')->fetch()['t'];
    }

    public static function delete(int $id): void
    {
        Database::run('DELETE FROM subscribers WHERE id = ?', [$id]);
    }
}
