<section class="admin-shell">
    <div class="admin-topbar">
        <div><p class="eyebrow">Configuración</p><h1>Ajustes web</h1></div>
    </div>
    <?php require __DIR__ . '/_menu.php'; ?>
    <form class="panel stack settings-panel" method="post" action="<?= e(url('/admin/settings')) ?>">
        <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
        <label>Nombre visible<input type="text" name="site_name" value="<?= e($siteName) ?>" required></label>
        <label>Subtítulo<input type="text" name="site_tagline" value="<?= e($siteTagline) ?>"></label>
        <label>Título de portada<input type="text" name="homepage_title" value="<?= e($homepageTitle) ?>"></label>
        <label>Texto superior de portada<input type="text" name="hero_eyebrow" value="<?= e($heroEyebrow) ?>"></label>
        <label>Descripción de portada<textarea name="hero_description" rows="3"><?= e($heroDescription) ?></textarea></label>
        <div class="admin-actions"><button class="button primary" type="submit">Guardar ajustes</button></div>
    </form>
</section>
