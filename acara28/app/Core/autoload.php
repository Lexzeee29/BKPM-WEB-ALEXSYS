<?php

$environment = getenv('APP_ENV');

defined('APP_ENV') || define(
    'APP_ENV',
    strtolower(
        $environment !== false && $environment !== ''
            ? $environment
            : ($_ENV['APP_ENV'] ?? $_SERVER['APP_ENV'] ?? 'development')
    )
);

spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';

    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $classPath = substr($class, strlen($prefix));
    $classPath = str_replace('\\', '/', $classPath);

    $path = __DIR__ . '/../' . $classPath . '.php';

    if (is_file($path)) {
        require_once $path;
    }
});