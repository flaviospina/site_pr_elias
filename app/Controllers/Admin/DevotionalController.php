<?php
namespace App\Controllers\Admin;

use App\Models\Devotional;

class DevotionalController extends AdminController
{
    public function index(): void
    {
        $this->adminView('admin/devotionals/index', [
            'pageTitle' => 'Devocionais',
            'items'     => Devotional::allForAdmin(),
        ]);
    }

    public function create(): void
    {
        $this->adminView('admin/devotionals/form', ['pageTitle' => 'Novo devocional', 'item' => null]);
    }

    public function edit(string $id): void
    {
        $item = Devotional::find((int) $id);
        if (!$item) redirect('admin/devocionais');
        $this->adminView('admin/devotionals/form', ['pageTitle' => 'Editar devocional', 'item' => $item]);
    }

    public function save(): void
    {
        $this->requireCsrf();
        $id      = (int) ($_POST['id'] ?? 0) ?: null;
        $title   = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        if ($title === '' || $content === '') {
            flash('error', 'Informe título e conteúdo.');
            redirect($id ? "admin/devocionais/$id/editar" : 'admin/devocionais/novo');
        }
        Devotional::save([
            'title'           => mb_substr($title, 0, 255),
            'slug'            => slugify(trim($_POST['slug'] ?? '') ?: $title),
            'excerpt'         => trim($_POST['excerpt'] ?? '') ?: excerpt_of($content),
            'content'         => clean_html($content),
            'bible_reference' => trim($_POST['bible_reference'] ?? '') ?: null,
            'image'           => trim($_POST['image'] ?? '') ?: null,
            'status'          => ($_POST['status'] ?? '') === 'draft' ? 'draft' : 'published',
            'published_at'    => trim($_POST['published_at'] ?? '') ?: date('Y-m-d H:i:s'),
        ], $id);
        flash('success', 'Devocional salvo com sucesso.');
        redirect('admin/devocionais');
    }

    public function delete(string $id): void
    {
        $this->requireCsrf();
        Devotional::delete((int) $id);
        flash('success', 'Devocional excluído.');
        redirect('admin/devocionais');
    }
}
