<?php

$routes = [
    'GET' => [
        '/' => ['HomeController', 'index', [\App\Core\Middleware\AuthMiddleware::class]],
        '/login' => ['AuthController', 'loginForm'],
        '/logout' => ['AuthController', 'logout', [\App\Core\Middleware\AuthMiddleware::class]],
        '/dashboard' => ['HomeController', 'dashboard', [\App\Core\Middleware\AuthMiddleware::class]],
        '/mahasiswa' => ['MahasiswaController', 'index', [\App\Core\Middleware\AuthMiddleware::class]],
        '/mahasiswa/create' => ['MahasiswaController', 'create', [\App\Core\Middleware\AuthMiddleware::class]],
        '/mahasiswa/{id}' => ['MahasiswaController', 'show', [\App\Core\Middleware\AuthMiddleware::class]],
    ],
    'POST' => [
        '/login' => ['AuthController', 'login'],
        '/mahasiswa' => ['MahasiswaController', 'store', [\App\Core\Middleware\AuthMiddleware::class]],
    ],
];