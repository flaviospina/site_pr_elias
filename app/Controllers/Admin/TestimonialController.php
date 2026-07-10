<?php
namespace App\Controllers\Admin;

use App\Models\Testimonial;

class TestimonialController extends AdminController
{
    public function index(): void
    {
        $this->adminView('admin/testimonials/index', [
            'pageTitle' => 'Depoimentos',
            'items'     => Testimonial::allForAdmin(),
        ]);
    }

    public function create(): void
    {
        $this->adminView('admin/testimonials/form', ['pageTitle' => 'Novo depoimento', 'item' => null]);
    }

    public function edit(string $id): void
    {
        $item = Testimonial::find((int) $id);
        if (!$item) redirect('admin/depoimentos');
        $this->adminView('admin/testimonials/form', ['pageTitle' => 'Editar depoimento', 'item' => $item]);
    }

    public function save(): void
    {
        $this->requireCsrf();
        $id      = (int) ($_POST['id'] ?? 0) ?: null;
        $author  = trim($_POST['author'] ?? '');
        $content = trim($_POST['content'] ?? '');
        if ($author === '' || $content === '') {
            flash('error', 'Informe autor e depoimento.');
            redirect($id ? "admin/depoimentos/$id/editar" : 'admin/depoimentos/novo');
        }
        Testimonial::save([
            'author'      => mb_substr($author, 0, 120),
            'author_role' => trim($_POST['author_role'] ?? '') ?: null,
            'content'     => mb_substr($content, 0, 2000),
            'rating'      => min(5, max(1, (int) ($_POST['rating'] ?? 5))),
            'status'      => ($_POST['status'] ?? '') === 'draft' ? 'draft' : 'published',
            'sort_order'  => (int) ($_POST['sort_order'] ?? 0),
        ], $id);
        flash('success', 'Depoimento salvo com sucesso.');
        redirect('admin/depoimentos');
    }

    public function delete(string $id): void
    {
        $this->requireCsrf();
        Testimonial::delete((int) $id);
        flash('success', 'Depoimento excluído.');
        redirect('admin/depoimentos');
    }
}
