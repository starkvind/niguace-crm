<?php
$isEdit = (bool) $article;
$action = $isEdit ? url('/admin/articles/' . $article['id']) : url('/admin/articles');
$selectedSection = $article['section'] ?? ($_GET['section'] ?? (allowed_sections()[0] ?? ''));
?>
<section class="admin-shell">
    <div class="admin-topbar">
        <div><p class="eyebrow">Edición</p><h1><?= $isEdit ? 'Editar entrada' : 'Nueva entrada' ?></h1></div>
    </div>
    <?php require __DIR__ . '/_menu.php'; ?>
    <form class="editor-form" method="post" action="<?= e($action) ?>" enctype="multipart/form-data">
        <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
        <div class="form-grid">
            <section class="panel stack">
                <label>Título<input type="text" name="title" value="<?= e($article['title'] ?? '') ?>" required></label>
                <label>Slug<input type="text" name="slug" value="<?= e($article['slug'] ?? '') ?>" placeholder="se genera si lo dejas vacío"></label>
                <label>Resumen<textarea name="excerpt" rows="3"><?= e($article['excerpt'] ?? '') ?></textarea></label>
                <label>Texto enriquecido<textarea id="body" name="body" rows="18"><?= e($article['body'] ?? '') ?></textarea></label>
            </section>
            <aside class="panel stack">
                <label>Sección
                    <select name="section" id="section-select">
                        <?php foreach (sections() as $section): ?>
                            <option value="<?= e($section['slug']) ?>" <?= $selectedSection === $section['slug'] ? 'selected' : '' ?>><?= e($section['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label>Etiqueta
                    <select name="category_id" id="category-select">
                        <option value="">Sin etiqueta</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= e((string) $category['id']) ?>" data-section="<?= e($category['section']) ?>" <?= (string) ($article['category_id'] ?? '') === (string) $category['id'] ? 'selected' : '' ?>><?= e($category['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label>Estado<select name="status"><option value="draft" <?= ($article['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>Borrador</option><option value="published" <?= ($article['status'] ?? '') === 'published' ? 'selected' : '' ?>>Publicado</option></select></label>
                <label>Fecha de publicación<input type="text" name="published_at" value="<?= e($article['published_at'] ?? '') ?>" placeholder="YYYY-MM-DD HH:MM:SS"></label>
                <?php if (has_role('admin', 'editor')): ?>
                    <label>Autor
                        <select name="created_by">
                            <?php foreach (($users ?? []) as $user): ?>
                                <option value="<?= e((string) $user['id']) ?>" <?= (string) ($article['created_by'] ?? current_user()['id']) === (string) $user['id'] ? 'selected' : '' ?>>
                                    <?= e($user['name']) ?><?= !empty($user['email']) ? ' (' . e($user['email']) . ')' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                <?php endif; ?>
                <label>Imagen destacada<input type="file" name="featured_image" accept="image/*"></label>
                <?php if (!empty($article['featured_image'])): ?><img class="thumb" src="<?= e(url('/uploads/' . $article['featured_image'])) ?>" alt=""><?php endif; ?>
                <button class="button primary" type="submit">Guardar</button>
            </aside>
        </div>
    </form>
</section>

<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
<script src="<?= e(url('/assets/js/admin.js')) ?>"></script>
