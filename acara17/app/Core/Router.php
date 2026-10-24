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
            $this->respond(404, [
                'success' => false,
                'message' => 'Endpoint tidak ditemukan',
                'data' => null,
            ]);
        }

        $resource = $matches[1];
        $id = isset($matches[2]) ? (int) $matches[2] : null;
        $controllerClass = $this->resources[$resource] ?? null;

        if ($controllerClass === null || ($id !== null && $id < 1)) {
            $this->respond(404, [
                'success' => false,
                'message' => 'Endpoint tidak ditemukan',
                'data' => null,
            ]);
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
        $this->respond(405, [
            'success' => false,
            'message' => 'Method tidak diizinkan untuk endpoint ini',
            'data' => null,
        ]);
    }

    private function respond(int $status, array $body): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
}