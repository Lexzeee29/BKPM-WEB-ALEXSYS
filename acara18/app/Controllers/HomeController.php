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
        $content = \VIEW_PATH . '/dashboard.php';
        require \VIEW_PATH . '/layouts/main.php';
    }
}
