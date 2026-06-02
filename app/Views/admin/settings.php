<section class="admin-shell">
    <div class="admin-topbar">
        <div><p class="eyebrow">Configuracion</p><h1>Ajustes web</h1></div>
    </div>
    <?php require __DIR__ . '/_menu.php'; ?>
    <form class="panel stack settings-panel" method="post" action="<?= e(url('/admin/settings')) ?>">
        <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
        <label>Nombre visible<input type="text" name="site_name" value="<?= e($siteName) ?>" required></label>
        <label>Subtitulo<input type="text" name="site_tagline" value="<?= e($siteTagline) ?>"></label>
        <label>Titulo de portada<input type="text" name="homepage_title" value="<?= e($homepageTitle) ?>"></label>
        <label>Texto superior de portada<input type="text" name="hero_eyebrow" value="<?= e($heroEyebrow) ?>"></label>
        <label>Descripcion de portada<textarea name="hero_description" rows="3"><?= e($heroDescription) ?></textarea></label>
        <hr class="settings-divider">
        <div>
            <p class="eyebrow">Footer</p>
            <h2>Seccion inferior</h2>
        </div>
        <label>Titulo del footer<input type="text" name="footer_title" value="<?= e($footerTitle) ?>"></label>
        <label>Contenido del footer<textarea name="footer_content" rows="4"><?= e($footerContent) ?></textarea></label>
        <div class="admin-actions"><button class="button primary" type="submit">Guardar ajustes</button></div>
    </form>
</section>
