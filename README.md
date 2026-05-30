# Niguace.net

CMS en PHP + SQLite para publicar y organizar archivos narrativos, lore, personajes, episodios, localizaciones o cualquier otro contenido editorial por secciones.

## Qué hace

- Sitio público con portada, navegación por secciones, etiquetas y buscador.
- Artículos con título, slug, resumen, cuerpo enriquecido, imagen destacada, autor y fecha de publicación.
- Secciones configurables desde administración:
  - nombre
  - slug
  - texto de menú
  - emoji
  - descripción
  - orden
  - activación/desactivación
- Etiquetas configurables por sección.
- Página informativa tipo `About` accesible desde menú principal.
- Ajustes del sitio desde administración:
  - nombre visible
  - subtítulo
  - título de portada
  - texto superior de portada
  - descripción de portada
- Gestión de usuarios con roles:
  - `admin`: control total
  - `editor`: crea artículos, edita cualquiera y gestiona secciones/etiquetas
  - `author`: crea artículos y edita los suyos
- Reasignación de autor en edición de artículos para `admin` y `editor`.
- Panel de administración con menú común para:
  - entradas
  - nueva entrada
  - secciones
  - etiquetas
  - ajustes web
  - usuarios

## Stack

- PHP 8.2+
- SQLite mediante `pdo_sqlite`
- CKEditor para edición enriquecida
- Frontend server-rendered, sin framework

## Requisitos

- PHP 8.2 o superior
- extensión `pdo_sqlite` activa
- servidor web apuntando a `public/` recomendado

En Debian/Raspberry Pi:

```bash
sudo apt update
sudo apt install php-sqlite3
sudo systemctl restart apache2
```

Comprueba extensión:

```bash
php -m | grep -i sqlite
```

## Instalación

Ejecuta:

```bash
php setup.php
```

Esto crea o actualiza estructura SQLite base y siembra datos iniciales del CMS.

## Configuración local

Si quieres overrides locales, crea `config.local.php`:

```php
<?php

return [
    'default_admin_email' => 'admin@example.test',
    'default_admin_password' => 'cambia-esto',
];
```

También puedes usar variables de entorno:

- `NIGUACE_BASE_URL`
- `NIGUACE_DATABASE_PATH`
- `NIGUACE_UPLOAD_PATH`
- `NIGUACE_UPLOAD_URL`
- `NIGUACE_SESSION_NAME`
- `NIGUACE_ADMIN_EMAIL`
- `NIGUACE_ADMIN_PASSWORD`

## Archivos que no deben subirse

- `config.local.php`
- `database/*.sqlite`
- `public/uploads/*`

`public/uploads/.keep` sí puede permanecer en repo.

## Desarrollo local

```bash
php -S localhost:8080 -t public
```

Luego abre:

```text
http://localhost:8080
```

## Estructura rápida

- `app/Controllers`: controladores público, auth y admin
- `app/Models`: acceso a artículos, usuarios, ajustes, secciones y etiquetas
- `app/Views`: vistas públicas y de administración
- `database/schema.sql`: esquema SQLite
- `public/`: front controller, assets y uploads públicos
- `setup.php`: bootstrap de instalación y seed inicial

## Seguridad básica ya aplicada

- separación entre config versionable y config local
- `gitignore` para uploads, base SQLite y overrides locales
- contraseña inicial no hardcodeada en README
- CSRF en formularios
- permisos por rol en panel
