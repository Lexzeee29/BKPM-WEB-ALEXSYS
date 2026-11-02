<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

session_start();

if (
    ($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS' &&
    isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])
) {
    http_response_code(204);
    exit;
}

define(
    'BASE_URL',
    rtrim(
        str_replace(
            '\\',
            '/',
            dirname($_SERVER['SCRIPT_NAME'])
        ),
        '/'
    )
);
define('VIEW_PATH', __DIR__ . '/views');

require_once __DIR__ . '/../app/Core/autoload.php';

set_exception_handler(static function (Throwable $exception): void {
    \App\Core\Logger::error($exception);

    if (!headers_sent()) {
        http_response_code(500);
    }

    echo 'Terjadi kesalahan pada server.';
});

require_once __DIR__ . '/../routes/web.php';


/*
|--------------------------------------------------------------------------
| URI
|--------------------------------------------------------------------------
*/

$uri = parse_url(
    $_SERVER['REQUEST_URI'],
    PHP_URL_PATH
) ?: '/';

$basePath = rtrim(
    str_replace(
        '\\',
        '/',
        dirname($_SERVER['SCRIPT_NAME'])
    ),
    '/'
);

if (
    $basePath !== '' &&
    str_starts_with($uri, $basePath)
) {
    $uri = substr(
        $uri,
        strlen($basePath)
    ) ?: '/';
}

$uri = rtrim($uri, '/') ?: '/';

$method = $_SERVER['REQUEST_METHOD'];

if (preg_match('#^/api/mahasiswa(?:/|$)#', $uri)) {
    $_SERVER['API_REQUEST_PATH'] = $uri;
    require __DIR__ . '/../api/mahasiswa.php';
    exit;
}

if (preg_match('#^/api/[^/]+(?:/|$)#', $uri)) {
    (new \App\Core\Router())->dispatch($uri);
    exit;
}


/*
|--------------------------------------------------------------------------
| Routes
|--------------------------------------------------------------------------
*/

if (isset($routes[$method][$uri])) {

    [
        $controllerName,
        $action,
        $middleware
    ] = array_pad(
        $routes[$method][$uri],
        3,
        []
    );

    /*
    |--------------------------------------------------------------------------
    | Middleware
    |--------------------------------------------------------------------------
    */

    foreach ($middleware as $middlewareClass) {
        (new $middlewareClass())->handle();
    }


    $controllerClass = "App\\Controllers\\{$controllerName}";
    $controller = new $controllerClass();


    /*
    |--------------------------------------------------------------------------
    | Action
    |--------------------------------------------------------------------------
    */

    $controller->$action();

    exit;
}


/*
|--------------------------------------------------------------------------
| 404
|--------------------------------------------------------------------------
*/

http_response_code(404);

echo '404 - Halaman tidak ditemukan';