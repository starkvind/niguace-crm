<?php
$isEdit = (bool) $section;
$action = $isEdit ? url('/admin/sections/' . $section['id']) : url('/admin/sections');
?>
<section class="admin-shell">
    <div class="admin-topbar">
        <div><p class="eyebrow">Estructura</p><h1>Secciones</h1></div>
    </div>
    <?php require __DIR__ . '/_menu.php'; ?>
    <div class="admin-grid">
        <form class="panel stack" method="post" action="<?= e($action) ?>">
            <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
            <label>Nombre<input type="text" name="name" value="<?= e($section['name'] ?? '') ?>" required></label>
            <label>Slug<input type="text" name="slug" value="<?= e($section['slug'] ?? '') ?>"></label>
            <label>Nombre menú<input type="text" name="nav_label" value="<?= e($section['nav_label'] ?? '') ?>"></label>
            <label>Emoji<input type="text" name="emoji" value="<?= e($section['emoji'] ?? '') ?>"></label>
            <label>Descripción<input type="text" name="description" value="<?= e($section['description'] ?? '') ?>"></label>
            <label>Orden<input type="number" name="sort_order" value="<?= e((string) ($section['sort_order'] ?? 0)) ?>"></label>
            <label><input type="checkbox" name="is_active" value="1" <?= !isset($section) || !empty($section['is_active']) ? 'checked' : '' ?>> Activa</label>
            <button class="button primary" type="submit"><?= $isEdit ? 'Guardar cambios' : 'Crear sección' ?></button>
        </form>
        <div class="table-wrap">
            <table class="responsive-table">
                <thead><tr><th>Nombre</th><th>Slug</th><th>Menú</th><th>Emoji</th><th></th></tr></thead>
                <tbody>
                    <?php foreach ($sections as $item): ?>
                        <tr>
                            <td data-label="Nombre"><strong><?= e($item['name']) ?></strong></td>
                            <td data-label="Slug"><code><?= e($item['slug']) ?></code></td>
                            <td data-label="Menú"><?= e($item['nav_label']) ?></td>
                            <td data-label="Emoji"><?= e($item['emoji']) ?></td>
                            <td class="row-actions">
                                <a class="button compact" href="<?= e(url('/admin/sections/' . $item['id'] . '/edit')) ?>">Editar</a>
                                <?php if (has_role('admin')): ?>
                                    <form method="post" action="<?= e(url('/admin/sections/' . $item['id'])) ?>" onsubmit="return confirm('Eliminar esta sección?')"><input type="hidden" name="_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="_method" value="DELETE"><button class="button danger compact" type="submit">Borrar</button></form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
