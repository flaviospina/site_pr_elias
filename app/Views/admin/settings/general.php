<?php use App\Core\Csrf; use App\Models\Setting; ?>
<form class="panel form-panel" method="post" action="<?= base_url('admin/configuracoes') ?>">
  <?= Csrf::field() ?>

  <h2>Identidade do site</h2>
  <div class="form-row">
    <label>Nome do site<input type="text" name="site_name" value="<?= e(Setting::get('site_name')) ?>"></label>
    <label>Frase/tagline<input type="text" name="site_tagline" value="<?= e(Setting::get('site_tagline')) ?>"></label>
  </div>
  <label>Logo (URL)<input type="text" name="site_logo" value="<?= e(Setting::get('site_logo')) ?>" data-upload-target></label>
  <p><input type="file" accept="image/*" data-upload-input> <span data-upload-status></span></p>
  <label>Barra de aviso (topo do site)<input type="text" name="announcement_bar" value="<?= e(Setting::get('announcement_bar')) ?>"></label>

  <h2>Página inicial</h2>
  <label>Título principal (hero)<input type="text" name="hero_title" value="<?= e(Setting::get('hero_title')) ?>"></label>
  <label>Subtítulo (hero)<textarea name="hero_subtitle" rows="2"><?= e(Setting::get('hero_subtitle')) ?></textarea></label>
  <div class="form-row">
    <label>Imagem do hero (URL)<input type="text" name="hero_image" value="<?= e(Setting::get('hero_image')) ?>"></label>
    <label>Foto "Sobre" (URL)<input type="text" name="about_image" value="<?= e(Setting::get('about_image')) ?>"></label>
  </div>
  <label>Versículo do rodapé<input type="text" name="footer_verse" value="<?= e(Setting::get('footer_verse')) ?>"></label>

  <h2>Contato</h2>
  <div class="form-row">
    <label>WhatsApp (só números, com 55)<input type="text" name="whatsapp" value="<?= e(Setting::get('whatsapp')) ?>" placeholder="5511932065151"></label>
    <label>WhatsApp (exibição)<input type="text" name="whatsapp_display" value="<?= e(Setting::get('whatsapp_display')) ?>" placeholder="(11) 93206-5151"></label>
  </div>
  <div class="form-row">
    <label>E-mail de contato<input type="email" name="contact_email" value="<?= e(Setting::get('contact_email')) ?>"></label>
    <label>Horário de atendimento<input type="text" name="attendance_hours" value="<?= e(Setting::get('attendance_hours')) ?>"></label>
  </div>
  <div class="form-row">
    <label>Instagram (URL)<input type="text" name="instagram_url" value="<?= e(Setting::get('instagram_url')) ?>"></label>
    <label>Facebook (URL)<input type="text" name="facebook_url" value="<?= e(Setting::get('facebook_url')) ?>"></label>
    <label>YouTube (URL)<input type="text" name="youtube_url" value="<?= e(Setting::get('youtube_url')) ?>"></label>
  </div>

  <h2>Loja</h2>
  <div class="form-row">
    <label>Dias de garantia<input type="number" name="guarantee_days" value="<?= e(Setting::get('guarantee_days')) ?>"></label>
    <label>Frete fixo (R$) — 0 combina após o pedido<input type="text" name="shipping_flat" value="<?= e(Setting::get('shipping_flat')) ?>"></label>
    <label>Google Tag Manager ID (opcional)<input type="text" name="gtm_id" value="<?= e(Setting::get('gtm_id')) ?>" placeholder="GTM-XXXXXX"></label>
  </div>

  <label class="check"><input type="checkbox" name="cookie_banner_enabled" value="1" <?= Setting::get('cookie_banner_enabled') === '1' ? 'checked' : '' ?>><span>Exibir banner de cookies (LGPD)</span></label>

  <div class="form-actions">
    <button class="btn btn-primary" type="submit">Salvar configurações</button>
  </div>
</form>
