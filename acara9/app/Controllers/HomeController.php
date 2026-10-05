<?php

namespace App\Controllers;

class HomeController
{
    public function index(): void
    {
        header('Location: ' . BASE_URL . '/dashboard');
        exit;
    }

    public function dashboard(): void
    {
        $title = 'Dashboard | SI Akademik';
        $content = __DIR__ . '/../Views/dashboard.php';
        require __DIR__ . '/../Views/layouts/main.php';
    }
}
