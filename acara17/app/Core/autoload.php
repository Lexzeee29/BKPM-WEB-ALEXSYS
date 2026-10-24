<?php

spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';

    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $classPath = substr($class, strlen($prefix));

    if (str_starts_with($classPath, 'Config\\')) {
        $classPath = 'config/' . substr($classPath, strlen('Config\\'));
    } else {
        $classPath = str_replace('\\', '/', $classPath);
    }

    $path = __DIR__ . '/../' . $classPath . '.php';

    if (is_file($path)) {
        require_once $path;
    }
});