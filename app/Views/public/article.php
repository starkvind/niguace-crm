<?php
    $label = $article['section'] === 'about' ? 'El proyecto' : section_label($article['section']);
?>
<article class="article-page">
    <header class="article-hero">
        <div class="article-header">
            <p class="eyebrow"><?= e($label) ?><?= $article['category_name'] && $article['section'] !== 'about' ? ' / ' . e($article['category_name']) : '' ?></p>
            <h1><?= e($article['title']) ?></h1>
            <p class="entry-meta article-meta">
                <?php if (!empty($article['author_name'])): ?>Por <?= e($article['author_name']) ?><?php endif; ?>
                <?php if (!empty($article['published_at'])): ?> · Publicado el <?= e(substr((string) $article['published_at'], 0, 10)) ?><?php endif; ?>
            </p>
            <?php if (!empty($article['category_name']) && !empty($article['category_slug'])): ?>
                <a class="category-pill article-pill" href="<?= e(url('/categoria/' . $article['section'] . '/' . $article['category_slug'])) ?>">
                    <?= e($article['category_name']) ?>
                </a>
            <?php endif; ?>
            <?php if ($article['excerpt']): ?>
                <p><?= e($article['excerpt']) ?></p>
            <?php endif; ?>
        </div>
    </header>

    <div class="article-body">
        <?php if (!empty($article['featured_image'])): ?>
            <img class="article-image" src="<?= e(url('/uploads/' . $article['featured_image'])) ?>" alt="">
        <?php endif; ?>
        <div class="rich-text">
            <?= $article['body'] ?>
        </div>
    </div>
</article>
