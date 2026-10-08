<?php

session_start();

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

require_once __DIR__ . '/../app/Core/BaseModel.php';
require_once __DIR__ . '/../app/Core/BaseController.php';

require_once __DIR__ . '/../app/Models/Mahasiswa.php';
require_once __DIR__ . '/../app/Models/Prodi.php';
require_once __DIR__ . '/../app/Models/Matakuliah.php';

require_once __DIR__ . '/../app/Core/Database.php';

require_once __DIR__ . '/../app/Repositories/MahasiswaRepository.php';
require_once __DIR__ . '/../app/Repositories/ProdiRepository.php';
require_once __DIR__ . '/../app/Services/MahasiswaService.php';

require_once __DIR__ . '/../app/Core/Middleware/AuthMiddleware.php';

require_once __DIR__ . '/../app/Controllers/HomeController.php';
require_once __DIR__ . '/../app/Controllers/AuthController.php';
require_once __DIR__ . '/../app/Controllers/MahasiswaController.php';
require_once __DIR__ . '/../app/Controllers/ProdiController.php';
require_once __DIR__ . '/../app/Controllers/MatakuliahController.php';

require_once __DIR__ . '/../routes/web.php';

use App\Core\Database;
use App\Repositories\MahasiswaRepository;
use App\Repositories\ProdiRepository;
use App\Services\MahasiswaService;


/*
|--------------------------------------------------------------------------
| Mahasiswa Controller Factory
|--------------------------------------------------------------------------
*/

$mahasiswaControllerClass = 'App\\Controllers\\MahasiswaController';

$createMahasiswaController = static function () use ($mahasiswaControllerClass) {

    $database = Database::getInstance();

    $repository = new MahasiswaRepository(
        $database
    );

    $prodiRepository = new ProdiRepository($database);

    $service = new MahasiswaService(
        $repository,
        $prodiRepository
    );

    return new $mahasiswaControllerClass($service);
};


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


    /*
    |--------------------------------------------------------------------------
    | Controller
    |--------------------------------------------------------------------------
    */

    $controllerClass =
        "App\\Controllers\\{$controllerName}";


    if (
        $controllerClass ===
        $mahasiswaControllerClass
    ) {

        $controller =
            $createMahasiswaController();

    } else {

        $controller =
            new $controllerClass();
    }


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
| GET /mahasiswa/{id}
|--------------------------------------------------------------------------
*/

if (
    $method === 'GET' &&
    preg_match(
        '#^/mahasiswa/([0-9]+)$#',
        $uri,
        $matches
    )
) {

    (new \App\Core\Middleware\AuthMiddleware())
        ->handle();

    $controller =
        $createMahasiswaController();

    $controller->show(
        (int) $matches[1]
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| GET /mahasiswa/{id}/edit
|--------------------------------------------------------------------------
*/

if (
    $method === 'GET' &&
    preg_match(
        '#^/mahasiswa/([0-9]+)/edit$#',
        $uri,
        $matches
    )
) {

    (new \App\Core\Middleware\AuthMiddleware())
        ->handle();

    $controller =
        $createMahasiswaController();

    $controller->edit(
        (int) $matches[1]
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| POST /mahasiswa/{id}/delete
|--------------------------------------------------------------------------
*/

if (
    $method === 'POST' &&
    preg_match(
        '#^/mahasiswa/([0-9]+)/delete$#',
        $uri,
        $matches
    )
) {

    (new \App\Core\Middleware\AuthMiddleware())
        ->handle();

    $controller =
        $createMahasiswaController();

    $controller->destroy(
        (int) $matches[1]
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| POST /mahasiswa/{id}
|--------------------------------------------------------------------------
*/

if (
    $method === 'POST' &&
    preg_match(
        '#^/mahasiswa/([0-9]+)$#',
        $uri,
        $matches
    )
) {

    (new \App\Core\Middleware\AuthMiddleware())
        ->handle();

    $controller =
        $createMahasiswaController();

    $controller->update(
        (int) $matches[1]
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| Prodi & Matakuliah
|--------------------------------------------------------------------------
*/

foreach (
    [
        'prodi' => 'ProdiController',
        'matakuliah' => 'MatakuliahController'
    ]
    as $resource => $controllerName
) {

    /*
    |--------------------------------------------------------------------------
    | Edit
    |--------------------------------------------------------------------------
    */

    if (
        $method === 'GET' &&
        preg_match(
            "#^/{$resource}/([0-9]+)/edit$#",
            $uri,
            $matches
        )
    ) {

        (new \App\Core\Middleware\AuthMiddleware())
            ->handle();

        $controllerClass =
            "App\\Controllers\\{$controllerName}";

        $controller =
            new $controllerClass();

        $controller->edit(
            (int) $matches[1]
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    if (
        $method === 'POST' &&
        preg_match(
            "#^/{$resource}/([0-9]+)/delete$#",
            $uri,
            $matches
        )
    ) {

        (new \App\Core\Middleware\AuthMiddleware())
            ->handle();

        $controllerClass =
            "App\\Controllers\\{$controllerName}";

        $controller =
            new $controllerClass();

        $controller->destroy(
            (int) $matches[1]
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    if (
        $method === 'POST' &&
        preg_match(
            "#^/{$resource}/([0-9]+)$#",
            $uri,
            $matches
        )
    ) {

        (new \App\Core\Middleware\AuthMiddleware())
            ->handle();

        $controllerClass =
            "App\\Controllers\\{$controllerName}";

        $controller =
            new $controllerClass();

        $controller->update(
            (int) $matches[1]
        );

        exit;
    }
}


/*
|--------------------------------------------------------------------------
| 404
|--------------------------------------------------------------------------
*/

http_response_code(404);

echo '404 - Halaman tidak ditemukan';