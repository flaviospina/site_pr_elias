<?php use App\Core\Csrf; ?>
<section class="page-hero">
  <div class="container">
    <h1>Finalizar Compra</h1>
    <div class="hero-trust dark">
      <span>🔒 Ambiente criptografado (SSL)</span>
      <span>✔ Garantia de <?= e($settings['guarantee_days'] ?? '7') ?> dias</span>
      <span>🛡 Dados protegidos — LGPD</span>
    </div>
  </div>
</section>

<section class="section">
  <div class="container checkout-layout">
    <form class="checkout-form" method="post" action="<?= base_url('finalizar-compra') ?>">
      <?= Csrf::field() ?>

      <fieldset>
        <legend>1. Seus dados</legend>
        <label>Nome completo *<input type="text" name="customer_name" value="<?= old('customer_name') ?>" required maxlength="160"></label>
        <div class="form-row">
          <label>E-mail *<input type="email" name="customer_email" value="<?= old('customer_email') ?>" required maxlength="190"></label>
          <label>WhatsApp<input type="text" name="customer_phone" value="<?= old('customer_phone') ?>" maxlength="30" placeholder="(11) 90000-0000"></label>
        </div>
        <label>CPF (para a nota/envio)<input type="text" name="customer_cpf" value="<?= old('customer_cpf') ?>" maxlength="14" placeholder="000.000.000-00"></label>
      </fieldset>

      <fieldset>
        <legend>2. Endereço de entrega</legend>
        <div class="form-row">
          <label>CEP *<input type="text" name="address_zip" value="<?= old('address_zip') ?>" required maxlength="12" placeholder="00000-000"></label>
          <label>Cidade *<input type="text" name="address_city" value="<?= old('address_city') ?>" required maxlength="120"></label>
        </div>
        <div class="form-row">
          <label>Endereço *<input type="text" name="address_street" value="<?= old('address_street') ?>" required maxlength="255"></label>
          <label class="narrow">Número *<input type="text" name="address_number" value="<?= old('address_number') ?>" required maxlength="20"></label>
        </div>
        <div class="form-row">
          <label>Complemento<input type="text" name="address_complement" value="<?= old('address_complement') ?>" maxlength="120"></label>
          <label>Bairro *<input type="text" name="address_district" value="<?= old('address_district') ?>" required maxlength="120"></label>
          <label class="narrow">UF *
            <select name="address_state" required>
              <option value="">--</option>
              <?php foreach (['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'] as $uf): ?>
              <option <?= old('address_state') === $uf ? 'selected' : '' ?>><?= $uf ?></option>
              <?php endforeach; ?>
            </select>
          </label>
        </div>
      </fieldset>

      <fieldset>
        <legend>3. Forma de pagamento</legend>
        <?php if (!$methods): ?>
          <p class="alert alert-error">Nenhuma forma de pagamento configurada. Entre em contato pelo WhatsApp.</p>
        <?php endif; ?>
        <div class="payment-options">
          <?php $first = true; foreach ($methods as $key => $method): ?>
          <label class="payment-option">
            <input type="radio" name="payment_method" value="<?= e($key) ?>" <?= $first ? 'checked' : '' ?> required>
            <span>
              <strong><?= e($method['label']) ?></strong>
              <small><?= e($method['hint']) ?></small>
            </span>
          </label>
          <?php $first = false; endforeach; ?>
        </div>
      </fieldset>

      <label>Observações (opcional)<textarea name="notes" rows="3" maxlength="2000"><?= old('notes') ?></textarea></label>

      <label class="check">
        <input type="checkbox" name="lgpd_consent" value="1" required>
        <span>Li e aceito os <a href="<?= base_url('termos-e-condicoes') ?>" target="_blank">Termos e Condições</a> e a <a href="<?= base_url('politica-de-privacidade') ?>" target="_blank">Política de Privacidade</a>. Meus dados serão usados apenas para processar este pedido (LGPD). *</span>
      </label>

      <button class="btn btn-gold btn-lg btn-block" type="submit">🔒 Confirmar pedido com segurança</button>
      <p class="secure-note">Seus dados trafegam criptografados. Este site não armazena dados de cartão — o pagamento é concluído no ambiente seguro do gateway escolhido.</p>
    </form>

    <aside class="cart-summary">
      <h2>Seu pedido</h2>
      <?php foreach ($items as $item): ?>
      <p class="summary-row">
        <span><?= (int) $item['quantity'] ?>× <?= e($item['book']['title']) ?></span>
        <strong><?= money($item['subtotal']) ?></strong>
      </p>
      <?php endforeach; ?>
      <p class="summary-row"><span>Frete</span><span><?= $shipping > 0 ? money($shipping) : 'combinado após o pedido' ?></span></p>
      <p class="summary-row total"><span>Total</span><strong><?= money($subtotal + $shipping) ?></strong></p>
      <div class="trust-box small">
        <span>🔒 Compra ultrassegura — criptografia SSL</span>
        <span>✔ Garantia incondicional de <?= e($settings['guarantee_days'] ?? '7') ?> dias</span>
        <span>🛡 Conformidade com a LGPD</span>
        <span>🚚 Envio com código de rastreio</span>
      </div>
    </aside>
  </div>
</section>
