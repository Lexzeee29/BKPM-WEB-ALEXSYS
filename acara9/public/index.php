<?php

session_start();

define('BASE_URL', rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/'));

require_once __DIR__ . '/../app/Models/Mahasiswa.php';
require_once __DIR__ . '/../app/Models/Prodi.php';
require_once __DIR__ . '/../app/Models/Matakuliah.php';
require_once __DIR__ . '/../app/Core/Database.php';
require_once __DIR__ . '/../app/Repositories/MahasiswaRepository.php';
require_once __DIR__ . '/../app/Core/Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../app/Controllers/HomeController.php';
require_once __DIR__ . '/../app/Controllers/AuthController.php';
require_once __DIR__ . '/../app/Controllers/MahasiswaController.php';
require_once __DIR__ . '/../app/Controllers/ProdiController.php';
require_once __DIR__ . '/../app/Controllers/MatakuliahController.php';
require_once __DIR__ . '/../routes/web.php';

use App\Controllers\MahasiswaController;
use App\Core\Database;
use App\Repositories\MahasiswaRepository;

$createMahasiswaController = static fn (): MahasiswaController => new MahasiswaController(
    new MahasiswaRepository(new Database())
);

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
$basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');

if ($basePath !== '' && str_starts_with($uri, $basePath)) {
    $uri = substr($uri, strlen($basePath)) ?: '/';
}

$uri = rtrim($uri, '/') ?: '/';
$method = $_SERVER['REQUEST_METHOD'];

if (isset($routes[$method][$uri])) {
    [$controllerName, $action, $middleware] = array_pad($routes[$method][$uri], 3, []);

    foreach ($middleware as $middlewareClass) {
        (new $middlewareClass())->handle();
    }

    $controllerClass = "App\\Controllers\\{$controllerName}";
    $controller = $controllerClass === MahasiswaController::class
        ? $createMahasiswaController()
        : new $controllerClass();
    $controller->$action();
    exit;
}

if ($method === 'GET' && preg_match('#^/mahasiswa/([0-9]+)$#', $uri, $matches)) {
    (new \App\Core\Middleware\AuthMiddleware())->handle();
    $controller = $createMahasiswaController();
    $controller->show((int) $matches[1]);
    exit;
}

if ($method === 'GET' && preg_match('#^/mahasiswa/([0-9]+)/edit$#', $uri, $matches)) {
    (new \App\Core\Middleware\AuthMiddleware())->handle();
    $createMahasiswaController()->edit((int) $matches[1]);
    exit;
}

if ($method === 'POST' && preg_match('#^/mahasiswa/([0-9]+)/delete$#', $uri, $matches)) {
    (new \App\Core\Middleware\AuthMiddleware())->handle();
    $createMahasiswaController()->destroy((int) $matches[1]);
    exit;
}

if ($method === 'POST' && preg_match('#^/mahasiswa/([0-9]+)$#', $uri, $matches)) {
    (new \App\Core\Middleware\AuthMiddleware())->handle();
    $createMahasiswaController()->update((int) $matches[1]);
    exit;
}

foreach (['prodi' => 'ProdiController', 'matakuliah' => 'MatakuliahController'] as $resource => $controllerName) {
    if ($method === 'GET' && preg_match("#^/{$resource}/([0-9]+)/edit$#", $uri, $matches)) {
        (new \App\Core\Middleware\AuthMiddleware())->handle();
        (new ("App\\Controllers\\{$controllerName}")())->edit((int) $matches[1]);
        exit;
    }
    if ($method === 'POST' && preg_match("#^/{$resource}/([0-9]+)/delete$#", $uri, $matches)) {
        (new \App\Core\Middleware\AuthMiddleware())->handle();
        (new ("App\\Controllers\\{$controllerName}")())->destroy((int) $matches[1]);
        exit;
    }
    if ($method === 'POST' && preg_match("#^/{$resource}/([0-9]+)$#", $uri, $matches)) {
        (new \App\Core\Middleware\AuthMiddleware())->handle();
        (new ("App\\Controllers\\{$controllerName}")())->update((int) $matches[1]);
        exit;
    }
}

http_response_code(404);
echo '404 - Halaman tidak ditemukan';
