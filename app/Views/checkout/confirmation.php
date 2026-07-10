<?php use App\Models\Setting; ?>
<section class="page-hero">
  <div class="container">
    <h1>✅ Pedido recebido!</h1>
    <p>Número do pedido: <strong><?= e($order['order_code']) ?></strong></p>
  </div>
</section>

<section class="section">
  <div class="container article-page">

    <?php if (($gatewayReturn ?? null) === 'success'): ?>
      <div class="alert alert-success">Pagamento recebido pelo gateway! Assim que for confirmado, você receberá o aviso de envio.</div>
    <?php elseif (($gatewayReturn ?? null) === 'failure'): ?>
      <div class="alert alert-error">O pagamento não foi concluído. Você pode tentar novamente ou falar conosco pelo WhatsApp.</div>
    <?php endif; ?>

    <div class="cart-summary" style="max-width:none">
      <h2>Resumo do pedido</h2>
      <?php foreach ($items as $item): ?>
      <p class="summary-row"><span><?= (int) $item['quantity'] ?>× <?= e($item['title']) ?></span><strong><?= money((float) $item['line_total']) ?></strong></p>
      <?php endforeach; ?>
      <?php if ((float) $order['shipping'] > 0): ?>
      <p class="summary-row"><span>Frete</span><span><?= money((float) $order['shipping']) ?></span></p>
      <?php endif; ?>
      <p class="summary-row total"><span>Total</span><strong><?= money((float) $order['total']) ?></strong></p>
    </div>

    <?php if ($order['payment_method'] === 'pix' && Setting::get('pay_pix_key')): ?>
    <div class="article-cta" style="margin-top:32px">
      <h3>💠 Pague com PIX</h3>
      <p>Chave PIX (<?= e(Setting::get('pay_pix_key_type', 'chave')) ?>): <strong style="font-size:1.2rem"><?= e(Setting::get('pay_pix_key')) ?></strong></p>
      <p>Titular: <?= e(Setting::get('pay_pix_holder', '')) ?> · Valor: <strong><?= money((float) $order['total']) ?></strong></p>
      <p>Após o pagamento, envie o comprovante pelo WhatsApp informando o pedido <strong><?= e($order['order_code']) ?></strong>.</p>
      <a class="btn btn-gold" target="_blank" rel="noopener"
         href="https://wa.me/<?= e(Setting::get('whatsapp', '')) ?>?text=<?= rawurlencode('Olá! Segue o comprovante do pedido ' . $order['order_code'] . ' (PIX de ' . money((float) $order['total']) . ').') ?>">
        Enviar comprovante no WhatsApp →</a>
    </div>
    <?php elseif ($order['payment_method'] === 'whatsapp'): ?>
    <div class="article-cta" style="margin-top:32px">
      <h3>📱 Combine o pagamento pelo WhatsApp</h3>
      <p>Clique no botão abaixo — sua mensagem já vai preenchida com o número do pedido.</p>
      <a class="btn btn-gold" target="_blank" rel="noopener"
         href="https://wa.me/<?= e(Setting::get('whatsapp', '')) ?>?text=<?= rawurlencode('Olá! Acabei de fazer o pedido ' . $order['order_code'] . ' no valor de ' . money((float) $order['total']) . ' e gostaria de combinar o pagamento.') ?>">
        Falar no WhatsApp →</a>
    </div>
    <?php else: ?>
    <div class="article-cta" style="margin-top:32px">
      <h3>Acompanhamento</h3>
      <p>Enviamos a confirmação para <strong><?= e($order['customer_email']) ?></strong>. Dúvidas? Fale conosco pelo WhatsApp informando o pedido <strong><?= e($order['order_code']) ?></strong>.</p>
      <a class="btn btn-outline" target="_blank" rel="noopener" href="https://wa.me/<?= e(Setting::get('whatsapp', '')) ?>">Falar no WhatsApp</a>
    </div>
    <?php endif; ?>

    <div class="trust-box" style="margin-top:32px">
      <span>🔒 Compra segura — seus dados estão protegidos</span>
      <span>✔ Garantia de <?= e(Setting::get('guarantee_days', '7')) ?> dias — devolução integral (CDC, art. 49)</span>
      <span>🚚 Envio para todo o Brasil com código de rastreio</span>
    </div>
  </div>
</section>
