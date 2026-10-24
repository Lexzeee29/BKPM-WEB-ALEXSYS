<?php

namespace App\Core\Middleware;

class AuthMiddleware
{
    public function handle(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            $_SESSION['flash'] = 'Silakan login terlebih dahulu.';
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
    }
}