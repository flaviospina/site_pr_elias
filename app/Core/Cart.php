<?php
namespace App\Core;

use App\Models\Book;

/** Carrinho de compras em sessão. Estrutura: [book_id => quantidade]. */
class Cart
{
    public static function items(): array
    {
        $cart = $_SESSION['cart'] ?? [];
        if (!$cart) return [];

        $ids   = array_keys($cart);
        $books = Book::findManyPublished($ids);
        $items = [];
        foreach ($books as $book) {
            $qty = max(1, (int) $cart[$book['id']]);
            $price = self::effectivePrice($book);
            $items[] = [
                'book'     => $book,
                'quantity' => $qty,
                'price'    => $price,
                'subtotal' => $price * $qty,
            ];
        }
        return $items;
    }

    public static function effectivePrice(array $book): float
    {
        $sale = $book['sale_price'] !== null ? (float) $book['sale_price'] : 0.0;
        return ($sale > 0 && $sale < (float) $book['price']) ? $sale : (float) $book['price'];
    }

    public static function add(int $bookId, int $qty = 1): void
    {
        $qty = max(1, min(99, $qty));
        $_SESSION['cart'][$bookId] = min(99, ($_SESSION['cart'][$bookId] ?? 0) + $qty);
    }

    public static function update(int $bookId, int $qty): void
    {
        if ($qty <= 0) {
            self::remove($bookId);
            return;
        }
        if (isset($_SESSION['cart'][$bookId])) {
            $_SESSION['cart'][$bookId] = min(99, $qty);
        }
    }

    public static function remove(int $bookId): void
    {
        unset($_SESSION['cart'][$bookId]);
    }

    public static function clear(): void
    {
        unset($_SESSION['cart']);
    }

    public static function count(): int
    {
        return array_sum($_SESSION['cart'] ?? []);
    }

    public static function total(): float
    {
        return array_sum(array_column(self::items(), 'subtotal'));
    }
}
