<?php $siteName = setting('site_name', config('app_name')); ?>
<section class="hero">
    <div class="hero-inner">
        <p class="eyebrow"><?= e(setting('hero_eyebrow', '')) ?></p>
        <h1><?= e($siteName) ?></h1>
        <p><?= e(setting('hero_description', '')) ?></p>
        <form class="hero-search" action="<?= e(url('/buscar')) ?>" method="get">
            <input type="search" name="q" placeholder="Busca articulos o etiquetas" aria-label="Buscar articulos">
            <button type="submit" class="button primary">Explorar</button>
        </form>
    </div>
</section>

<section class="content-band">
    <div class="section-heading">
        <p class="eyebrow">Ultimas actualizaciones</p>
        <h2><?= e(setting('homepage_title', 'Archivo de campana')) ?></h2>
    </div>
    <div class="card-grid">
        <?php foreach ($latest as $article): ?>
            <?php require __DIR__ . '/_card.php'; ?>
        <?php endforeach; ?>
        <?php if (!$latest): ?>
            <p>No hay entradas publicadas todavia.</p>
        <?php endif; ?>
    </div>
</section>
