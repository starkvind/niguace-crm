<?php

namespace App\Controllers;

use App\Core\View;
use App\Models\Article;
use App\Models\Category;

class PublicController
{
    public function search(): void
    {
        $query = trim((string) ($_GET['q'] ?? ''));

        View::render('public/section', [
            'title' => $query !== '' ? 'Buscar: ' . $query : 'Buscar',
            'metaDescription' => $query !== '' ? 'Resultados de busqueda para ' . $query . '.' : 'Busqueda.',
            'section' => 'search',
            'searchQuery' => $query,
            'articles' => $query !== '' ? Article::searchPublished($query) : [],
        ]);
    }

    public function home(): void
    {
        View::render('public/home', [
            'title' => setting('homepage_title', 'Archivo de campana'),
            'metaDescription' => setting('site_meta_description', setting('hero_description', '')),
            'latest' => array_slice(Article::published(), 0, 6),
        ]);
    }

    public function section(string $section): void
    {
        if (!in_array($section, allowed_sections(), true)) {
            http_response_code(404);
            exit('Sección no encontrada.');
        }

        $sectionRecord = section_record($section);
        View::render('public/section', [
            'title' => $sectionRecord['name'] ?? section_label($section),
            'metaDescription' => $sectionRecord['description'] ?? section_label($section),
            'section' => $section,
            'articles' => Article::published($section),
        ]);
    }

    public function category(string $section, string $categorySlug): void
    {
        if (!in_array($section, allowed_sections(), true)) {
            http_response_code(404);
            exit('Sección no encontrada.');
        }

        $category = Category::findBySectionAndSlug($section, $categorySlug);
        if (!$category) {
            http_response_code(404);
            exit('Etiqueta no encontrada.');
        }

        View::render('public/section', [
            'title' => $category['name'],
            'metaDescription' => 'Entradas con la etiqueta ' . $category['name'] . '.',
            'section' => $section,
            'category' => $category,
            'articles' => Article::publishedByCategory($section, $categorySlug),
        ]);
    }

    public function about(): void
    {
        $article = Article::findPublishedBySlug(setting('about_slug', 'sobre-niguace-net'));
        if (!$article) {
            View::render('public/about_empty', [
                'title' => 'Sobre',
                'metaDescription' => 'Informacion del sitio.',
            ]);
            return;
        }

        View::render('public/article', [
            'title' => $article['title'],
            'metaDescription' => $article['excerpt'] ?: 'Informacion del sitio.',
            'ogImage' => !empty($article['featured_image']) ? url('/uploads/' . $article['featured_image']) : null,
            'article' => $article,
        ]);
    }

    public function article(string $slug): void
    {
        $article = Article::findPublishedBySlug($slug);
        if (!$article) {
            http_response_code(404);
            exit('Entrada no encontrada.');
        }

        View::render('public/article', [
            'title' => $article['title'],
            'metaDescription' => $article['excerpt'] ?: 'Entrada del archivo.',
            'ogImage' => !empty($article['featured_image']) ? url('/uploads/' . $article['featured_image']) : null,
            'article' => $article,
        ]);
    }
}
