<?php
namespace App\Controllers\Admin;

use App\Models\ContactMessage;
use App\Models\Subscriber;

class MessageController extends AdminController
{
    public function index(): void
    {
        $this->adminView('admin/messages/index', [
            'pageTitle' => 'Mensagens',
            'messages'  => ContactMessage::allForAdmin(),
        ]);
    }

    public function markRead(string $id): void
    {
        $this->requireCsrf();
        ContactMessage::markRead((int) $id);
        redirect('admin/mensagens');
    }

    public function delete(string $id): void
    {
        $this->requireCsrf();
        ContactMessage::delete((int) $id);
        flash('success', 'Mensagem excluída.');
        redirect('admin/mensagens');
    }

    public function subscribers(): void
    {
        $this->adminView('admin/messages/subscribers', [
            'pageTitle'   => 'Inscritos nos Devocionais',
            'subscribers' => Subscriber::allForAdmin(),
        ]);
    }

    public function deleteSubscriber(string $id): void
    {
        $this->requireCsrf();
        Subscriber::delete((int) $id);
        flash('success', 'Inscrito removido.');
        redirect('admin/inscritos');
    }
}
