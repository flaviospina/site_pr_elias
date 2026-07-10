<?php
namespace App\Models;

use App\Core\Database;

class User
{
    public static function findByEmail(string $email): ?array
    {
        $row = Database::run('SELECT * FROM users WHERE email = ?', [$email])->fetch();
        return $row ?: null;
    }

    public static function find(int $id): ?array
    {
        $row = Database::run('SELECT * FROM users WHERE id = ?', [$id])->fetch();
        return $row ?: null;
    }

    public static function all(): array
    {
        return Database::run('SELECT id, name, email, role, active, last_login_at FROM users ORDER BY name')->fetchAll();
    }

    public static function count(): int
    {
        return (int) Database::run('SELECT COUNT(*) AS t FROM users')->fetch()['t'];
    }

    public static function touchLogin(int $id): void
    {
        Database::run('UPDATE users SET last_login_at = NOW() WHERE id = ?', [$id]);
    }

    public static function save(array $data, ?int $id = null): int
    {
        if ($id) {
            if (!empty($data['password'])) {
                Database::run(
                    'UPDATE users SET name = ?, email = ?, role = ?, active = ?, password_hash = ? WHERE id = ?',
                    [$data['name'], $data['email'], $data['role'], $data['active'],
                     password_hash($data['password'], PASSWORD_BCRYPT), $id]
                );
            } else {
                Database::run(
                    'UPDATE users SET name = ?, email = ?, role = ?, active = ? WHERE id = ?',
                    [$data['name'], $data['email'], $data['role'], $data['active'], $id]
                );
            }
            return $id;
        }
        Database::run(
            'INSERT INTO users (name, email, role, active, password_hash) VALUES (?, ?, ?, ?, ?)',
            [$data['name'], $data['email'], $data['role'], $data['active'],
             password_hash($data['password'], PASSWORD_BCRYPT)]
        );
        return (int) Database::pdo()->lastInsertId();
    }

    public static function delete(int $id): void
    {
        Database::run('DELETE FROM users WHERE id = ?', [$id]);
    }
}
