<?php
namespace App\Controllers\Admin;

use App\Models\Page;

class PageController extends AdminController
{
    public function index(): void
    {
        $this->adminView('admin/pages/index', [
            'pageTitle' => 'Páginas',
            'items'     => Page::allForAdmin(),
        ]);
    }

    public function create(): void
    {
        $this->adminView('admin/pages/form', ['pageTitle' => 'Nova página', 'item' => null]);
    }

    public function edit(string $id): void
    {
        $item = Page::find((int) $id);
        if (!$item) redirect('admin/paginas');
        $this->adminView('admin/pages/form', ['pageTitle' => 'Editar página', 'item' => $item]);
    }

    public function save(): void
    {
        $this->requireCsrf();
        $id    = (int) ($_POST['id'] ?? 0) ?: null;
        $title = trim($_POST['title'] ?? '');
        if ($title === '') {
            flash('error', 'Informe o título da página.');
            redirect($id ? "admin/paginas/$id/editar" : 'admin/paginas/novo');
        }
        Page::save([
            'title'   => mb_substr($title, 0, 255),
            'slug'    => slugify(trim($_POST['slug'] ?? '') ?: $title),
            'content' => clean_html($_POST['content'] ?? ''),
            'status'  => ($_POST['status'] ?? '') === 'draft' ? 'draft' : 'published',
        ], $id);
        flash('success', 'Página salva com sucesso.');
        redirect('admin/paginas');
    }

    public function delete(string $id): void
    {
        $this->requireCsrf();
        Page::delete((int) $id);
        flash('success', 'Página excluída.');
        redirect('admin/paginas');
    }
}
