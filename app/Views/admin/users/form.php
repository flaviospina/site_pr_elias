<?php use App\Core\Csrf; ?>
<form class="panel form-panel" method="post" action="<?= base_url('admin/usuarios/salvar') ?>">
  <?= Csrf::field() ?>
  <input type="hidden" name="id" value="<?= (int) ($user['id'] ?? 0) ?>">

  <div class="form-row">
    <label>Nome *<input type="text" name="name" value="<?= e($user['name'] ?? '') ?>" required maxlength="120"></label>
    <label>E-mail *<input type="email" name="email" value="<?= e($user['email'] ?? '') ?>" required maxlength="190"></label>
  </div>
  <div class="form-row">
    <label><?= $user ? 'Nova senha (deixe vazio para manter a atual)' : 'Senha * (mín. 8 caracteres)' ?>
      <input type="password" name="password" minlength="8" <?= $user ? '' : 'required' ?> autocomplete="new-password">
    </label>
    <label>Perfil
      <select name="role">
        <option value="admin" <?= ($user['role'] ?? 'admin') === 'admin' ? 'selected' : '' ?>>Administrador (acesso total)</option>
        <option value="editor" <?= ($user['role'] ?? '') === 'editor' ? 'selected' : '' ?>>Editor</option>
      </select>
    </label>
  </div>
  <label class="check"><input type="checkbox" name="active" value="1" <?= !$user || $user['active'] ? 'checked' : '' ?>><span>Conta ativa</span></label>

  <div class="form-actions">
    <a class="btn btn-outline" href="<?= base_url('admin/usuarios') ?>">Cancelar</a>
    <button class="btn btn-primary" type="submit">Salvar usuário</button>
  </div>
</form>
