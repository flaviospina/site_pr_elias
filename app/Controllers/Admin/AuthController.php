<?php
namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;

class AuthController extends Controller
{
    public function form(): void
    {
        if (Auth::check()) {
            redirect('admin');
        }
        $this->view('admin/login', ['pageTitle' => 'Acesso ao Painel'], 'blank');
    }

    public function login(): void
    {
        $this->requireCsrf();
        if (Auth::tooManyAttempts()) {
            flash('error', 'Muitas tentativas de acesso. Aguarde 15 minutos.');
            redirect('admin/login');
        }
        $ok = Auth::attempt(trim($_POST['email'] ?? ''), $_POST['password'] ?? '');
        if (!$ok) {
            flash('error', 'E-mail ou senha inválidos.');
            redirect('admin/login');
        }
        redirect('admin');
    }

    public function logout(): void
    {
        $this->requireCsrf();
        Auth::logout();
        redirect('admin/login');
    }
}
