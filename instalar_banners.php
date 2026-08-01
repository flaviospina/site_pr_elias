<?php
/**
 * INSTALADOR DOS BANNERS — recria as pastas/arquivos das views de banner
 * com o conteúdo correto, sem depender de criar subpastas por FTP.
 *
 * COMO USAR:
 *   1. Envie SÓ este arquivo para a raiz do site (site_new/instalar_banners.php).
 *   2. Abra no navegador: https://eliasjosedasilva.com.br/site_new/instalar_banners.php
 *   3. Confira os ✅ e APAGUE este arquivo do servidor.
 */
header('Content-Type: text/plain; charset=utf-8');
$base = __DIR__;
$files = [];
$files['app/Views/admin/banners/index.php'] = <<<'ARQ_EOF'
<div class="panel">
  <div class="panel-head">
    <h2>Banners das páginas</h2>
  </div>
  <p class="help-note">Cada página do site tem um banner no topo (logo abaixo do menu). Clique em "Configurar" para trocar a imagem de fundo, o texto, os botões e o estilo. Se não configurar, a página exibe o texto padrão.</p>
  <div class="table-wrap">
  <table class="admin-table">
    <thead><tr><th>Página</th><th>Prévia</th><th>Título atual</th><th>Situação</th><th class="actions">Ações</th></tr></thead>
    <tbody>
      <?php foreach ($banners as $item): $d = $item['data']; ?>
      <tr>
        <td><strong><?= e($item['label']) ?></strong></td>
        <td><?php if (!empty($d['image'])): ?><img class="thumb" style="width:80px;height:44px;object-fit:cover" src="<?= e($d['image']) ?>" alt=""><?php else: ?><span style="color:var(--muted)">—</span><?php endif; ?></td>
        <td><?= e($d['title'] ?? '') ?: '<span style="color:var(--muted)">(padrão da página)</span>' ?></td>
        <td>
          <?php if (!$d): ?><span class="status status-draft">não configurado</span>
          <?php elseif ((int) $d['enabled'] === 1): ?><span class="status status-published">ativo</span>
          <?php else: ?><span class="status status-cancelled">desativado</span><?php endif; ?>
        </td>
        <td class="actions">
          <a class="btn btn-primary btn-sm" href="<?= base_url('admin/banners/' . e($item['location']) . '/editar') ?>">Configurar</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  </div>
</div>

ARQ_EOF;
$files['app/Views/admin/banners/form.php'] = <<<'ARQ_EOF'
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

ARQ_EOF;
$files['app/Views/partials/banner.php'] = <<<'ARQ_EOF'
<?php
/**
 * Renderiza o banner do topo de uma página.
 * Variáveis esperadas (preparadas pelo helper render_banner):
 *   $b = [image,title,subtitle,kicker,button_text,button_url,button2_text,
 *         button2_url,overlay,text_color,align,height,trust]
 */
$hasImage = !empty($b['image']);
$op    = max(0, min(100, (int) ($b['overlay'] ?? 70))) / 100;
$style = $hasImage
    ? "background-image:linear-gradient(rgba(10,37,64,{$op}),rgba(10,37,64,{$op})),url('" . e($b['image']) . "');"
    : '';
$classes = 'site-banner '
    . 'banner-h-' . e($b['height'] ?? 'medium') . ' '
    . 'banner-align-' . e($b['align'] ?? 'center') . ' '
    . 'banner-text-' . e($b['text_color'] ?? 'light') . ' '
    . ($hasImage ? 'banner-has-image' : 'banner-plain');
?>
<section class="<?= $classes ?>"<?= $style ? ' style="' . $style . '"' : '' ?>>
  <div class="container site-banner-inner">
    <?php if (!empty($b['kicker'])): ?><p class="hero-kicker"><?= e($b['kicker']) ?></p><?php endif; ?>
    <?php if (!empty($b['title'])): ?><h1><?= e($b['title']) ?></h1><?php endif; ?>
    <?php if (!empty($b['subtitle'])): ?><p class="hero-subtitle"><?= e($b['subtitle']) ?></p><?php endif; ?>

    <?php if (!empty($b['button_text']) || !empty($b['button2_text'])): ?>
    <div class="hero-actions">
      <?php if (!empty($b['button_text'])): ?>
        <a class="btn btn-gold btn-lg" href="<?= e($b['button_url'] ?: '#') ?>"><?= e($b['button_text']) ?></a>
      <?php endif; ?>
      <?php if (!empty($b['button2_text'])): ?>
        <a class="btn <?= $hasImage ? 'btn-ghost' : 'btn-outline' ?> btn-lg" href="<?= e($b['button2_url'] ?: '#') ?>"><?= e($b['button2_text']) ?></a>
      <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($b['trust'])): ?>
    <div class="hero-trust<?= $hasImage ? '' : ' dark' ?>">
      <?php foreach ($b['trust'] as $t): ?><span><?= e($t) ?></span><?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>

ARQ_EOF;

$ok = 0; $fail = 0;
foreach ($files as $rel => $content) {
    $full = $base . '/' . $rel;
    $dir  = dirname($full);
    if (!is_dir($dir)) { @mkdir($dir, 0755, true); }
    $bytes = @file_put_contents($full, $content);
    if ($bytes !== false) { echo "✅ Criado: $rel ($bytes bytes)\n"; $ok++; }
    else { echo "❌ FALHA ao criar: $rel — verifique permissões da pasta\n"; $fail++; }
}
echo "\n";
foreach (['app/Models/Banner.php','app/Controllers/Admin/BannerController.php'] as $req) {
    echo (is_file($base.'/'.$req) ? "✅ Existe: $req\n" : "⚠️  FALTA (envie por FTP): $req\n");
}
echo "\n======================================\n";
echo ($fail === 0 ? "CONCLUÍDO! Agora rode a migração 003_banners.sql no phpMyAdmin (se ainda não rodou),\nacesse Painel > Banners e APAGUE este arquivo (instalar_banners.php)." : "Houve falhas — veja acima.");
echo "\n======================================\n";
