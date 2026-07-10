<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\ContactMessage;

class ContactController extends Controller
{
    public function index(): void
    {
        $this->view('contact/index', ['pageTitle' => 'Contato']);
    }

    public function send(): void
    {
        $this->requireCsrf();

        // Honeypot anti-spam
        if (!empty($_POST['website'])) {
            redirect('contato');
        }
        if (ContactMessage::rateLimited()) {
            flash('error', 'Muitas mensagens enviadas. Aguarde alguns minutos e tente novamente.');
            redirect('contato');
        }

        $name    = trim($_POST['name'] ?? '');
        $email   = trim($_POST['email'] ?? '');
        $message = trim($_POST['message'] ?? '');
        $consent = !empty($_POST['lgpd_consent']);

        if ($name === '' || $message === '' || !$consent) {
            $_SESSION['_old'] = $_POST;
            flash('error', 'Preencha nome, mensagem e aceite a política de privacidade.');
            redirect('contato');
        }
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['_old'] = $_POST;
            flash('error', 'Informe um e-mail válido.');
            redirect('contato');
        }

        ContactMessage::create([
            'name'         => mb_substr($name, 0, 160),
            'email'        => mb_substr($email, 0, 190) ?: null,
            'phone'        => mb_substr(trim($_POST['phone'] ?? ''), 0, 30) ?: null,
            'subject'      => mb_substr(trim($_POST['subject'] ?? ''), 0, 190) ?: null,
            'message'      => mb_substr($message, 0, 5000),
            'lgpd_consent' => 1,
        ]);

        unset($_SESSION['_old']);
        flash('success', 'Mensagem enviada com sucesso! Responderemos o mais breve possível.');
        redirect('contato');
    }
}
