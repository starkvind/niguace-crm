<?php

require __DIR__ . '/app/bootstrap.php';

use App\Core\Database;
use App\Models\Setting;

$pdo = Database::connect();
$schema = file_get_contents(__DIR__ . '/database/schema.sql');
$pdo->exec($schema);

$config = require __DIR__ . '/config.php';
$initialPassword = $config['default_admin_password'] ?: bin2hex(random_bytes(8));

$sections = [
    ['slug' => 'news', 'name' => 'Noticias', 'nav_label' => 'Noticias', 'emoji' => '📰', 'description' => 'Actualidad del proyecto'],
    ['slug' => 'characters', 'name' => 'Personajes', 'nav_label' => 'Personajes', 'emoji' => '👤', 'description' => 'Personajes y expedientes'],
    ['slug' => 'chapters', 'name' => 'Capítulos', 'nav_label' => 'Capitulos', 'emoji' => '📖', 'description' => 'Cronicas y capítulos'],
    ['slug' => 'locations', 'name' => 'Localizaciones', 'nav_label' => 'Localizaciones', 'emoji' => '🗺️', 'description' => 'Lugares importantes'],
    ['slug' => 'lore', 'name' => 'Trasfondo', 'nav_label' => 'Trasfondo', 'emoji' => '📚', 'description' => 'Historia, magia y trasfondo'],
    ['slug' => 'about', 'name' => 'Sobre', 'nav_label' => 'Sobre', 'emoji' => '✨', 'description' => 'Pagina informativa'],
];

$insertSection = $pdo->prepare(
    'INSERT OR IGNORE INTO sections (name, slug, nav_label, emoji, description, sort_order, is_active)
     VALUES (:name, :slug, :nav_label, :emoji, :description, :sort_order, 1)'
);
foreach ($sections as $index => $section) {
    $insertSection->execute([
        ':name' => $section['name'],
        ':slug' => $section['slug'],
        ':nav_label' => $section['nav_label'],
        ':emoji' => $section['emoji'],
        ':description' => $section['description'],
        ':sort_order' => $index,
    ]);
}

$seedTags = [
    'news' => ['Noticias'],
    'characters' => ['Protagonistas', 'Secundarios', 'Profesorado', 'PNJ', 'Aquelarres', 'Criaturas'],
    'chapters' => ['1er curso', 'Cronicas principales', 'Interludios'],
    'locations' => ['Academia', 'Templo', 'Exterior', 'Reinos'],
    'lore' => ['Historia', 'Magia', 'Religion', 'Secretos', 'Organizaciones'],
    'about' => ['Sobre el proyecto'],
];
$insertCategory = $pdo->prepare('INSERT OR IGNORE INTO categories (section, name, slug, sort_order) VALUES (:section, :name, :slug, :sort_order)');
foreach ($seedTags as $section => $names) {
    foreach ($names as $index => $name) {
        $insertCategory->execute([
            ':section' => $section,
            ':name' => $name,
            ':slug' => slugify($name),
            ':sort_order' => $index,
        ]);
    }
}

$stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE email = :email');
$stmt->execute([':email' => $config['default_admin_email']]);
if ((int) $stmt->fetchColumn() === 0) {
    $insertUser = $pdo->prepare('INSERT INTO users (name, email, password_hash, role) VALUES (:name, :email, :password_hash, :role)');
    $insertUser->execute([
        ':name' => 'Administrador',
        ':email' => $config['default_admin_email'],
        ':password_hash' => password_hash($initialPassword, PASSWORD_DEFAULT),
        ':role' => 'admin',
    ]);
}

$adminId = (int) $pdo->query("SELECT id FROM users WHERE email = " . $pdo->quote($config['default_admin_email']))->fetchColumn();
$aboutCategoryId = (int) $pdo->query("SELECT id FROM categories WHERE section = 'about' AND slug = 'sobre-el-proyecto'")->fetchColumn();
$stmt = $pdo->prepare('SELECT COUNT(*) FROM articles WHERE slug = :slug');
$stmt->execute([':slug' => 'sobre-niguace-net']);
if ((int) $stmt->fetchColumn() === 0 && $adminId > 0) {
    $insertArticle = $pdo->prepare(
        'INSERT INTO articles (section, category_id, title, slug, excerpt, body, status, published_at, created_by, updated_at)
         VALUES (:section, :category_id, :title, :slug, :excerpt, :body, :status, CURRENT_TIMESTAMP, :created_by, CURRENT_TIMESTAMP)'
    );
    $insertArticle->execute([
        ':section' => 'about',
        ':category_id' => $aboutCategoryId ?: null,
        ':title' => 'Sobre ' . setting('site_name', config('app_name')),
        ':slug' => 'sobre-niguace-net',
        ':excerpt' => 'Presentación del sitio.',
        ':body' => '<p>Esta pagina puede editarse desde el panel.</p>',
        ':status' => 'published',
        ':created_by' => $adminId,
    ]);
}

Setting::set('hero_eyebrow', 'Academia de Magia de Niguace');
Setting::set('hero_description', 'Crónicas, expedientes, resúmenes, localizaciones y trasfondo de la campaña.');
Setting::set('homepage_title', 'Archivo de campaña');
Setting::set('site_tagline', 'Archivo de campaña');

echo "Sitio preparado.\n";
echo "Usuario admin: {$config['default_admin_email']}\n";
echo "Password inicial: {$initialPassword}\n";
