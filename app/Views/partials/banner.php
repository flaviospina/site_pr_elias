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
