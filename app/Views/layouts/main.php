<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php
        $siteName = setting('site_name', config('app_name'));
        $siteTagline = setting('site_tagline', 'Archivo arcano');
        $footerTitle = setting('footer_title', '');
        $footerContent = setting('footer_content', '');
        $pageTitle = $title ?? $siteName;
        $description = $metaDescription ?? setting('site_meta_description', setting('hero_description', ''));
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'niguace.duckdns.org';
        $canonical = $scheme . '://' . $host . ($_SERVER['REQUEST_URI'] ?? '/');
        $shareImage = $ogImage ?? null;
    ?>
    <title><?= e($pageTitle) ?> | <?= e($siteName) ?></title>
    <meta name="description" content="<?= e($description) ?>">
    <link rel="canonical" href="<?= e($canonical) ?>">
    <meta property="og:site_name" content="<?= e($siteName) ?>">
    <meta property="og:title" content="<?= e($pageTitle . ' | ' . $siteName) ?>">
    <meta property="og:description" content="<?= e($description) ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= e($canonical) ?>">
    <?php if ($shareImage): ?><meta property="og:image" content="<?= e($scheme . '://' . $host . $shareImage) ?>"><?php endif; ?>
    <meta name="twitter:card" content="<?= $shareImage ? 'summary_large_image' : 'summary' ?>">
    <link rel="stylesheet" href="<?= e(url('/assets/css/app.css')) ?>">
</head>
<body>
    <header class="site-header">
        <a class="brand" href="<?= e(url('/')) ?>">
            <span class="brand-mark">N</span>
            <span>
                <strong><?= e($siteName) ?></strong>
                <small><?= e($siteTagline) ?></small>
            </span>
        </a>
        <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="main-nav" aria-label="Abrir menu"><span></span><span></span><span></span></button>
        <nav class="main-nav" id="main-nav">
            <?php foreach (menu_sections() as $section): ?>
                <a href="<?= e(url('/' . $section['slug'])) ?>">
                    <?php if (!empty($section['emoji'])): ?><span aria-hidden="true"><?= e($section['emoji']) ?></span><?php endif; ?>
                    <?= e($section['nav_label']) ?>
                </a>
            <?php endforeach; ?>
            <?php $aboutSection = about_section(); ?>
            <?php if ($aboutSection): ?>
                <a href="<?= e(url('/sobre')) ?>">
                    <?php if (!empty($aboutSection['emoji'])): ?><span aria-hidden="true"><?= e($aboutSection['emoji']) ?></span><?php endif; ?>
                    <?= e($aboutSection['nav_label']) ?>
                </a>
            <?php endif; ?>
            <?php if (current_user()): ?>
                <a href="<?= e(url('/admin')) ?>"><span aria-hidden="true">⚙️</span> Panel</a>
            <?php else: ?>
                <a href="<?= e(url('/login')) ?>"><span aria-hidden="true">🔒</span> Acceso</a>
            <?php endif; ?>
        </nav>
        <form class="search-form" action="<?= e(url('/buscar')) ?>" method="get">
            <input type="search" name="q" value="<?= e((string) ($_GET['q'] ?? '')) ?>" placeholder="Buscar articulos" aria-label="Buscar articulos">
            <button type="submit" class="button compact">Buscar</button>
        </form>
    </header>
    <main><?= $content ?></main>
    <?php if ($footerTitle !== '' || $footerContent !== ''): ?>
        <footer class="site-footer">
            <div class="site-footer-inner">
                <?php if ($footerTitle !== ''): ?><h2><?= e($footerTitle) ?></h2><?php endif; ?>
                <?php if ($footerContent !== ''): ?><p><?= nl2br(e($footerContent)) ?></p><?php endif; ?>
            </div>
        </footer>
    <?php endif; ?>
    <script src="<?= e(url('/assets/js/site.js')) ?>"></script>
</body>
</html>
