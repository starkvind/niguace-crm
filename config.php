<?php

$config = [
    'app_name' => 'Niguace.net',
    'base_url' => getenv('NIGUACE_BASE_URL') ?: '',
    'database_path' => getenv('NIGUACE_DATABASE_PATH') ?: __DIR__ . '/database/niguace.sqlite',
    'upload_path' => getenv('NIGUACE_UPLOAD_PATH') ?: __DIR__ . '/public/uploads',
    'upload_url' => getenv('NIGUACE_UPLOAD_URL') ?: '/uploads',
    'session_name' => getenv('NIGUACE_SESSION_NAME') ?: 'niguace_net_session',
    'default_admin_email' => getenv('NIGUACE_ADMIN_EMAIL') ?: 'admin@niguace.local',
    'default_admin_password' => getenv('NIGUACE_ADMIN_PASSWORD') ?: '',
];

$localConfig = __DIR__ . '/config.local.php';
if (is_file($localConfig)) {
    $overrides = require $localConfig;
    if (is_array($overrides)) {
        $config = array_replace($config, $overrides);
    }
}

return $config;
