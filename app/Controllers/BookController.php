<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Book;
use App\Models\Testimonial;

class BookController extends Controller
{
    public function index(): void
    {
        $this->view('books/index', [
            'pageTitle'    => 'Livros',
            'books'        => Book::published(),
            'testimonials' => Testimonial::published(3),
        ]);
    }

    public function show(string $slug): void
    {
        $book = Book::findBySlug($slug);
        if (!$book) {
            (new PageController())->notFound();
            return;
        }
        $others = array_values(array_filter(
            Book::published(),
            fn($b) => $b['id'] !== $book['id']
        ));
        $this->view('books/show', [
            'pageTitle'    => $book['title'],
            'book'         => $book,
            'others'       => array_slice($others, 0, 3),
            'testimonials' => Testimonial::published(3),
        ]);
    }
}
