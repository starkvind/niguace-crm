<?php
$isEdit = (bool) $category;
$action = $isEdit ? url('/admin/categories/' . $category['id']) : url('/admin/categories');
$selectedSection = $category['section'] ?? (allowed_sections()[0] ?? '');
?>
<section class="admin-shell">
    <div class="admin-topbar">
        <div><p class="eyebrow">Clasificación</p><h1>Etiquetas</h1></div>
    </div>
    <?php require __DIR__ . '/_menu.php'; ?>
    <div class="admin-grid">
        <form class="panel stack" method="post" action="<?= e($action) ?>">
            <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
            <label>Sección<select name="section"><?php foreach (sections() as $section): ?><option value="<?= e($section['slug']) ?>" <?= $selectedSection === $section['slug'] ? 'selected' : '' ?>><?= e($section['name']) ?></option><?php endforeach; ?></select></label>
            <label>Nombre<input type="text" name="name" value="<?= e($category['name'] ?? '') ?>" required></label>
            <label>Slug<input type="text" name="slug" value="<?= e($category['slug'] ?? '') ?>"></label>
            <label>Orden<input type="number" name="sort_order" value="<?= e((string) ($category['sort_order'] ?? 0)) ?>"></label>
            <button class="button primary" type="submit"><?= $isEdit ? 'Guardar cambios' : 'Crear etiqueta' ?></button>
        </form>
        <div class="table-wrap category-list">
            <table class="responsive-table">
                <thead><tr><th>Nombre</th><th>Sección</th><th>Slug</th><th>Orden</th><th></th></tr></thead>
                <tbody>
                    <?php foreach ($categories as $item): ?>
                        <tr>
                            <td data-label="Nombre"><strong><?= e($item['name']) ?></strong></td>
                            <td data-label="Sección"><?= e(section_label($item['section'])) ?></td>
                            <td data-label="Slug"><code><?= e($item['slug']) ?></code></td>
                            <td data-label="Orden"><?= e((string) $item['sort_order']) ?></td>
                            <td class="row-actions">
                                <a class="button compact" href="<?= e(url('/admin/categories/' . $item['id'] . '/edit')) ?>">Editar</a>
                                <form method="post" action="<?= e(url('/admin/categories/' . $item['id'])) ?>" onsubmit="return confirm('Eliminar esta etiqueta?')"><input type="hidden" name="_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="_method" value="DELETE"><button class="button danger compact" type="submit">Borrar</button></form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
