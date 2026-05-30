<article class="entry-card">
    <?php if (!empty($article['featured_image'])): ?>
        <img src="<?= e(url('/uploads/' . $article['featured_image'])) ?>" alt="">
    <?php endif; ?>
    <div>
        <p class="eyebrow"><?= e(section_label($article['section'])) ?></p>
        <?php if (!empty($article['category_name']) && !empty($article['category_slug'])): ?>
            <a class="category-pill" href="<?= e(url('/categoria/' . $article['section'] . '/' . $article['category_slug'])) ?>">
                <?= e($article['category_name']) ?>
            </a>
        <?php endif; ?>
        <h3><a href="<?= e(url('/entrada/' . $article['slug'])) ?>"><?= e($article['title']) ?></a></h3>
        <p class="entry-meta">
            <?php if (!empty($article['author_name'])): ?>Por <?= e($article['author_name']) ?><?php endif; ?>
            <?php if (!empty($article['published_at'])): ?> · <?= e(substr((string) $article['published_at'], 0, 10)) ?><?php endif; ?>
        </p>
        <?php if (!empty($article['excerpt'])): ?>
            <p><?= e($article['excerpt']) ?></p>
        <?php endif; ?>
    </div>
</article>
