<?php
namespace App\Models;

use App\Core\Database;

class Event
{
    public static function upcoming(int $limit = 0): array
    {
        $sql = "SELECT * FROM events WHERE status = 'published' AND event_date >= CURDATE() ORDER BY event_date, event_time";
        if ($limit > 0) $sql .= ' LIMIT ' . (int) $limit;
        return Database::run($sql)->fetchAll();
    }

    public static function past(int $limit = 10): array
    {
        return Database::run(
            "SELECT * FROM events WHERE status = 'published' AND event_date < CURDATE() ORDER BY event_date DESC LIMIT " . (int) $limit
        )->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $row = Database::run('SELECT * FROM events WHERE id = ?', [$id])->fetch();
        return $row ?: null;
    }

    public static function allForAdmin(): array
    {
        return Database::run('SELECT * FROM events ORDER BY event_date DESC')->fetchAll();
    }

    public static function save(array $data, ?int $id = null): int
    {
        $fields = ['title','description','location','city','event_date','event_time','link','status'];
        $params = [];
        foreach ($fields as $f) $params[$f] = $data[$f] ?? null;

        if ($id) {
            $set = implode(', ', array_map(fn($f) => "`$f` = :$f", $fields));
            $params['id'] = $id;
            Database::run("UPDATE events SET $set WHERE id = :id", $params);
            return $id;
        }
        $cols = implode(', ', array_map(fn($f) => "`$f`", $fields));
        $vals = implode(', ', array_map(fn($f) => ":$f", $fields));
        Database::run("INSERT INTO events ($cols) VALUES ($vals)", $params);
        return (int) Database::pdo()->lastInsertId();
    }

    public static function delete(int $id): void
    {
        Database::run('DELETE FROM events WHERE id = ?', [$id]);
    }
}
