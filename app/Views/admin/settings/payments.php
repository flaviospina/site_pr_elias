<?php use App\Core\Csrf; use App\Models\Setting; ?>
<form class="panel form-panel" method="post" action="<?= base_url('admin/pagamentos') ?>">
  <?= Csrf::field() ?>

  <p class="help-note">🔒 As credenciais ficam salvas apenas no seu banco de dados. Marque a opção "sandbox" para testar antes de vender de verdade. Ao lado de cada gateway há o link para obter as chaves.</p>

  <!-- MERCADO PAGO -->
  <fieldset class="pay-block">
    <legend>Mercado Pago <small>(cartão, PIX e boleto)</small></legend>
    <label class="check"><input type="checkbox" name="pay_mercadopago_enabled" value="1" <?= Setting::get('pay_mercadopago_enabled') === '1' ? 'checked' : '' ?>><span>Ativar Mercado Pago</span></label>
    <div class="form-row">
      <label>Public Key<input type="text" name="pay_mercadopago_public_key" value="<?= e(Setting::get('pay_mercadopago_public_key')) ?>"></label>
      <label>Access Token<input type="password" name="pay_mercadopago_access_token" value="<?= e(Setting::get('pay_mercadopago_access_token')) ?>"></label>
    </div>
    <label class="check"><input type="checkbox" name="pay_mercadopago_sandbox" value="1" <?= Setting::get('pay_mercadopago_sandbox') === '1' ? 'checked' : '' ?>><span>Modo sandbox (teste)</span></label>
    <p><small>Obtenha as credenciais em: <strong>mercadopago.com.br → Seu negócio → Configurações → Credenciais</strong>.</small></p>
  </fieldset>

  <!-- PIX DIRETO -->
  <fieldset class="pay-block">
    <legend>PIX direto <small>(sem taxas de gateway)</small></legend>
    <label class="check"><input type="checkbox" name="pay_pix_enabled" value="1" <?= Setting::get('pay_pix_enabled') === '1' ? 'checked' : '' ?>><span>Ativar PIX direto</span></label>
    <div class="form-row">
      <label>Chave PIX<input type="text" name="pay_pix_key" value="<?= e(Setting::get('pay_pix_key')) ?>"></label>
      <label>Tipo da chave
        <select name="pay_pix_key_type">
          <?php foreach (['celular','cpf','cnpj','email','aleatoria'] as $t): ?>
          <option <?= Setting::get('pay_pix_key_type') === $t ? 'selected' : '' ?>><?= $t ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>Titular<input type="text" name="pay_pix_holder" value="<?= e(Setting::get('pay_pix_holder')) ?>"></label>
    </div>
    <p><small>O cliente recebe a chave na tela de confirmação e envia o comprovante pelo WhatsApp.</small></p>
  </fieldset>

  <!-- PAGBANK / PAGSEGURO -->
  <fieldset class="pay-block">
    <legend>PagBank (PagSeguro)</legend>
    <label class="check"><input type="checkbox" name="pay_pagseguro_enabled" value="1" <?= Setting::get('pay_pagseguro_enabled') === '1' ? 'checked' : '' ?>><span>Ativar PagBank</span></label>
    <div class="form-row">
      <label>E-mail da conta<input type="email" name="pay_pagseguro_email" value="<?= e(Setting::get('pay_pagseguro_email')) ?>"></label>
      <label>Token<input type="password" name="pay_pagseguro_token" value="<?= e(Setting::get('pay_pagseguro_token')) ?>"></label>
    </div>
    <label class="check"><input type="checkbox" name="pay_pagseguro_sandbox" value="1" <?= Setting::get('pay_pagseguro_sandbox') === '1' ? 'checked' : '' ?>><span>Modo sandbox (teste)</span></label>
  </fieldset>

  <!-- PAYPAL -->
  <fieldset class="pay-block">
    <legend>PayPal</legend>
    <label class="check"><input type="checkbox" name="pay_paypal_enabled" value="1" <?= Setting::get('pay_paypal_enabled') === '1' ? 'checked' : '' ?>><span>Ativar PayPal</span></label>
    <div class="form-row">
      <label>Client ID<input type="text" name="pay_paypal_client_id" value="<?= e(Setting::get('pay_paypal_client_id')) ?>"></label>
      <label>Secret<input type="password" name="pay_paypal_secret" value="<?= e(Setting::get('pay_paypal_secret')) ?>"></label>
    </div>
    <label class="check"><input type="checkbox" name="pay_paypal_sandbox" value="1" <?= Setting::get('pay_paypal_sandbox') === '1' ? 'checked' : '' ?>><span>Modo sandbox (teste)</span></label>
  </fieldset>

  <!-- WHATSAPP -->
  <fieldset class="pay-block">
    <legend>Combinar pelo WhatsApp</legend>
    <label class="check"><input type="checkbox" name="pay_whatsapp_enabled" value="1" <?= Setting::get('pay_whatsapp_enabled') === '1' ? 'checked' : '' ?>><span>Permitir finalizar e combinar o pagamento pelo WhatsApp</span></label>
    <p><small>Opção sempre disponível como alternativa simples, sem necessidade de gateway.</small></p>
  </fieldset>

  <div class="form-actions">
    <button class="btn btn-primary" type="submit">Salvar formas de pagamento</button>
  </div>
</form>
