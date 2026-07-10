<?php
namespace App\Models;

use App\Core\Database;

class Testimonial
{
    public static function published(int $limit = 0): array
    {
        $sql = "SELECT * FROM testimonials WHERE status = 'published' ORDER BY sort_order, id";
        if ($limit > 0) $sql .= ' LIMIT ' . (int) $limit;
        return Database::run($sql)->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $row = Database::run('SELECT * FROM testimonials WHERE id = ?', [$id])->fetch();
        return $row ?: null;
    }

    public static function allForAdmin(): array
    {
        return Database::run('SELECT * FROM testimonials ORDER BY sort_order, id')->fetchAll();
    }

    public static function save(array $data, ?int $id = null): int
    {
        $fields = ['author','author_role','content','rating','status','sort_order'];
        $params = [];
        foreach ($fields as $f) $params[$f] = $data[$f] ?? null;

        if ($id) {
            $set = implode(', ', array_map(fn($f) => "`$f` = :$f", $fields));
            $params['id'] = $id;
            Database::run("UPDATE testimonials SET $set WHERE id = :id", $params);
            return $id;
        }
        $cols = implode(', ', array_map(fn($f) => "`$f`", $fields));
        $vals = implode(', ', array_map(fn($f) => ":$f", $fields));
        Database::run("INSERT INTO testimonials ($cols) VALUES ($vals)", $params);
        return (int) Database::pdo()->lastInsertId();
    }

    public static function delete(int $id): void
    {
        Database::run('DELETE FROM testimonials WHERE id = ?', [$id]);
    }
}
