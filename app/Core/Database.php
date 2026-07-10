<?php
namespace App\Core;

use PDO;
use PDOException;

/**
 * Conexão PDO única (singleton) com o MySQL.
 * Todas as consultas do site usam prepared statements.
 */
class Database
{
    private static ?PDO $pdo = null;

    public static function pdo(): PDO
    {
        if (self::$pdo === null) {
            $cfg = config('db');
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                $cfg['host'], $cfg['name'], $cfg['charset']
            );
            try {
                self::$pdo = new PDO($dsn, $cfg['user'], $cfg['pass'], [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                if (config('env') === 'development') {
                    throw $e;
                }
                http_response_code(500);
                exit('Erro de conexão com o banco de dados. Verifique config/config.php.');
            }
        }
        return self::$pdo;
    }

    /** Executa uma consulta preparada e retorna o statement. */
    public static function run(string $sql, array $params = []): \PDOStatement
    {
        $stmt = self::pdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
}
