<?php
namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Models\User;

class UserController extends AdminController
{
    public function index(): void
    {
        $this->adminView('admin/users/index', [
            'pageTitle' => 'Usuários',
            'users'     => User::all(),
        ]);
    }

    public function create(): void
    {
        $this->adminView('admin/users/form', ['pageTitle' => 'Novo usuário', 'user' => null]);
    }

    public function edit(string $id): void
    {
        $user = User::find((int) $id);
        if (!$user) redirect('admin/usuarios');
        $this->adminView('admin/users/form', ['pageTitle' => 'Editar usuário', 'user' => $user]);
    }

    public function save(): void
    {
        $this->requireCsrf();
        $id    = (int) ($_POST['id'] ?? 0) ?: null;
        $name  = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Informe nome e e-mail válidos.');
            redirect($id ? "admin/usuarios/$id/editar" : 'admin/usuarios/novo');
        }
        if (!$id && strlen($password) < 8) {
            flash('error', 'A senha deve ter no mínimo 8 caracteres.');
            redirect('admin/usuarios/novo');
        }
        if ($id && $password !== '' && strlen($password) < 8) {
            flash('error', 'A nova senha deve ter no mínimo 8 caracteres.');
            redirect("admin/usuarios/$id/editar");
        }

        $existing = User::findByEmail($email);
        if ($existing && (int) $existing['id'] !== $id) {
            flash('error', 'Já existe um usuário com este e-mail.');
            redirect($id ? "admin/usuarios/$id/editar" : 'admin/usuarios/novo');
        }

        User::save([
            'name'     => mb_substr($name, 0, 120),
            'email'    => mb_substr($email, 0, 190),
            'role'     => ($_POST['role'] ?? '') === 'editor' ? 'editor' : 'admin',
            // Não permite desativar a própria conta
            'active'   => ($id === Auth::id()) ? 1 : (!empty($_POST['active']) ? 1 : 0),
            'password' => $password,
        ], $id);

        flash('success', 'Usuário salvo com sucesso.');
        redirect('admin/usuarios');
    }

    public function delete(string $id): void
    {
        $this->requireCsrf();
        $id = (int) $id;
        if ($id === Auth::id()) {
            flash('error', 'Você não pode excluir a sua própria conta.');
            redirect('admin/usuarios');
        }
        if (User::count() <= 1) {
            flash('error', 'Não é possível excluir o último usuário.');
            redirect('admin/usuarios');
        }
        User::delete($id);
        flash('success', 'Usuário excluído.');
        redirect('admin/usuarios');
    }
}
