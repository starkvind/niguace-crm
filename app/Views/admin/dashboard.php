<section class="admin-shell">
    <div class="admin-topbar">
        <div><p class="eyebrow">CMS</p><h1>Panel de entradas</h1></div>
    </div>
    <?php require __DIR__ . '/_menu.php'; ?>

    <?php if (!empty($_SESSION['flash_error'])): ?><p class="alert"><?= e($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?></p><?php endif; ?>

    <div class="table-wrap">
        <table class="responsive-table">
            <thead><tr><th>Título</th><th>Sección</th><th>Etiqueta</th><th>Autor</th><th>Estado</th><th>Publicado</th><th></th></tr></thead>
            <tbody>
                <?php foreach ($articles as $article): ?>
                    <tr>
                        <td data-label="Título"><strong><?= e($article['title']) ?></strong><br><small>/entrada/<?= e($article['slug']) ?></small></td>
                        <td data-label="Sección"><?= e(section_label($article['section'])) ?></td>
                        <td data-label="Etiqueta"><?= e($article['category_name'] ?? '-') ?></td>
                        <td data-label="Autor"><?= e($article['author_name'] ?? '-') ?></td>
                        <td data-label="Estado"><span class="status <?= e($article['status']) ?>"><?= e($article['status']) ?></span></td>
                        <td data-label="Publicado"><?= e($article['published_at'] ?: '-') ?></td>
                        <td class="row-actions">
                            <a class="button compact" href="<?= e(url('/admin/articles/' . $article['id'] . '/edit')) ?>">Editar</a>
                            <?php if (has_role('admin')): ?>
                                <form method="post" action="<?= e(url('/admin/articles/' . $article['id'])) ?>" onsubmit="return confirm('Eliminar esta entrada?')"><input type="hidden" name="_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="_method" value="DELETE"><button class="button danger compact" type="submit">Borrar</button></form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$articles): ?><tr><td colspan="7">Todavía no hay entradas.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
