<?php
namespace App\Models;

use App\Core\Database;

class Book
{
    public static function published(): array
    {
        return Database::run(
            "SELECT * FROM books WHERE status = 'published' ORDER BY sort_order, title"
        )->fetchAll();
    }

    public static function featured(int $limit = 3): array
    {
        return Database::run(
            "SELECT * FROM books WHERE status = 'published' AND featured = 1 ORDER BY sort_order LIMIT " . (int) $limit
        )->fetchAll();
    }

    public static function findBySlug(string $slug): ?array
    {
        $row = Database::run("SELECT * FROM books WHERE slug = ? AND status = 'published'", [$slug])->fetch();
        return $row ?: null;
    }

    public static function find(int $id): ?array
    {
        $row = Database::run('SELECT * FROM books WHERE id = ?', [$id])->fetch();
        return $row ?: null;
    }

    public static function findManyPublished(array $ids): array
    {
        if (!$ids) return [];
        $ids = array_map('intval', $ids);
        $in  = implode(',', array_fill(0, count($ids), '?'));
        return Database::run(
            "SELECT * FROM books WHERE id IN ($in) AND status = 'published'", $ids
        )->fetchAll();
    }

    public static function allForAdmin(): array
    {
        return Database::run('SELECT * FROM books ORDER BY sort_order, title')->fetchAll();
    }

    public static function save(array $data, ?int $id = null): int
    {
        $fields = ['title','slug','subtitle','excerpt','description','price','sale_price','image','gallery',
                   'pages','isbn','stock','in_stock','featured','badge','sort_order','status'];
        $params = [];
        foreach ($fields as $f) $params[$f] = $data[$f] ?? null;

        if ($id) {
            $set = implode(', ', array_map(fn($f) => "`$f` = :$f", $fields));
            $params['id'] = $id;
            Database::run("UPDATE books SET $set WHERE id = :id", $params);
            return $id;
        }
        $cols = implode(', ', array_map(fn($f) => "`$f`", $fields));
        $vals = implode(', ', array_map(fn($f) => ":$f", $fields));
        Database::run("INSERT INTO books ($cols) VALUES ($vals)", $params);
        return (int) Database::pdo()->lastInsertId();
    }

    public static function delete(int $id): void
    {
        Database::run('DELETE FROM books WHERE id = ?', [$id]);
    }
}
