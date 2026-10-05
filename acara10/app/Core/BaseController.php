<?php

namespace App\Core;

class BaseController
{
    protected function view(string $content, array $data = []): void
    {
        extract($data, EXTR_SKIP);

        require __DIR__ . '/../Views/layouts/main.php';
    }

    protected function redirect(string $path): void
    {
        header('Location: ' . BASE_URL . $path);
        exit;
    }
}