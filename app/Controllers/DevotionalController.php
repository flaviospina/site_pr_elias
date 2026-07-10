<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Devotional;
use App\Models\Subscriber;

class DevotionalController extends Controller
{
    private const PER_PAGE = 12;

    public function index(): void
    {
        $search = trim($_GET['busca'] ?? '');
        $page   = max(1, (int) ($_GET['pagina'] ?? 1));
        $offset = ($page - 1) * self::PER_PAGE;

        $devotionals = $search
            ? Devotional::search($search, self::PER_PAGE, $offset)
            : Devotional::published(self::PER_PAGE, $offset);

        $total = Devotional::countPublished($search ?: null);

        $this->view('devotionals/index', [
            'pageTitle'   => 'Devocionais',
            'devotionals' => $devotionals,
            'search'      => $search,
            'page'        => $page,
            'totalPages'  => (int) ceil($total / self::PER_PAGE),
        ]);
    }

    public function show(string $slug): void
    {
        $devotional = Devotional::findBySlug($slug);
        if (!$devotional) {
            (new PageController())->notFound();
            return;
        }
        [$prev, $next] = Devotional::neighbors($devotional);
        $this->view('devotionals/show', [
            'pageTitle'  => $devotional['title'],
            'devotional' => $devotional,
            'prev'       => $prev,
            'next'       => $next,
        ]);
    }

    public function subscribe(): void
    {
        $this->requireCsrf();
        // Honeypot anti-spam: campo invisível que humanos não preenchem
        if (!empty($_POST['website'])) {
            redirect('devocionais');
        }
        $ok = Subscriber::subscribe(
            trim($_POST['name'] ?? '') ?: null,
            trim($_POST['email'] ?? '')
        );
        flash($ok ? 'success' : 'error', $ok
            ? 'Inscrição realizada! Você receberá os devocionais por e-mail.'
            : 'Informe um e-mail válido.');
        redirect($_POST['_back'] ?? 'devocionais');
    }
}
