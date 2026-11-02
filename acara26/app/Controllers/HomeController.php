<?php

namespace App\Controllers;

use App\Core\Auth;

class HomeController
{
    public function index(): void
    {
        header('Location: ' . BASE_URL . '/dashboard');
        exit;
    }

    public function dashboard(): void
    {
        $apiToken = null;

        if (($_SESSION['user_role'] ?? null) === 'admin') {
            $apiToken = Auth::generateToken([
                'id' => (int) ($_SESSION['user_id'] ?? 0),
                'role' => 'admin',
            ]);
        }

        $title = 'Dashboard | SI Akademik';
        $content = \VIEW_PATH . '/dashboard.php';
        require \VIEW_PATH . '/layouts/main.php';
    }
}
