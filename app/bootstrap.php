<?php

declare(strict_types=1);

use App\Models\Section;
use App\Models\Setting;

spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
    }

    $relative = substr($class, strlen($prefix));
    $path = __DIR__ . '/' . str_replace('\\', '/', $relative) . '.php';
    if (is_file($path)) {
        require $path;
    }
});

$config = require dirname(__DIR__) . '/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_name($config['session_name']);
    session_start();
}

function config(string $key, $default = null)
{
    static $config = null;
    if ($config === null) {
        $config = require dirname(__DIR__) . '/config.php';
    }

    return $config[$key] ?? $default;
}

function setting(string $key, $default = null)
{
    try {
        return Setting::get($key, $default);
    } catch (\Throwable $e) {
        return $default;
    }
}

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function slugify(string $value): string
{
    $value = iconv('UTF-8', 'ASCII//TRANSLIT', $value) ?: $value;
    $value = strtolower($value);
    $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? '';
    $value = trim($value, '-');

    return $value !== '' ? $value : 'entrada-' . bin2hex(random_bytes(3));
}

function redirect(string $path)
{
    header('Location: ' . url($path));
    exit;
}

function url(string $path = ''): string
{
    $configured = rtrim(config('base_url', ''), '/');
    if ($configured !== '') {
        return $configured . '/' . ltrim($path, '/');
    }

    $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    $base = rtrim(str_replace('/index.php', '', $script), '/');
    if (substr($base, -7) === '/public') {
        $base = substr($base, 0, -7);
    }
    return $base . '/' . ltrim($path, '/');
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function require_auth(): void
{
    if (!current_user()) {
        redirect('/login');
    }
}

function has_role(string ...$roles): bool
{
    $user = current_user();
    return $user && in_array($user['role'] ?? '', $roles, true);
}

function require_role(string ...$roles): void
{
    require_auth();
    if (!has_role(...$roles)) {
        http_response_code(403);
        exit('No autorizado.');
    }
}

function can_manage_article(?array $article = null): bool
{
    $user = current_user();
    if (!$user) {
        return false;
    }

    if (in_array($user['role'], ['admin', 'editor'], true)) {
        return true;
    }

    return $user['role'] === 'author' && $article && (int) $article['created_by'] === (int) $user['id'];
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verify_csrf(): void
{
    $token = $_POST['_token'] ?? '';
    if (!is_string($token) || !hash_equals(csrf_token(), $token)) {
        http_response_code(419);
        exit('Token CSRF no valido.');
    }
}

function sections(): array
{
    try {
        return Section::navigable();
    } catch (\Throwable $e) {
        return [];
    }
}

function menu_sections(): array
{
    return array_values(array_filter(
        sections(),
        static fn (array $section): bool => $section['slug'] !== 'about'
    ));
}

function about_section(): ?array
{
    return section_record('about');
}

function section_record(string $slug): ?array
{
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        foreach (sections() as $section) {
            $cache[$section['slug']] = $section;
        }
    }

    return $cache[$slug] ?? null;
}

function section_label(string $section): string
{
    if ($section === 'search') {
        return 'Busqueda';
    }

    $record = section_record($section);
    return $record['name'] ?? $section;
}

function allowed_sections(): array
{
    return array_map(static fn (array $section): string => $section['slug'], sections());
}

function admin_menu_items(): array
{
    $items = [
        ['key' => 'entries', 'label' => 'Entradas', 'url' => url('/admin')],
        ['key' => 'new-entry', 'label' => 'Nueva entrada', 'url' => url('/admin/articles/create')],
    ];

    if (has_role('admin', 'editor')) {
        $items[] = ['key' => 'sections', 'label' => 'Secciones', 'url' => url('/admin/sections')];
        $items[] = ['key' => 'tags', 'label' => 'Etiquetas', 'url' => url('/admin/categories')];
    }

    if (has_role('admin')) {
        $items[] = ['key' => 'settings', 'label' => 'Ajustes web', 'url' => url('/admin/settings')];
        $items[] = ['key' => 'users', 'label' => 'Usuarios', 'url' => url('/admin/users')];
    }

    $items[] = ['key' => 'site', 'label' => 'Ver web', 'url' => url('/')];

    return $items;
}
