<section class="page-hero">
  <div class="container">
    <h1><?= e($page['title']) ?></h1>
  </div>
</section>

<section class="section">
  <div class="container article-page">
    <div class="article-body">
      <?= $page['content'] /* HTML sanitizado no admin */ ?>
    </div>
    <p class="article-meta" style="margin-top:32px">Última atualização: <?= date_br($page['updated_at']) ?></p>
  </div>
</section>
