<?php
namespace App\Controllers\Admin;

use App\Models\Sermon;

class SermonController extends AdminController
{
    public function index(): void
    {
        $this->adminView('admin/sermons/index', [
            'pageTitle' => 'Sermões',
            'items'     => Sermon::allForAdmin(),
        ]);
    }

    public function create(): void
    {
        $this->adminView('admin/sermons/form', ['pageTitle' => 'Novo sermão', 'item' => null]);
    }

    public function edit(string $id): void
    {
        $item = Sermon::find((int) $id);
        if (!$item) redirect('admin/sermoes');
        $this->adminView('admin/sermons/form', ['pageTitle' => 'Editar sermão', 'item' => $item]);
    }

    public function save(): void
    {
        $this->requireCsrf();
        $id    = (int) ($_POST['id'] ?? 0) ?: null;
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        if ($title === '' || $content === '') {
            flash('error', 'Informe título e conteúdo.');
            redirect($id ? "admin/sermoes/$id/editar" : 'admin/sermoes/novo');
        }
        Sermon::save([
            'title'           => mb_substr($title, 0, 255),
            'slug'            => slugify(trim($_POST['slug'] ?? '') ?: $title),
            'excerpt'         => trim($_POST['excerpt'] ?? '') ?: excerpt_of($content),
            'content'         => clean_html($content),
            'bible_reference' => trim($_POST['bible_reference'] ?? '') ?: null,
            'video_url'       => trim($_POST['video_url'] ?? '') ?: null,
            'image'           => trim($_POST['image'] ?? '') ?: null,
            'status'          => ($_POST['status'] ?? '') === 'draft' ? 'draft' : 'published',
            'published_at'    => trim($_POST['published_at'] ?? '') ?: date('Y-m-d H:i:s'),
        ], $id);
        flash('success', 'Sermão salvo com sucesso.');
        redirect('admin/sermoes');
    }

    public function delete(string $id): void
    {
        $this->requireCsrf();
        Sermon::delete((int) $id);
        flash('success', 'Sermão excluído.');
        redirect('admin/sermoes');
    }
}
