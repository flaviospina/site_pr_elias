<?php
namespace App\Models;

use App\Core\Database;

class Order
{
    /** Cria o pedido com itens dentro de uma transação; retorna o pedido criado. */
    public static function create(array $data, array $items): array
    {
        $pdo = Database::pdo();
        $pdo->beginTransaction();
        try {
            $code = self::generateCode();
            Database::run(
                'INSERT INTO orders (order_code, customer_name, customer_email, customer_phone, customer_cpf,
                    address_zip, address_street, address_number, address_complement, address_district,
                    address_city, address_state, subtotal, shipping, total, payment_method, status,
                    notes, lgpd_consent, ip)
                 VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)',
                [
                    $code, $data['customer_name'], $data['customer_email'], $data['customer_phone'],
                    $data['customer_cpf'], $data['address_zip'], $data['address_street'], $data['address_number'],
                    $data['address_complement'], $data['address_district'], $data['address_city'],
                    $data['address_state'], $data['subtotal'], $data['shipping'], $data['total'],
                    $data['payment_method'], 'pending', $data['notes'], $data['lgpd_consent'], client_ip(),
                ]
            );
            $orderId = (int) $pdo->lastInsertId();

            foreach ($items as $item) {
                Database::run(
                    'INSERT INTO order_items (order_id, book_id, title, unit_price, quantity, line_total)
                     VALUES (?,?,?,?,?,?)',
                    [$orderId, $item['book']['id'], $item['book']['title'],
                     $item['price'], $item['quantity'], $item['subtotal']]
                );
            }
            $pdo->commit();
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
        return self::find($orderId);
    }

    private static function generateCode(): string
    {
        return 'EJ' . date('ymd') . strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));
    }

    public static function find(int $id): ?array
    {
        $row = Database::run('SELECT * FROM orders WHERE id = ?', [$id])->fetch();
        return $row ?: null;
    }

    public static function findByCode(string $code): ?array
    {
        $row = Database::run('SELECT * FROM orders WHERE order_code = ?', [$code])->fetch();
        return $row ?: null;
    }

    public static function items(int $orderId): array
    {
        return Database::run('SELECT * FROM order_items WHERE order_id = ?', [$orderId])->fetchAll();
    }

    public static function setPaymentRef(int $id, string $ref): void
    {
        Database::run('UPDATE orders SET payment_ref = ? WHERE id = ?', [$ref, $id]);
    }

    public static function updateStatus(int $id, string $status): void
    {
        $valid = ['pending','paid','shipped','completed','cancelled'];
        if (!in_array($status, $valid, true)) return;
        Database::run('UPDATE orders SET status = ? WHERE id = ?', [$status, $id]);
    }

    public static function allForAdmin(?string $status = null): array
    {
        if ($status) {
            return Database::run('SELECT * FROM orders WHERE status = ? ORDER BY created_at DESC', [$status])->fetchAll();
        }
        return Database::run('SELECT * FROM orders ORDER BY created_at DESC')->fetchAll();
    }

    public static function stats(): array
    {
        $row = Database::run(
            "SELECT COUNT(*) AS total_orders,
                    COALESCE(SUM(CASE WHEN status IN ('paid','shipped','completed') THEN total END), 0) AS revenue,
                    SUM(status = 'pending') AS pending_orders
             FROM orders"
        )->fetch();
        return $row ?: ['total_orders' => 0, 'revenue' => 0, 'pending_orders' => 0];
    }

    public static function delete(int $id): void
    {
        Database::run('DELETE FROM orders WHERE id = ?', [$id]);
    }
}
