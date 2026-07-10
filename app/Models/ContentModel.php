<?php
namespace App\Models;

use App\Core\Database;

/**
 * Base para conteúdos publicáveis (sermões e devocionais),
 * que compartilham a mesma estrutura de tabela.
 */
abstract class ContentModel
{
    protected const TABLE  = '';
    protected const FIELDS = ['title','slug','excerpt','content','bible_reference','image','status','published_at'];

    public static function published(int $limit = 0, int $offset = 0): array
    {
        $sql = 'SELECT * FROM ' . static::TABLE . " WHERE status = 'published' ORDER BY published_at DESC, id DESC";
        if ($limit > 0) $sql .= ' LIMIT ' . (int) $limit . ' OFFSET ' . (int) $offset;
        return Database::run($sql)->fetchAll();
    }

    public static function countPublished(?string $search = null): int
    {
        $sql = 'SELECT COUNT(*) AS total FROM ' . static::TABLE . " WHERE status = 'published'";
        $params = [];
        if ($search) {
            $sql .= ' AND (title LIKE ? OR content LIKE ?)';
            $params = ["%$search%", "%$search%"];
        }
        return (int) Database::run($sql, $params)->fetch()['total'];
    }

    public static function search(string $term, int $limit, int $offset): array
    {
        return Database::run(
            'SELECT * FROM ' . static::TABLE .
            " WHERE status = 'published' AND (title LIKE ? OR content LIKE ?)
              ORDER BY published_at DESC, id DESC LIMIT " . (int) $limit . ' OFFSET ' . (int) $offset,
            ["%$term%", "%$term%"]
        )->fetchAll();
    }

    public static function findBySlug(string $slug): ?array
    {
        $row = Database::run(
            'SELECT * FROM ' . static::TABLE . " WHERE slug = ? AND status = 'published'", [$slug]
        )->fetch();
        return $row ?: null;
    }

    public static function find(int $id): ?array
    {
        $row = Database::run('SELECT * FROM ' . static::TABLE . ' WHERE id = ?', [$id])->fetch();
        return $row ?: null;
    }

    /** Anterior/próximo para navegação entre textos. */
    public static function neighbors(array $current): array
    {
        $prev = Database::run(
            'SELECT title, slug FROM ' . static::TABLE .
            " WHERE status='published' AND (published_at < ? OR (published_at = ? AND id < ?))
              ORDER BY published_at DESC, id DESC LIMIT 1",
            [$current['published_at'], $current['published_at'], $current['id']]
        )->fetch() ?: null;
        $next = Database::run(
            'SELECT title, slug FROM ' . static::TABLE .
            " WHERE status='published' AND (published_at > ? OR (published_at = ? AND id > ?))
              ORDER BY published_at ASC, id ASC LIMIT 1",
            [$current['published_at'], $current['published_at'], $current['id']]
        )->fetch() ?: null;
        return [$prev, $next];
    }

    public static function allForAdmin(): array
    {
        return Database::run(
            'SELECT id, title, slug, status, published_at FROM ' . static::TABLE . ' ORDER BY published_at DESC, id DESC'
        )->fetchAll();
    }

    public static function save(array $data, ?int $id = null): int
    {
        $fields = static::FIELDS;
        $params = [];
        foreach ($fields as $f) $params[$f] = $data[$f] ?? null;

        if ($id) {
            $set = implode(', ', array_map(fn($f) => "`$f` = :$f", $fields));
            $params['id'] = $id;
            Database::run('UPDATE ' . static::TABLE . " SET $set WHERE id = :id", $params);
            return $id;
        }
        $cols = implode(', ', array_map(fn($f) => "`$f`", $fields));
        $vals = implode(', ', array_map(fn($f) => ":$f", $fields));
        Database::run('INSERT INTO ' . static::TABLE . " ($cols) VALUES ($vals)", $params);
        return (int) Database::pdo()->lastInsertId();
    }

    public static function delete(int $id): void
    {
        Database::run('DELETE FROM ' . static::TABLE . ' WHERE id = ?', [$id]);
    }
}
