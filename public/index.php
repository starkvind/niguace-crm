<?php

require dirname(__DIR__) . '/app/bootstrap.php';

use App\Controllers\AdminController;
use App\Controllers\AuthController;
use App\Controllers\PublicController;
use App\Models\Section;

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
if (substr($scriptDir, -7) === '/public') {
    $scriptDir = substr($scriptDir, 0, -7) ?: '/';
}
if ($scriptDir && $scriptDir !== '/' && strpos($path, $scriptDir) === 0) {
    $path = substr($path, strlen($scriptDir)) ?: '/';
}
$path = '/' . trim($path, '/');
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

$public = new PublicController();
$auth = new AuthController();
$admin = new AdminController();

if ($method === 'GET' && $path === '/') { $public->home(); return; }
if ($method === 'GET' && $path === '/buscar') { $public->search(); return; }
if ($method === 'GET' && $path === '/login') { $auth->loginForm(); return; }
if ($method === 'POST' && $path === '/login') { $auth->login(); return; }
if ($method === 'POST' && $path === '/logout') { $auth->logout(); return; }
if ($method === 'GET' && $path === '/admin') { $admin->dashboard(); return; }
if ($method === 'GET' && $path === '/admin/settings') { $admin->settings(); return; }
if ($method === 'POST' && $path === '/admin/settings') { $admin->updateSettings(); return; }
if ($method === 'GET' && $path === '/admin/articles/create') { $admin->create(); return; }
if ($method === 'GET' && $path === '/admin/sections') { $admin->sections(); return; }
if ($method === 'POST' && $path === '/admin/sections') { $admin->storeSection(); return; }
if ($method === 'GET' && $path === '/admin/categories') { $admin->categories(); return; }
if ($method === 'POST' && $path === '/admin/categories') { $admin->storeCategory(); return; }
if ($method === 'GET' && $path === '/admin/users') { $admin->users(); return; }
if ($method === 'POST' && $path === '/admin/users') { $admin->storeUser(); return; }

if (preg_match('#^/admin/sections/(\d+)/edit$#', $path, $matches) && $method === 'GET') { $admin->editSection((int) $matches[1]); return; }
if (preg_match('#^/admin/sections/(\d+)$#', $path, $matches) && $method === 'POST') {
    $action = $_POST['_method'] ?? 'POST';
    if ($action === 'DELETE') { $admin->deleteSection((int) $matches[1]); return; }
    $admin->updateSection((int) $matches[1]); return;
}
if (preg_match('#^/admin/categories/(\d+)/edit$#', $path, $matches) && $method === 'GET') { $admin->editCategory((int) $matches[1]); return; }
if (preg_match('#^/admin/categories/(\d+)$#', $path, $matches) && $method === 'POST') {
    $action = $_POST['_method'] ?? 'POST';
    if ($action === 'DELETE') { $admin->deleteCategory((int) $matches[1]); return; }
    $admin->updateCategory((int) $matches[1]); return;
}
if ($method === 'POST' && $path === '/admin/articles') { $admin->store(); return; }
if (preg_match('#^/admin/articles/(\d+)/edit$#', $path, $matches) && $method === 'GET') { $admin->edit((int) $matches[1]); return; }
if (preg_match('#^/admin/articles/(\d+)$#', $path, $matches) && $method === 'POST') {
    $action = $_POST['_method'] ?? 'POST';
    if ($action === 'DELETE') { $admin->delete((int) $matches[1]); return; }
    $admin->update((int) $matches[1]); return;
}
if (preg_match('#^/categoria/([a-z0-9-]+)/([a-z0-9-]+)$#', $path, $matches) && $method === 'GET') { $public->category($matches[1], $matches[2]); return; }
if ($method === 'GET' && $path === '/sobre') { $public->about(); return; }
if (preg_match('#^/entrada/([a-z0-9-]+)$#', $path, $matches) && $method === 'GET') { $public->article($matches[1]); return; }
if ($method === 'GET' && preg_match('#^/([a-z0-9-]+)$#', $path, $matches) && Section::findBySlug($matches[1])) { $public->section($matches[1]); return; }

http_response_code(404);
echo 'Pagina no encontrada.';
