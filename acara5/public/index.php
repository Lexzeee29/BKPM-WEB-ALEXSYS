<?php

session_start();

define('BASE_URL', rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/'));

require_once __DIR__ . '/../app/Models/Mahasiswa.php';
require_once __DIR__ . '/../app/Controllers/HomeController.php';
require_once __DIR__ . '/../app/Controllers/MahasiswaController.php';
require_once __DIR__ . '/../routes/web.php';

use App\Controllers\MahasiswaController;

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
$basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');

if ($basePath !== '' && str_starts_with($uri, $basePath)) {
    $uri = substr($uri, strlen($basePath)) ?: '/';
}

$uri = rtrim($uri, '/') ?: '/';
$method = $_SERVER['REQUEST_METHOD'];

if (isset($routes[$method][$uri])) {
    [$controllerName, $action] = $routes[$method][$uri];
    $controllerClass = "App\\Controllers\\{$controllerName}";
    $controller = new $controllerClass();
    $controller->$action();
    exit;
}

if ($method === 'GET' && preg_match('#^/mahasiswa/([0-9]+)$#', $uri, $matches)) {
    $controller = new MahasiswaController();
    $controller->show((int) $matches[1]);
    exit;
}

http_response_code(404);
echo '404 - Halaman tidak ditemukan';
