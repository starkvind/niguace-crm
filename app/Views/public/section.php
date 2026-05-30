<section class="page-title">
    <p class="eyebrow"><?= isset($searchQuery) ? 'Busqueda' : (isset($category) ? e(section_label($section)) : 'Sección') ?></p>
    <h1><?= isset($searchQuery) ? ($searchQuery !== '' ? 'Resultados para "' . e($searchQuery) . '"' : 'Buscar en el archivo') : (isset($category) ? e($category['name']) : e(section_label($section))) ?></h1>
    <?php if (isset($searchQuery)): ?>
        <p class="section-subtitle"><?= $searchQuery !== '' ? count($articles) . ' resultado(s) en articulos publicados.' : 'Escribe un termino para recorrer el archivo.' ?></p>
    <?php elseif (isset($category)): ?>
        <p class="section-subtitle">Entradas filtradas por categoria.</p>
    <?php endif; ?>
</section>

<section class="content-band">
    <div class="card-grid">
        <?php foreach ($articles as $article): ?>
            <?php require __DIR__ . '/_card.php'; ?>
        <?php endforeach; ?>
        <?php if (!$articles): ?>
            <?php if (isset($searchQuery) && $searchQuery !== ''): ?>
                <p>No hay coincidencias para esa busqueda.</p>
            <?php elseif (isset($searchQuery)): ?>
                <p>No hay busqueda activa.</p>
            <?php else: ?>
                <p>No hay entradas publicadas en esta sección.</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>
