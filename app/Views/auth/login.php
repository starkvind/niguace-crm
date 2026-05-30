<?php $siteName = setting('site_name', config('app_name')); ?>
<section class="auth-panel">
    <a class="brand auth-brand" href="<?= e(url('/')) ?>">
        <span class="brand-mark">N</span>
        <span>
            <strong><?= e($siteName) ?></strong>
            <small>Archivo arcano</small>
        </span>
    </a>

    <h1>Acceso al archivo</h1>
    <?php if (!empty($_SESSION['flash_error'])): ?>
        <p class="alert"><?= e($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?></p>
    <?php endif; ?>

    <form method="post" action="<?= e(url('/login')) ?>" class="stack">
        <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
        <label>
            Email
            <input type="email" name="email" required autocomplete="email">
        </label>
        <label>
            Password
            <input type="password" name="password" required autocomplete="current-password">
        </label>
        <button class="button primary" type="submit">Entrar</button>
    </form>
</section>
