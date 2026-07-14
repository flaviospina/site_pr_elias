<?php use App\Core\Csrf; use App\Models\Setting; ?>
<form class="panel form-panel" method="post" action="<?= base_url('admin/notificacoes') ?>">
  <?= Csrf::field() ?>

  <h2>📲 Aviso no celular a cada venda (Telegram)</h2>
  <p class="help-note">
    Receba um aviso instantâneo no seu celular sempre que houver uma nova venda ou um pagamento aprovado —
    cobre <strong>todas</strong> as formas (Mercado Pago, PIX e WhatsApp). É gratuito e leva 2 minutos para configurar:
  </p>
  <ol style="font-size:.9rem;color:var(--muted);line-height:1.8;margin:0 0 18px;padding-left:20px">
    <li>No Telegram, abra uma conversa com <strong>@BotFather</strong>, envie <code>/newbot</code>, escolha um nome e copie o <strong>token</strong> que ele fornece.</li>
    <li>Abra uma conversa com o <strong>seu novo bot</strong> e envie qualquer mensagem (ex.: "oi") — isso autoriza o bot a te enviar mensagens.</li>
    <li>Abra uma conversa com <strong>@userinfobot</strong> e copie o número do seu <strong>Chat ID</strong>.</li>
    <li>Cole os dois campos abaixo, marque "Ativar" e salve — uma mensagem de teste será enviada na hora.</li>
  </ol>

  <label class="check">
    <input type="checkbox" name="telegram_enabled" value="1" <?= Setting::get('telegram_enabled') === '1' ? 'checked' : '' ?>>
    <span>Ativar avisos no Telegram</span>
  </label>
  <div class="form-row">
    <label>Token do bot<input type="text" name="telegram_bot_token" value="<?= e(Setting::get('telegram_bot_token')) ?>" placeholder="123456789:AAE..."></label>
    <label>Chat ID (seu número no Telegram)<input type="text" name="telegram_chat_id" value="<?= e(Setting::get('telegram_chat_id')) ?>" placeholder="123456789"></label>
  </div>

  <div class="form-actions">
    <button class="btn btn-primary" type="submit">Salvar e enviar teste</button>
  </div>
</form>
