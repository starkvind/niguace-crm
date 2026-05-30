<?php

namespace App\Controllers;

use App\Core\View;
use App\Models\Article;
use App\Models\Category;
use App\Models\Section;
use App\Models\Setting;
use App\Models\User;

class AdminController
{
    public function dashboard(): void
    {
        require_auth();
        View::render('admin/dashboard', [
            'title' => 'Panel',
            'articles' => Article::allForAdmin(),
            'adminMenuKey' => 'entries',
        ]);
    }

    public function settings(): void
    {
        require_role('admin');
        View::render('admin/settings', [
            'title' => 'Ajustes web',
            'adminMenuKey' => 'settings',
            'siteName' => setting('site_name', config('app_name')),
            'siteTagline' => setting('site_tagline', 'Archivo arcano'),
            'homepageTitle' => setting('homepage_title', 'Archivo de campana'),
            'heroEyebrow' => setting('hero_eyebrow', ''),
            'heroDescription' => setting('hero_description', ''),
        ]);
    }

    public function updateSettings(): void
    {
        require_role('admin');
        verify_csrf();

        Setting::set('site_name', trim((string) ($_POST['site_name'] ?? '')) ?: config('app_name'));
        Setting::set('site_tagline', trim((string) ($_POST['site_tagline'] ?? '')));
        Setting::set('homepage_title', trim((string) ($_POST['homepage_title'] ?? '')) ?: 'Archivo de campana');
        Setting::set('hero_eyebrow', trim((string) ($_POST['hero_eyebrow'] ?? '')));
        Setting::set('hero_description', trim((string) ($_POST['hero_description'] ?? '')));
        redirect('/admin/settings');
    }

    public function sections(): void
    {
        require_role('admin', 'editor');
        View::render('admin/sections', ['title' => 'Secciones', 'sections' => Section::all(), 'section' => null, 'adminMenuKey' => 'sections']);
    }

    public function editSection(int $id): void
    {
        require_role('admin', 'editor');
        $section = Section::find($id);
        if (!$section) {
            http_response_code(404);
            exit('Sección no encontrada.');
        }
        View::render('admin/sections', ['title' => 'Editar sección', 'sections' => Section::all(), 'section' => $section, 'adminMenuKey' => 'sections']);
    }

    public function storeSection(): void
    {
        require_role('admin', 'editor');
        verify_csrf();
        Section::create($this->sectionPayload());
        redirect('/admin/sections');
    }

    public function updateSection(int $id): void
    {
        require_role('admin', 'editor');
        verify_csrf();
        if (!Section::find($id)) {
            http_response_code(404);
            exit('Sección no encontrada.');
        }
        Section::update($id, $this->sectionPayload($id));
        redirect('/admin/sections');
    }

    public function deleteSection(int $id): void
    {
        require_role('admin');
        verify_csrf();
        Section::delete($id);
        redirect('/admin/sections');
    }

    public function categories(): void
    {
        require_role('admin', 'editor');
        View::render('admin/categories', ['title' => 'Etiquetas', 'categories' => Category::all(), 'category' => null, 'adminMenuKey' => 'tags']);
    }

    public function editCategory(int $id): void
    {
        require_role('admin', 'editor');
        $category = Category::find($id);
        if (!$category) {
            http_response_code(404);
            exit('Etiqueta no encontrada.');
        }
        View::render('admin/categories', ['title' => 'Editar etiqueta', 'categories' => Category::all(), 'category' => $category, 'adminMenuKey' => 'tags']);
    }

    public function storeCategory(): void
    {
        require_role('admin', 'editor');
        verify_csrf();
        Category::create($this->categoryPayload());
        redirect('/admin/categories');
    }

    public function updateCategory(int $id): void
    {
        require_role('admin', 'editor');
        verify_csrf();
        if (!Category::find($id)) {
            http_response_code(404);
            exit('Etiqueta no encontrada.');
        }
        Category::update($id, $this->categoryPayload($id));
        redirect('/admin/categories');
    }

    public function deleteCategory(int $id): void
    {
        require_role('admin', 'editor');
        verify_csrf();
        Category::delete($id);
        redirect('/admin/categories');
    }

    public function create(): void
    {
        require_role('admin', 'editor', 'author');
        View::render('admin/article_form', [
            'title' => 'Nueva entrada',
            'article' => null,
            'categories' => Category::all(),
            'users' => has_role('admin', 'editor') ? User::allAssignable() : [],
            'adminMenuKey' => 'new-entry',
        ]);
    }

    public function store(): void
    {
        require_role('admin', 'editor', 'author');
        verify_csrf();
        $data = $this->articlePayload();
        $data[':created_by'] = current_user()['id'];
        Article::create($data);
        redirect('/admin');
    }

    public function edit(int $id): void
    {
        require_auth();
        $article = Article::find($id);
        if (!$article) {
            http_response_code(404);
            exit('Entrada no encontrada.');
        }
        if (!can_manage_article($article)) {
            http_response_code(403);
            exit('No autorizado.');
        }
        View::render('admin/article_form', [
            'title' => 'Editar entrada',
            'article' => $article,
            'categories' => Category::all(),
            'users' => has_role('admin', 'editor') ? User::allAssignable() : [],
            'adminMenuKey' => 'entries',
        ]);
    }

    public function update(int $id): void
    {
        require_auth();
        verify_csrf();
        $article = Article::find($id);
        if (!$article) {
            http_response_code(404);
            exit('Entrada no encontrada.');
        }
        if (!can_manage_article($article)) {
            http_response_code(403);
            exit('No autorizado.');
        }
        Article::update($id, $this->articlePayload($article));
        redirect('/admin');
    }

    public function delete(int $id): void
    {
        require_role('admin');
        verify_csrf();
        Article::delete($id);
        redirect('/admin');
    }

    public function users(): void
    {
        require_role('admin');
        View::render('admin/users', ['title' => 'Usuarios', 'users' => User::all(), 'adminMenuKey' => 'users']);
    }

    public function storeUser(): void
    {
        require_role('admin');
        verify_csrf();

        $name = trim((string) ($_POST['name'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $role = (string) ($_POST['role'] ?? 'author');

        if ($name === '' || $email === '' || $password === '') {
            $_SESSION['flash_error'] = 'Nombre, email y password son obligatorios.';
            redirect('/admin/users');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash_error'] = 'Email no valido.';
            redirect('/admin/users');
        }
        if (!in_array($role, ['admin', 'editor', 'author'], true)) {
            $role = 'author';
        }
        if (User::findByEmail($email)) {
            $_SESSION['flash_error'] = 'Ya existe un usuario con ese email.';
            redirect('/admin/users');
        }

        User::create([
            ':name' => $name,
            ':email' => $email,
            ':password_hash' => password_hash($password, PASSWORD_DEFAULT),
            ':role' => $role,
        ]);
        redirect('/admin/users');
    }

    private function articlePayload(?array $existing = null): array
    {
        $section = (string) ($_POST['section'] ?? '');
        $allowed = allowed_sections();
        if (!in_array($section, $allowed, true)) {
            $section = $allowed[0] ?? 'news';
        }

        $title = trim((string) ($_POST['title'] ?? ''));
        if ($title === '') {
            $title = 'Entrada sin titulo';
        }

        $slug = trim((string) ($_POST['slug'] ?? ''));
        $slug = $slug !== '' ? slugify($slug) : slugify($title);
        $ignoreId = $existing ? (int) $existing['id'] : null;
        $baseSlug = $slug;
        $counter = 2;
        while (Article::slugExists($slug, $ignoreId)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        $featuredImage = $existing['featured_image'] ?? null;
        if (!empty($_FILES['featured_image']['tmp_name']) && is_uploaded_file($_FILES['featured_image']['tmp_name'])) {
            $featuredImage = $this->storeImage($_FILES['featured_image']);
        }

        $status = (string) ($_POST['status'] ?? 'draft');
        $status = in_array($status, ['draft', 'published'], true) ? $status : 'draft';
        $publishedAt = trim((string) ($_POST['published_at'] ?? ''));
        if ($status === 'published' && $publishedAt === '') {
            $publishedAt = date('Y-m-d H:i:s');
        }

        return [
            ':section' => $section,
            ':category_id' => ($_POST['category_id'] ?? '') !== '' ? (int) $_POST['category_id'] : null,
            ':title' => $title,
            ':slug' => $slug,
            ':excerpt' => trim((string) ($_POST['excerpt'] ?? '')),
            ':featured_image' => $featuredImage,
            ':body' => (string) ($_POST['body'] ?? ''),
            ':status' => $status,
            ':published_at' => $publishedAt !== '' ? $publishedAt : null,
            ':created_by' => $this->resolveAuthorId($existing),
        ];
    }

    private function resolveAuthorId(?array $existing = null): int
    {
        if (!has_role('admin', 'editor')) {
            return $existing ? (int) $existing['created_by'] : (int) current_user()['id'];
        }

        $submittedId = (int) ($_POST['created_by'] ?? 0);
        if ($submittedId > 0 && User::find($submittedId)) {
            return $submittedId;
        }

        return $existing ? (int) $existing['created_by'] : (int) current_user()['id'];
    }

    private function categoryPayload(?int $ignoreId = null): array
    {
        $section = (string) ($_POST['section'] ?? '');
        $allowed = allowed_sections();
        if (!in_array($section, $allowed, true)) {
            $section = $allowed[0] ?? 'news';
        }

        $name = trim((string) ($_POST['name'] ?? ''));
        if ($name === '') {
            $name = 'Etiqueta sin titulo';
        }

        $slug = trim((string) ($_POST['slug'] ?? ''));
        $slug = $slug !== '' ? slugify($slug) : slugify($name);
        $baseSlug = $slug;
        $counter = 2;
        while (Category::slugExists($section, $slug, $ignoreId)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return [':section' => $section, ':name' => $name, ':slug' => $slug, ':sort_order' => (int) ($_POST['sort_order'] ?? 0)];
    }

    private function sectionPayload(?int $ignoreId = null): array
    {
        $name = trim((string) ($_POST['name'] ?? ''));
        if ($name === '') {
            $name = 'Sección sin título';
        }
        $slug = trim((string) ($_POST['slug'] ?? ''));
        $slug = $slug !== '' ? slugify($slug) : slugify($name);
        $baseSlug = $slug;
        $counter = 2;
        while (Section::slugExists($slug, $ignoreId)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return [
            ':name' => $name,
            ':slug' => $slug,
            ':nav_label' => trim((string) ($_POST['nav_label'] ?? '')) ?: $name,
            ':emoji' => trim((string) ($_POST['emoji'] ?? '')),
            ':description' => trim((string) ($_POST['description'] ?? '')),
            ':sort_order' => (int) ($_POST['sort_order'] ?? 0),
            ':is_active' => !empty($_POST['is_active']) ? 1 : 0,
        ];
    }

    private function storeImage(array $file): string
    {
        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
        $mime = mime_content_type($file['tmp_name']) ?: '';
        if (!isset($allowed[$mime])) {
            $_SESSION['flash_error'] = 'La imagen debe ser JPG, PNG, WEBP o GIF.';
            redirect('/admin/articles/create');
        }

        $dir = config('upload_path');
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $name = date('YmdHis') . '-' . bin2hex(random_bytes(4)) . '.' . $allowed[$mime];
        move_uploaded_file($file['tmp_name'], $dir . '/' . $name);
        return $name;
    }
}
