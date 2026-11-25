<?php

$routes = [
    'GET' => [
        '/' => ['HomeController', 'index', [\App\Core\Middleware\AuthMiddleware::class]],
        '/login' => ['AuthController', 'loginForm'],
        '/register' => ['AuthController', 'registerForm'],
        '/logout' => ['AuthController', 'logout', [\App\Core\Middleware\AuthMiddleware::class]],
        '/dashboard' => ['HomeController', 'dashboard', [\App\Core\Middleware\AuthMiddleware::class]],
        '/mahasiswa' => ['HomeController', 'dashboard', [\App\Core\Middleware\AuthMiddleware::class]],
    ],
    'POST' => [
        '/login' => ['AuthController', 'login'],
        '/register' => ['AuthController', 'register'],
    ],
];