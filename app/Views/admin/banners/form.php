<?php use App\Core\Csrf; $b = $banner ?? []; ?>
<form class="panel form-panel" method="post" action="<?= base_url('admin/banners/salvar') ?>">
  <?= Csrf::field() ?>
  <input type="hidden" name="location" value="<?= e($location) ?>">

  <p class="help-note">Configurando o banner da página: <strong><?= e($label) ?></strong>. Deixe um campo vazio para usar o padrão da página. A prévia abaixo atualiza conforme você edita.</p>

  <label class="check">
    <input type="checkbox" name="enabled" value="1" <?= (!$b || (int) ($b['enabled'] ?? 1) === 1) ? 'checked' : '' ?> data-bn="enabled">
    <span>Banner ativo (exibir esta configuração no site)</span>
  </label>

  <h2>Imagem de fundo</h2>
  <label>URL da imagem — envie pelo botão abaixo ou cole uma URL
    <input type="text" name="image" value="<?= e($b['image'] ?? '') ?>" data-upload-target data-bn="image" placeholder="deixe vazio para fundo azul sólido">
  </label>
  <p><input type="file" accept="image/*" data-upload-input> <span data-upload-status></span>
     <small style="display:block;color:var(--muted)">Recomendado: imagem larga (ex.: 1920×700), boa qualidade.</small></p>

  <h2>Textos</h2>
  <label>Título (frente do banner)<input type="text" name="title" value="<?= e($b['title'] ?? '') ?>" data-bn="title" maxlength="255"></label>
  <label>Subtítulo<textarea name="subtitle" rows="2" data-bn="subtitle" maxlength="500"><?= e($b['subtitle'] ?? '') ?></textarea></label>

  <h2>Botões de ação (CTA)</h2>
  <div class="form-row">
    <label>Texto do botão 1<input type="text" name="button_text" value="<?= e($b['button_text'] ?? '') ?>" data-bn="button_text" placeholder="Ex.: Conhecer os livros"></label>
    <label>Link do botão 1<input type="text" name="button_url" value="<?= e($b['button_url'] ?? '') ?>" placeholder="Ex.: <?= e(base_url('livros')) ?>"></label>
  </div>
  <div class="form-row">
    <label>Texto do botão 2 (opcional)<input type="text" name="button2_text" value="<?= e($b['button2_text'] ?? '') ?>" data-bn="button2_text"></label>
    <label>Link do botão 2<input type="text" name="button2_url" value="<?= e($b['button2_url'] ?? '') ?>"></label>
  </div>

  <h2>Estilo</h2>
  <div class="form-row">
    <label>Escurecimento da imagem (0 a 100)
      <input type="range" min="0" max="100" name="overlay" value="<?= e((string) ($b['overlay'] ?? 70)) ?>" data-bn="overlay" oninput="this.nextElementSibling.textContent=this.value+'%'">
      <span style="font-size:.8rem;color:var(--muted)"><?= e((string) ($b['overlay'] ?? 70)) ?>%</span>
    </label>
    <label>Altura do banner
      <select name="height" data-bn="height">
        <?php foreach (['small'=>'Baixo','medium'=>'Médio','large'=>'Alto'] as $v => $t): ?>
        <option value="<?= $v ?>" <?= ($b['height'] ?? 'medium') === $v ? 'selected' : '' ?>><?= $t ?></option>
        <?php endforeach; ?>
      </select>
    </label>
  </div>
  <div class="form-row">
    <label>Cor do texto
      <select name="text_color" data-bn="text_color">
        <option value="light" <?= ($b['text_color'] ?? 'light') === 'light' ? 'selected' : '' ?>>Claro (para fundo escuro)</option>
        <option value="dark" <?= ($b['text_color'] ?? '') === 'dark' ? 'selected' : '' ?>>Escuro (para fundo claro)</option>
      </select>
    </label>
    <label>Alinhamento do texto
      <select name="align" data-bn="align">
        <option value="center" <?= ($b['align'] ?? 'center') === 'center' ? 'selected' : '' ?>>Centralizado</option>
        <option value="left" <?= ($b['align'] ?? '') === 'left' ? 'selected' : '' ?>>À esquerda</option>
      </select>
    </label>
  </div>

  <h2>Prévia</h2>
  <div class="banner-preview" data-bn-preview>
    <div class="bnp-overlay" data-bn-overlay></div>
    <div class="bnp-content" data-bn-content>
      <h3 data-bn-view="title"><?= e($b['title'] ?? '') ?></h3>
      <p data-bn-view="subtitle"><?= e($b['subtitle'] ?? '') ?></p>
      <div class="bnp-buttons">
        <span class="btn btn-gold btn-sm" data-bn-view="button_text" <?= empty($b['button_text']) ? 'hidden' : '' ?>><?= e($b['button_text'] ?? '') ?></span>
        <span class="btn btn-outline btn-sm" data-bn-view="button2_text" <?= empty($b['button2_text']) ? 'hidden' : '' ?>><?= e($b['button2_text'] ?? '') ?></span>
      </div>
    </div>
  </div>

  <div class="form-actions">
    <a class="btn btn-outline" href="<?= base_url('admin/banners') ?>">Cancelar</a>
    <button class="btn btn-primary" type="submit">Salvar banner</button>
  </div>
</form>
