<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Sermon;

class SermonController extends Controller
{
    private const PER_PAGE = 9;

    public function index(): void
    {
        $search = trim($_GET['busca'] ?? '');
        $page   = max(1, (int) ($_GET['pagina'] ?? 1));
        $offset = ($page - 1) * self::PER_PAGE;

        $sermons = $search
            ? Sermon::search($search, self::PER_PAGE, $offset)
            : Sermon::published(self::PER_PAGE, $offset);

        $total = Sermon::countPublished($search ?: null);

        $this->view('sermons/index', [
            'pageTitle'  => 'Sermões',
            'sermons'    => $sermons,
            'search'     => $search,
            'page'       => $page,
            'totalPages' => (int) ceil($total / self::PER_PAGE),
        ]);
    }

    public function show(string $slug): void
    {
        $sermon = Sermon::findBySlug($slug);
        if (!$sermon) {
            (new PageController())->notFound();
            return;
        }
        [$prev, $next] = Sermon::neighbors($sermon);
        $this->view('sermons/show', [
            'pageTitle' => $sermon['title'],
            'sermon'    => $sermon,
            'prev'      => $prev,
            'next'      => $next,
        ]);
    }
}
