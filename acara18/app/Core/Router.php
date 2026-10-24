<?php

namespace App\Core;

use App\Controllers\BukuController;

class Router
{
    private array $resources = [
        'buku' => BukuController::class,
    ];

    public function dispatch(string $uri): void
    {
        if (!preg_match('#^/api/([^/]+)(?:/([0-9]+))?$#', $uri, $matches)) {
            Response::json(404, false, 'Endpoint tidak ditemukan');
        }

        $resource = $matches[1];
        $id = isset($matches[2]) ? (int) $matches[2] : null;
        $controllerClass = $this->resources[$resource] ?? null;

        if ($controllerClass === null || ($id !== null && $id < 1)) {
            Response::json(404, false, 'Endpoint tidak ditemukan');
        }

        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $controller = new $controllerClass();

        if ($method === 'GET') {
            $id === null ? $controller->index() : $controller->show($id);
            return;
        }

        if ($method === 'POST' && $id === null) {
            $controller->store();
            return;
        }

        if ($method === 'PUT' && $id !== null) {
            $controller->update($id);
            return;
        }

        if ($method === 'DELETE' && $id !== null) {
            $controller->destroy($id);
            return;
        }

        header('Allow: ' . ($id === null ? 'GET, POST' : 'GET, PUT, DELETE'));
        Response::json(405, false, 'Method tidak diizinkan untuk endpoint ini');
    }
}