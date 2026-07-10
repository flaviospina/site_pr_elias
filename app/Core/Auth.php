<?php
namespace App\Core;

use App\Models\User;

/** Autenticação do painel administrativo. */
class Auth
{
    private const MAX_ATTEMPTS = 5;      // tentativas por IP
    private const WINDOW_MIN   = 15;     // janela em minutos

    public static function attempt(string $email, string $password): bool
    {
        if (self::tooManyAttempts()) {
            return false;
        }
        self::recordAttempt($email);

        $user = User::findByEmail($email);
        if ($user && (int) $user['active'] === 1 && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id']   = (int) $user['id'];
            $_SESSION['admin_name'] = $user['name'];
            $_SESSION['admin_role'] = $user['role'];
            User::touchLogin((int) $user['id']);
            self::clearAttempts();
            return true;
        }
        return false;
    }

    public static function check(): bool
    {
        return !empty($_SESSION['admin_id']);
    }

    public static function id(): ?int
    {
        return isset($_SESSION['admin_id']) ? (int) $_SESSION['admin_id'] : null;
    }

    public static function logout(): void
    {
        unset($_SESSION['admin_id'], $_SESSION['admin_name'], $_SESSION['admin_role']);
        session_regenerate_id(true);
    }

    /** Exige login; redireciona ao formulário se não autenticado. */
    public static function require(): void
    {
        if (!self::check()) {
            redirect('admin/login');
        }
    }

    public static function tooManyAttempts(): bool
    {
        $row = Database::run(
            'SELECT COUNT(*) AS total FROM login_attempts WHERE ip = ? AND attempted_at > (NOW() - INTERVAL ' . self::WINDOW_MIN . ' MINUTE)',
            [client_ip()]
        )->fetch();
        return (int) ($row['total'] ?? 0) >= self::MAX_ATTEMPTS;
    }

    private static function recordAttempt(string $email): void
    {
        Database::run(
            'INSERT INTO login_attempts (ip, email, attempted_at) VALUES (?, ?, NOW())',
            [client_ip(), $email]
        );
    }

    private static function clearAttempts(): void
    {
        Database::run('DELETE FROM login_attempts WHERE ip = ?', [client_ip()]);
    }
}
