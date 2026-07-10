<?php
namespace App\Models;

use App\Core\Database;

class ContactMessage
{
    public static function create(array $data): void
    {
        Database::run(
            'INSERT INTO contact_messages (name, email, phone, subject, message, lgpd_consent, ip)
             VALUES (?,?,?,?,?,?,?)',
            [$data['name'], $data['email'], $data['phone'], $data['subject'],
             $data['message'], $data['lgpd_consent'], client_ip()]
        );
    }

    public static function allForAdmin(): array
    {
        return Database::run('SELECT * FROM contact_messages ORDER BY created_at DESC')->fetchAll();
    }

    public static function unreadCount(): int
    {
        return (int) Database::run('SELECT COUNT(*) AS t FROM contact_messages WHERE is_read = 0')->fetch()['t'];
    }

    public static function markRead(int $id): void
    {
        Database::run('UPDATE contact_messages SET is_read = 1 WHERE id = ?', [$id]);
    }

    public static function delete(int $id): void
    {
        Database::run('DELETE FROM contact_messages WHERE id = ?', [$id]);
    }

    /** Limita envios por IP: máx. 5 mensagens por hora. */
    public static function rateLimited(): bool
    {
        $row = Database::run(
            'SELECT COUNT(*) AS t FROM contact_messages WHERE ip = ? AND created_at > (NOW() - INTERVAL 1 HOUR)',
            [client_ip()]
        )->fetch();
        return (int) $row['t'] >= 5;
    }
}
