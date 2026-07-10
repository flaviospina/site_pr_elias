<?php
namespace App\Controllers;

use App\Core\Cart;
use App\Core\Controller;

class CartController extends Controller
{
    public function index(): void
    {
        $this->view('cart/index', [
            'pageTitle' => 'Carrinho',
            'items'     => Cart::items(),
            'total'     => Cart::total(),
        ]);
    }

    public function add(): void
    {
        $this->requireCsrf();
        Cart::add((int) ($_POST['book_id'] ?? 0), (int) ($_POST['quantity'] ?? 1));
        flash('success', 'Livro adicionado ao carrinho!');
        redirect('carrinho');
    }

    public function update(): void
    {
        $this->requireCsrf();
        foreach ((array) ($_POST['qty'] ?? []) as $bookId => $qty) {
            Cart::update((int) $bookId, (int) $qty);
        }
        redirect('carrinho');
    }

    public function remove(): void
    {
        $this->requireCsrf();
        Cart::remove((int) ($_POST['book_id'] ?? 0));
        redirect('carrinho');
    }
}
