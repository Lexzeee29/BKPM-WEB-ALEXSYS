<?php

$routes = [
    'GET' => [
        '/' => ['HomeController', 'index', [\App\Core\Middleware\AuthMiddleware::class]],
        '/login' => ['AuthController', 'loginForm'],
        '/logout' => ['AuthController', 'logout', [\App\Core\Middleware\AuthMiddleware::class]],
        '/dashboard' => ['HomeController', 'dashboard', [\App\Core\Middleware\AuthMiddleware::class]],
        '/mahasiswa' => ['MahasiswaController', 'index', [\App\Core\Middleware\AuthMiddleware::class]],
        '/mahasiswa/create' => ['MahasiswaController', 'create', [\App\Core\Middleware\AuthMiddleware::class]],
        '/mahasiswa/{id}/edit' => ['MahasiswaController', 'edit', [\App\Core\Middleware\AuthMiddleware::class]],
        '/mahasiswa/{id}' => ['MahasiswaController', 'show', [\App\Core\Middleware\AuthMiddleware::class]],
        '/prodi' => ['ProdiController', 'index', [\App\Core\Middleware\AuthMiddleware::class]],
        '/prodi/create' => ['ProdiController', 'create', [\App\Core\Middleware\AuthMiddleware::class]],
        '/prodi/{id}/edit' => ['ProdiController', 'edit', [\App\Core\Middleware\AuthMiddleware::class]],
        '/matakuliah' => ['MatakuliahController', 'index', [\App\Core\Middleware\AuthMiddleware::class]],
        '/matakuliah/create' => ['MatakuliahController', 'create', [\App\Core\Middleware\AuthMiddleware::class]],
        '/matakuliah/{id}/edit' => ['MatakuliahController', 'edit', [\App\Core\Middleware\AuthMiddleware::class]],
    ],
    'POST' => [
        '/login' => ['AuthController', 'login'],
        '/mahasiswa' => ['MahasiswaController', 'store', [\App\Core\Middleware\AuthMiddleware::class]],
        '/mahasiswa/{id}' => ['MahasiswaController', 'update', [\App\Core\Middleware\AuthMiddleware::class]],
        '/mahasiswa/{id}/delete' => ['MahasiswaController', 'destroy', [\App\Core\Middleware\AuthMiddleware::class]],
        '/prodi' => ['ProdiController', 'store', [\App\Core\Middleware\AuthMiddleware::class]],
        '/prodi/{id}' => ['ProdiController', 'update', [\App\Core\Middleware\AuthMiddleware::class]],
        '/prodi/{id}/delete' => ['ProdiController', 'destroy', [\App\Core\Middleware\AuthMiddleware::class]],
        '/matakuliah' => ['MatakuliahController', 'store', [\App\Core\Middleware\AuthMiddleware::class]],
        '/matakuliah/{id}' => ['MatakuliahController', 'update', [\App\Core\Middleware\AuthMiddleware::class]],
        '/matakuliah/{id}/delete' => ['MatakuliahController', 'destroy', [\App\Core\Middleware\AuthMiddleware::class]],
    ],
];