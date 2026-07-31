<?php use App\Core\Csrf; ?>
<?php render_banner('contato', [
    'title'    => 'Contato',
    'subtitle' => 'Estamos à disposição para orar com você, tirar dúvidas ou conversar sobre os trabalhos do ministério.',
]); ?>

<section class="section">
  <div class="container contact-grid">
    <div class="contact-cards">
      <div class="contact-card">
        <span class="contact-icon">📱</span>
        <h3>WhatsApp</h3>
        <p class="contact-highlight"><?= e($settings['whatsapp_display'] ?? '') ?></p>
        <p>Atendimento exclusivo via WhatsApp. Envie sua mensagem e responderemos o mais breve possível.</p>
        <a class="btn btn-primary" href="https://wa.me/<?= e($settings['whatsapp'] ?? '') ?>" target="_blank" rel="noopener">Conversar no WhatsApp →</a>
      </div>
      <div class="contact-card">
        <span class="contact-icon">🕗</span>
        <h3>Horário de atendimento</h3>
        <p class="contact-highlight">08h — 18h</p>
        <p><?= e($settings['attendance_hours'] ?? '') ?>. Fora deste horário, sua mensagem será respondida assim que possível.</p>
      </div>
      <div class="contact-card">
        <span class="contact-icon">✉️</span>
        <h3>E-mail</h3>
        <p class="contact-highlight" style="font-size:1.1rem"><?= e($settings['contact_email'] ?? '') ?></p>
      </div>
    </div>

    <form class="contact-form" method="post" action="<?= base_url('contato') ?>">
      <?= Csrf::field() ?>
      <input type="text" name="website" class="hp-field" tabindex="-1" autocomplete="off" aria-hidden="true">
      <h2>Envie uma mensagem</h2>
      <label>Nome *<input type="text" name="name" value="<?= old('name') ?>" required maxlength="160"></label>
      <div class="form-row">
        <label>E-mail<input type="email" name="email" value="<?= old('email') ?>" maxlength="190"></label>
        <label>Telefone/WhatsApp<input type="text" name="phone" value="<?= old('phone') ?>" maxlength="30"></label>
      </div>
      <label>Assunto
        <select name="subject">
          <option>Pedido de oração</option>
          <option>Convite para pregação</option>
          <option>Dúvida sobre os livros</option>
          <option>Outro assunto</option>
        </select>
      </label>
      <label>Mensagem *<textarea name="message" rows="6" required maxlength="5000"><?= old('message') ?></textarea></label>
      <label class="check">
        <input type="checkbox" name="lgpd_consent" value="1" required>
        <span>Autorizo o uso dos meus dados para responder a esta mensagem, conforme a <a href="<?= base_url('politica-de-privacidade') ?>" target="_blank">Política de Privacidade</a> (LGPD). *</span>
      </label>
      <button class="btn btn-gold btn-lg" type="submit">Enviar mensagem</button>
    </form>
  </div>
</section>
