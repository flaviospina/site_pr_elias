<?php
namespace App\Controllers\Admin;

use App\Models\Event;

class EventController extends AdminController
{
    public function index(): void
    {
        $this->adminView('admin/events/index', [
            'pageTitle' => 'Agenda',
            'events'    => Event::allForAdmin(),
        ]);
    }

    public function create(): void
    {
        $this->adminView('admin/events/form', ['pageTitle' => 'Novo evento', 'event' => null]);
    }

    public function edit(string $id): void
    {
        $event = Event::find((int) $id);
        if (!$event) redirect('admin/agenda');
        $this->adminView('admin/events/form', ['pageTitle' => 'Editar evento', 'event' => $event]);
    }

    public function save(): void
    {
        $this->requireCsrf();
        $id    = (int) ($_POST['id'] ?? 0) ?: null;
        $title = trim($_POST['title'] ?? '');
        $date  = trim($_POST['event_date'] ?? '');
        if ($title === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            flash('error', 'Informe título e data do evento.');
            redirect($id ? "admin/agenda/$id/editar" : 'admin/agenda/novo');
        }
        Event::save([
            'title'       => mb_substr($title, 0, 255),
            'description' => trim($_POST['description'] ?? '') ?: null,
            'location'    => trim($_POST['location'] ?? '') ?: null,
            'city'        => trim($_POST['city'] ?? '') ?: null,
            'event_date'  => $date,
            'event_time'  => trim($_POST['event_time'] ?? '') ?: null,
            'link'        => trim($_POST['link'] ?? '') ?: null,
            'status'      => ($_POST['status'] ?? '') === 'draft' ? 'draft' : 'published',
        ], $id);
        flash('success', 'Evento salvo com sucesso.');
        redirect('admin/agenda');
    }

    public function delete(string $id): void
    {
        $this->requireCsrf();
        Event::delete((int) $id);
        flash('success', 'Evento excluído.');
        redirect('admin/agenda');
    }
}
