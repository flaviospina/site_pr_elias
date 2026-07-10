<?php
namespace App\Controllers\Admin;

use App\Models\Book;

class BookController extends AdminController
{
    public function index(): void
    {
        $this->adminView('admin/books/index', [
            'pageTitle' => 'Livros',
            'books'     => Book::allForAdmin(),
        ]);
    }

    public function create(): void
    {
        $this->adminView('admin/books/form', ['pageTitle' => 'Novo livro', 'book' => null]);
    }

    public function edit(string $id): void
    {
        $book = Book::find((int) $id);
        if (!$book) redirect('admin/livros');
        $this->adminView('admin/books/form', ['pageTitle' => 'Editar livro', 'book' => $book]);
    }

    public function save(): void
    {
        $this->requireCsrf();
        $id    = (int) ($_POST['id'] ?? 0) ?: null;
        $title = trim($_POST['title'] ?? '');
        if ($title === '') {
            flash('error', 'Informe o título do livro.');
            redirect($id ? "admin/livros/$id/editar" : 'admin/livros/novo');
        }
        $salePrice = trim($_POST['sale_price'] ?? '');
        Book::save([
            'title'       => mb_substr($title, 0, 255),
            'slug'        => slugify(trim($_POST['slug'] ?? '') ?: $title),
            'subtitle'    => trim($_POST['subtitle'] ?? '') ?: null,
            'excerpt'     => trim($_POST['excerpt'] ?? '') ?: null,
            'description' => clean_html($_POST['description'] ?? ''),
            'price'       => (float) str_replace(',', '.', $_POST['price'] ?? '0'),
            'sale_price'  => $salePrice !== '' ? (float) str_replace(',', '.', $salePrice) : null,
            'image'       => trim($_POST['image'] ?? '') ?: null,
            'gallery'     => trim($_POST['gallery'] ?? '') ?: null,
            'pages'       => (int) ($_POST['pages'] ?? 0) ?: null,
            'isbn'        => trim($_POST['isbn'] ?? '') ?: null,
            'stock'       => (int) ($_POST['stock'] ?? 100),
            'in_stock'    => !empty($_POST['in_stock']) ? 1 : 0,
            'featured'    => !empty($_POST['featured']) ? 1 : 0,
            'badge'       => trim($_POST['badge'] ?? '') ?: null,
            'sort_order'  => (int) ($_POST['sort_order'] ?? 0),
            'status'      => ($_POST['status'] ?? '') === 'draft' ? 'draft' : 'published',
        ], $id);
        flash('success', 'Livro salvo com sucesso.');
        redirect('admin/livros');
    }

    public function delete(string $id): void
    {
        $this->requireCsrf();
        Book::delete((int) $id);
        flash('success', 'Livro excluído.');
        redirect('admin/livros');
    }
}
