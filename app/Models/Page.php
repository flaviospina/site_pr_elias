<?php
namespace App\Models;

use App\Core\Database;

class Page
{
    public static function findBySlug(string $slug): ?array
    {
        $row = Database::run("SELECT * FROM pages WHERE slug = ? AND status = 'published'", [$slug])->fetch();
        return $row ?: null;
    }

    public static function find(int $id): ?array
    {
        $row = Database::run('SELECT * FROM pages WHERE id = ?', [$id])->fetch();
        return $row ?: null;
    }

    public static function allForAdmin(): array
    {
        return Database::run('SELECT id, title, slug, status, updated_at FROM pages ORDER BY title')->fetchAll();
    }

    public static function save(array $data, ?int $id = null): int
    {
        $fields = ['title','slug','content','status'];
        $params = [];
        foreach ($fields as $f) $params[$f] = $data[$f] ?? null;

        if ($id) {
            $set = implode(', ', array_map(fn($f) => "`$f` = :$f", $fields));
            $params['id'] = $id;
            Database::run("UPDATE pages SET $set WHERE id = :id", $params);
            return $id;
        }
        $cols = implode(', ', array_map(fn($f) => "`$f`", $fields));
        $vals = implode(', ', array_map(fn($f) => ":$f", $fields));
        Database::run("INSERT INTO pages ($cols) VALUES ($vals)", $params);
        return (int) Database::pdo()->lastInsertId();
    }

    public static function delete(int $id): void
    {
        Database::run('DELETE FROM pages WHERE id = ?', [$id]);
    }
}
