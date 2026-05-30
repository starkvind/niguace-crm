<?php $activeKey = $adminMenuKey ?? ''; ?>
<nav class="admin-menu">
    <?php foreach (admin_menu_items() as $item): ?>
        <a class="button <?= $activeKey === $item['key'] ? 'primary' : 'ghost' ?>" href="<?= e($item['url']) ?>"><?= e($item['label']) ?></a>
    <?php endforeach; ?>
    <form method="post" action="<?= e(url('/logout')) ?>">
        <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
        <button class="button ghost" type="submit">Salir</button>
    </form>
</nav>
