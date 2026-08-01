<?php
/**
 * INSTALADOR / REPARADOR DAS VIEWS — recria as páginas do site com o
 * conteúdo correto, cada uma no lugar certo (conserta arquivos trocados
 * ou que não subiram por FTP). Preserva a codificação UTF-8.
 *
 * COMO USAR:
 *   1. Envie SÓ este arquivo para a raiz do site (site_new/reparar_views.php).
 *      No FileZilla, garanta modo de transferência "Binário" (ou "Automático").
 *   2. Abra no navegador: https://eliasjosedasilva.com.br/site_new/reparar_views.php
 *   3. Confira os ✅ e APAGUE este arquivo do servidor.
 */
header('Content-Type: text/plain; charset=utf-8');
$base = __DIR__;
$files = [];
$files['app/Views/home/index.php'] = <<<'ARQ_EOF'
<?php use App\Core\Cart; use App\Core\Csrf; ?>

<!-- BANNER PRINCIPAL (configurável em Painel > Banners) -->
<?php render_banner('home', [
    'kicker'   => $settings['site_tagline'] ?? '',
    'title'    => $settings['hero_title'] ?? '',
    'subtitle' => $settings['hero_subtitle'] ?? '',
    'image'    => $settings['hero_image'] ?? '',
    'height'   => 'large',
    'trust'    => [
        '🔒 Compra 100% segura',
        '✔ Garantia de ' . ($settings['guarantee_days'] ?? '7') . ' dias',
        '🚚 Envio para todo o Brasil',
    ],
]); ?>

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

ARQ_EOF;
$files['app/Views/books/index.php'] = <<<'ARQ_EOF'
<?php use App\Core\Csrf; ?>
<?php render_banner('livros', [
    'title'    => 'Livros do Pr. Elias',
    'subtitle' => 'Obras para fortalecer a fé, aprofundar o conhecimento das Escrituras e edificar a igreja.',
    'trust'    => [
        '🔒 Compra 100% segura',
        '✔ Garantia de ' . ($settings['guarantee_days'] ?? '7') . ' dias',
        '🚚 Envio para todo o Brasil',
    ],
]); ?>

<section class="section">
  <div class="container">
    <div class="grid grid-3">
      <?php foreach ($books as $book): ?>
      <article class="book-card">
        <a href="<?= base_url('livros/' . $book['slug']) ?>" class="book-cover">
          <?php if ($book['badge']): ?><span class="badge"><?= e($book['badge']) ?></span><?php endif; ?>
          <img src="<?= e($book['image']) ?>" alt="Capa do livro <?= e($book['title']) ?>" loading="lazy">
        </a>
        <div class="book-info">
          <h3><a href="<?= base_url('livros/' . $book['slug']) ?>"><?= e($book['title']) ?></a></h3>
          <?php if ($book['subtitle']): ?><p class="book-subtitle"><?= e($book['subtitle']) ?></p><?php endif; ?>
          <p><?= e($book['excerpt']) ?></p>
          <div class="book-buy">
            <span class="price">
              <?php $sale = $book['sale_price'] !== null && (float)$book['sale_price'] > 0 && (float)$book['sale_price'] < (float)$book['price']; ?>
              <?php if ($sale): ?><s><?= money((float) $book['price']) ?></s> <strong><?= money((float) $book['sale_price']) ?></strong>
              <?php else: ?><strong><?= money((float) $book['price']) ?></strong><?php endif; ?>
            </span>
            <?php if ((int) $book['in_stock'] === 1): ?>
            <form method="post" action="<?= base_url('carrinho/adicionar') ?>">
              <?= Csrf::field() ?>
              <input type="hidden" name="book_id" value="<?= (int) $book['id'] ?>">
              <button class="btn btn-primary" type="submit">Comprar</button>
            </form>
            <?php else: ?>
            <span class="badge badge-muted">Esgotado</span>
            <?php endif; ?>
          </div>
        </div>
      </article>
      <?php endforeach; ?>
    </div>

    <?php if ($testimonials): ?>
    <div class="section-head" style="margin-top:64px">
      <h2>O que dizem os leitores</h2>
    </div>
    <div class="grid grid-3">
      <?php foreach ($testimonials as $t): ?>
      <blockquote class="testimonial">
        <div class="stars"><?= str_repeat('★', (int) $t['rating']) . str_repeat('☆', 5 - (int) $t['rating']) ?></div>
        <p>“<?= e($t['content']) ?>”</p>
        <footer><strong><?= e($t['author']) ?></strong><?= $t['author_role'] ? ' · ' . e($t['author_role']) : '' ?></footer>
      </blockquote>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>

ARQ_EOF;
$files['app/Views/sermons/index.php'] = <<<'ARQ_EOF'
<?php render_banner('sermoes', [
    'title'    => 'Sermões',
    'subtitle' => 'Pregações expositivas, fiéis às Escrituras, para edificação da igreja.',
]); ?>

<section class="section">
  <div class="container">
    <form class="search-bar" method="get" action="<?= base_url('sermoes') ?>">
      <input type="search" name="busca" value="<?= e($search) ?>" placeholder="Buscar sermão por título ou texto…">
      <button class="btn btn-primary" type="submit">Buscar</button>
    </form>

    <?php if (!$sermons): ?>
      <p class="empty-state">Nenhum sermão encontrado<?= $search ? ' para “' . e($search) . '”' : '' ?>.</p>
    <?php else: ?>
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
    <?php endif; ?>

    <?php if ($totalPages > 1): ?>
    <nav class="pagination">
      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a class="<?= $i === $page ? 'active' : '' ?>" href="<?= base_url('sermoes?pagina=' . $i . ($search ? '&busca=' . urlencode($search) : '')) ?>"><?= $i ?></a>
      <?php endfor; ?>
    </nav>
    <?php endif; ?>
  </div>
</section>

ARQ_EOF;
$files['app/Views/devotionals/index.php'] = <<<'ARQ_EOF'
<?php use App\Core\Csrf; ?>
<?php render_banner('devocionais', [
    'title'    => 'Devocionais',
    'subtitle' => 'Reflexões bíblicas diárias para fortalecer a sua caminhada com Deus.',
]); ?>

<section class="section">
  <div class="container">
    <form class="search-bar" method="get" action="<?= base_url('devocionais') ?>">
      <input type="search" name="busca" value="<?= e($search) ?>" placeholder="Buscar devocional por título ou texto…">
      <button class="btn btn-primary" type="submit">Buscar</button>
    </form>

    <?php if (!$devotionals): ?>
      <p class="empty-state">Nenhum devocional encontrado<?= $search ? ' para “' . e($search) . '”' : '' ?>.</p>
    <?php else: ?>
    <div class="grid grid-3">
      <?php foreach ($devotionals as $dev): ?>
      <article class="content-card">
        <p class="content-card-meta"><?= e($dev['bible_reference'] ?: date_br($dev['published_at'])) ?></p>
        <h3><a href="<?= base_url('devocionais/' . $dev['slug']) ?>"><?= e($dev['title']) ?></a></h3>
        <p><?= e($dev['excerpt']) ?></p>
        <a class="card-link" href="<?= base_url('devocionais/' . $dev['slug']) ?>">Ler devocional →</a>
      </article>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if ($totalPages > 1): ?>
    <nav class="pagination">
      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a class="<?= $i === $page ? 'active' : '' ?>" href="<?= base_url('devocionais?pagina=' . $i . ($search ? '&busca=' . urlencode($search) : '')) ?>"><?= $i ?></a>
      <?php endfor; ?>
    </nav>
    <?php endif; ?>

    <form class="newsletter boxed" method="post" action="<?= base_url('devocionais/inscrever') ?>">
      <?= Csrf::field() ?>
      <input type="hidden" name="_back" value="devocionais">
      <input type="text" name="website" class="hp-field" tabindex="-1" autocomplete="off" aria-hidden="true">
      <h3>Receba os devocionais por e-mail</h3>
      <div class="newsletter-fields">
        <input type="text" name="name" placeholder="Seu nome">
        <input type="email" name="email" placeholder="Seu melhor e-mail" required>
        <button class="btn btn-gold" type="submit">Quero receber</button>
      </div>
      <small>Ao se inscrever, você concorda com a nossa <a href="<?= base_url('politica-de-privacidade') ?>">Política de Privacidade</a> (LGPD).</small>
    </form>
  </div>
</section>

ARQ_EOF;
$files['app/Views/agenda/index.php'] = <<<'ARQ_EOF'
<?php render_banner('agenda', [
    'title'    => 'Agenda',
    'subtitle' => 'Acompanhe os próximos compromissos, cultos e conferências do Pr. Elias.',
]); ?>

<section class="section">
  <div class="container">
    <?php if (!$upcoming): ?>
      <p class="empty-state">Nenhum evento agendado no momento. Volte em breve ou fale conosco pelo WhatsApp para convites.</p>
    <?php else: ?>
    <div class="event-list">
      <?php foreach ($upcoming as $event): ?>
      <article class="event-row">
        <div class="event-date">
          <strong><?= (int) date('d', strtotime($event['event_date'])) ?></strong>
          <span><?= month_br($event['event_date']) ?> <?= date('Y', strtotime($event['event_date'])) ?></span>
        </div>
        <div class="event-info">
          <h3><?= e($event['title']) ?></h3>
          <?php if ($event['description']): ?><p><?= e($event['description']) ?></p><?php endif; ?>
          <p class="event-meta">
            <?= e(trim(($event['location'] ? $event['location'] . ' — ' : '') . ($event['city'] ?? ''))) ?>
            <?= $event['event_time'] ? ' · ' . e($event['event_time']) : '' ?>
          </p>
          <?php if ($event['link']): ?><a class="card-link" href="<?= e($event['link']) ?>" target="_blank" rel="noopener">Mais informações →</a><?php endif; ?>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="article-cta" style="margin-top:48px">
      <h3>Convites para pregações e eventos</h3>
      <p>Entre em contato pelo WhatsApp <?= e($settings['whatsapp_display'] ?? '') ?> para convidar o Pr. Elias para o seu evento.</p>
      <a class="btn btn-gold" href="https://wa.me/<?= e($settings['whatsapp'] ?? '') ?>" target="_blank" rel="noopener">Falar no WhatsApp →</a>
    </div>
  </div>
</section>

ARQ_EOF;
$files['app/Views/contact/index.php'] = <<<'ARQ_EOF'
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

ARQ_EOF;
$files['app/Views/about/index.php'] = <<<'ARQ_EOF'
<?php render_banner('sobre-o-pr', [
    'kicker' => $settings['site_tagline'] ?? '',
    'title'  => 'Pr. Elias José da Silva',
]); ?>

<section class="section">
  <div class="container about-page">
    <div class="about-page-photo">
      <img src="<?= e($settings['about_image'] ?? '') ?>" alt="Pr. Elias José da Silva">
    </div>
    <div class="article-body">
      <?php if ($page): ?>
        <?= $page['content'] /* HTML sanitizado no admin */ ?>
      <?php else: ?>
        <p>Conteúdo em breve.</p>
      <?php endif; ?>
    </div>
  </div>

  <?php if ($books): ?>
  <div class="container" style="margin-top:64px">
    <div class="section-head"><h2>Obras publicadas</h2></div>
    <div class="grid grid-3">
      <?php foreach ($books as $book): ?>
      <article class="book-card">
        <a href="<?= base_url('livros/' . $book['slug']) ?>" class="book-cover">
          <img src="<?= e($book['image']) ?>" alt="Capa do livro <?= e($book['title']) ?>" loading="lazy">
        </a>
        <div class="book-info">
          <h3><a href="<?= base_url('livros/' . $book['slug']) ?>"><?= e($book['title']) ?></a></h3>
          <div class="book-buy">
            <span class="price"><strong><?= money((float) $book['price']) ?></strong></span>
            <a class="btn btn-outline" href="<?= base_url('livros/' . $book['slug']) ?>">Ver livro</a>
          </div>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>
</section>

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

$ok=0;$fail=0;
foreach ($files as $rel => $content) {
    $full = $base.'/'.$rel; $dir = dirname($full);
    if (!is_dir($dir)) @mkdir($dir, 0755, true);
    $b = @file_put_contents($full, $content);
    if ($b !== false) { echo "✅ $rel ($b bytes)\n"; $ok++; }
    else { echo "❌ FALHA: $rel (permissão da pasta?)\n"; $fail++; }
}
echo "\n";
foreach (['app/Models/Banner.php','app/Controllers/Admin/BannerController.php','app/Core/helpers.php'] as $req)
    echo (is_file($base.'/'.$req) ? "✅ existe: $req\n" : "⚠️  FALTA (envie por FTP): $req\n");
echo "\n===============================\n";
echo ($fail===0 ? "CONCLUÍDO! $ok arquivos gravados. Recarregue o site (Ctrl+F5),\nteste os banners no Painel e APAGUE este arquivo (reparar_views.php)." : "Houve $fail falha(s) — veja acima.");
echo "\n===============================\n";
