<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Book;
use App\Models\Page;

class PageController extends Controller
{
    public function about(): void
    {
        $page = Page::findBySlug('sobre-o-pr');
        $this->view('about/index', [
            'pageTitle' => 'Sobre o Pastor',
            'page'      => $page,
            'books'     => Book::published(),
        ]);
    }

    public function privacy(): void  { $this->show('politica-de-privacidade'); }
    public function terms(): void    { $this->show('termos-e-condicoes'); }

    public function show(string $slug = ''): void
    {
        $page = Page::findBySlug($slug);
        if (!$page) {
            $this->notFound();
            return;
        }
        $this->view('pages/show', [
            'pageTitle' => $page['title'],
            'page'      => $page,
        ]);
    }

    public function notFound(): void
    {
        http_response_code(404);
        $this->view('errors/404', ['pageTitle' => 'Página não encontrada']);
    }
}
