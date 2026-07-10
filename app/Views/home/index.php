<?php use App\Core\Cart; use App\Core\Csrf; ?>

<!-- HERO -->
<section class="hero" style="background-image:linear-gradient(rgba(10,37,64,.78),rgba(10,37,64,.88)),url('<?= e($settings['hero_image'] ?? '') ?>')">
  <div class="container hero-inner">
    <p class="hero-kicker"><?= e($settings['site_tagline'] ?? '') ?></p>
    <h1><?= e($settings['hero_title'] ?? '') ?></h1>
    <p class="hero-subtitle"><?= e($settings['hero_subtitle'] ?? '') ?></p>
    <div class="hero-actions">
      <a class="btn btn-gold btn-lg" href="<?= base_url('livros') ?>">📚 Conhecer os livros</a>
      <a class="btn btn-ghost btn-lg" href="<?= base_url('sermoes') ?>">Ler os sermões</a>
    </div>
    <div class="hero-trust">
      <span>🔒 Compra 100% segura</span>
      <span>✔ Garantia de <?= e($settings['guarantee_days'] ?? '7') ?> dias</span>
      <span>🚚 Envio para todo o Brasil</span>
    </div>
  </div>
</section>

<!-- LIVROS EM DESTAQUE -->
<?php if ($books): ?>
<section class="section">
  <div class="container">
    <div class="section-head">
      <p class="section-kicker">Livraria</p>
      <h2>Livros que edificam a sua fé</h2>
      <p class="section-lead">Obras escritas pelo Pr. Elias para fortalecer a sua caminhada com Deus.</p>
    </div>
    <div class="grid grid-3">
      <?php foreach ($books as $book): ?>
      <article class="book-card">
        <a href="<?= base_url('livros/' . $book['slug']) ?>" class="book-cover">
          <?php if ($book['badge']): ?><span class="badge"><?= e($book['badge']) ?></span><?php endif; ?>
          <img src="<?= e($book['image']) ?>" alt="Capa do livro <?= e($book['title']) ?>" loading="lazy">
        </a>
        <div class="book-info">
          <h3><a href="<?= base_url('livros/' . $book['slug']) ?>"><?= e($book['title']) ?></a></h3>
          <p><?= e($book['excerpt']) ?></p>
          <div class="book-buy">
            <span class="price">
              <?php $sale = $book['sale_price'] !== null && (float)$book['sale_price'] > 0 && (float)$book['sale_price'] < (float)$book['price']; ?>
              <?php if ($sale): ?><s><?= money((float) $book['price']) ?></s> <strong><?= money((float) $book['sale_price']) ?></strong>
              <?php else: ?><strong><?= money((float) $book['price']) ?></strong><?php endif; ?>
            </span>
            <form method="post" action="<?= base_url('carrinho/adicionar') ?>">
              <?= Csrf::field() ?>
              <input type="hidden" name="book_id" value="<?= (int) $book['id'] ?>">
              <button class="btn btn-primary" type="submit">Comprar</button>
            </form>
          </div>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
    <p class="section-more"><a class="btn btn-outline" href="<?= base_url('livros') ?>">Ver todos os livros →</a></p>
  </div>
</section>
<?php endif; ?>

<!-- SOBRE -->
<section class="section section-alt">
  <div class="container about-strip">
    <div class="about-photo">
      <img src="<?= e($settings['about_image'] ?? '') ?>" alt="Pr. Elias José da Silva" loading="lazy">
    </div>
    <div class="about-text">
      <p class="section-kicker">Sobre o pastor</p>
      <h2>Pr. Elias José da Silva</h2>
      <p>Educador Cristão, Pastor Evangélico e Escritor, dedicando sua vida ao ensino das Escrituras, à formação de líderes e ao fortalecimento da fé cristã por meio da pregação, do discipulado e da produção literária.</p>
      <p>Seu ministério é marcado pelo compromisso com a fidelidade bíblica, pela valorização da educação cristã e pela defesa da fé.</p>
      <a class="btn btn-outline" href="<?= base_url('sobre-o-pr') ?>">Conhecer a trajetória →</a>
    </div>
  </div>
</section>

<!-- SERMÕES -->
<?php if ($sermons): ?>
<section class="section">
  <div class="container">
    <div class="section-head">
      <p class="section-kicker">Pregação</p>
      <h2>Sermões recentes</h2>
    </div>
    <div class="grid grid-3">
      <?php foreach ($sermons as $sermon): ?>
      <article class="content-card">
        <p class="content-card-meta"><?= e($sermon['bible_reference'] ?: date_br($sermon['published_at'])) ?></p>
        <h3><a href="<?= base_url('sermoes/' . $sermon['slug']) ?>"><?= e($sermon['title']) ?></a></h3>
        <p><?= e($sermon['excerpt']) ?></p>
        <a class="card-link" href="<?= base_url('sermoes/' . $sermon['slug']) ?>">Ler sermão →</a>
      </article>
      <?php endforeach; ?>
    </div>
    <p class="section-more"><a class="btn btn-outline" href="<?= base_url('sermoes') ?>">Ver todos os sermões →</a></p>
  </div>
</section>
<?php endif; ?>

<!-- DEVOCIONAIS + NEWSLETTER -->
<section class="section section-navy">
  <div class="container">
    <div class="section-head light">
      <p class="section-kicker">Alimento diário</p>
      <h2>Devocionais</h2>
      <p class="section-lead">Reflexões bíblicas para o seu dia a dia com Deus.</p>
    </div>
    <div class="grid grid-3">
      <?php foreach ($devotionals as $dev): ?>
      <article class="content-card dark">
        <p class="content-card-meta"><?= e($dev['bible_reference'] ?: date_br($dev['published_at'])) ?></p>
        <h3><a href="<?= base_url('devocionais/' . $dev['slug']) ?>"><?= e($dev['title']) ?></a></h3>
        <p><?= e($dev['excerpt']) ?></p>
        <a class="card-link" href="<?= base_url('devocionais/' . $dev['slug']) ?>">Ler devocional →</a>
      </article>
      <?php endforeach; ?>
    </div>
    <form class="newsletter" method="post" action="<?= base_url('devocionais/inscrever') ?>">
      <?= Csrf::field() ?>
      <input type="hidden" name="_back" value="/">
      <input type="text" name="website" class="hp-field" tabindex="-1" autocomplete="off" aria-hidden="true">
      <h3>Receba os devocionais por e-mail</h3>
      <div class="newsletter-fields">
        <input type="text" name="name" placeholder="Seu nome">
        <input type="email" name="email" placeholder="Seu melhor e-mail" required>
        <button class="btn btn-gold" type="submit">Quero receber</button>
      </div>
      <small>Ao se inscrever, você concorda com a nossa <a href="<?= base_url('politica-de-privacidade') ?>">Política de Privacidade</a> (LGPD). Cancele quando quiser.</small>
    </form>
  </div>
</section>

<!-- AGENDA -->
<?php if ($events): ?>
<section class="section">
  <div class="container">
    <div class="section-head">
      <p class="section-kicker">Agenda</p>
      <h2>Próximos compromissos</h2>
    </div>
    <div class="event-list">
      <?php foreach ($events as $event): ?>
      <article class="event-row">
        <div class="event-date">
          <strong><?= (int) date('d', strtotime($event['event_date'])) ?></strong>
          <span><?= month_br($event['event_date']) ?></span>
        </div>
        <div class="event-info">
          <h3><?= e($event['title']) ?></h3>
          <p><?= e(trim(($event['location'] ? $event['location'] . ' — ' : '') . ($event['city'] ?? ''))) ?><?= $event['event_time'] ? ' · ' . e($event['event_time']) : '' ?></p>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
    <p class="section-more"><a class="btn btn-outline" href="<?= base_url('agenda') ?>">Ver agenda completa →</a></p>
  </div>
</section>
<?php endif; ?>

<!-- DEPOIMENTOS -->
<?php if ($testimonials): ?>
<section class="section section-alt">
  <div class="container">
    <div class="section-head">
      <p class="section-kicker">Depoimentos</p>
      <h2>O que dizem os leitores</h2>
    </div>
    <div class="grid grid-3">
      <?php foreach ($testimonials as $t): ?>
      <blockquote class="testimonial">
        <div class="stars" aria-label="<?= (int) $t['rating'] ?> de 5 estrelas"><?= str_repeat('★', (int) $t['rating']) . str_repeat('☆', 5 - (int) $t['rating']) ?></div>
        <p>“<?= e($t['content']) ?>”</p>
        <footer><strong><?= e($t['author']) ?></strong><?= $t['author_role'] ? ' · ' . e($t['author_role']) : '' ?></footer>
      </blockquote>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- CTA FINAL -->
<section class="section cta-final">
  <div class="container">
    <h2>Leve a Palavra com você</h2>
    <p>Adquira os livros do Pr. Elias com envio para todo o Brasil, pagamento seguro e garantia de <?= e($settings['guarantee_days'] ?? '7') ?> dias.</p>
    <a class="btn btn-gold btn-lg" href="<?= base_url('livros') ?>">Comprar agora →</a>
  </div>
</section>
