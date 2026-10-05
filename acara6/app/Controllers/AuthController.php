<?php

namespace App\Controllers;

class AuthController
{
    public function loginForm(): void
    {
        if (!empty($_SESSION['logged_in'])) {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        $flash = $this->getFlash();
        $title = 'Login | SI Akademik';
        $content = __DIR__ . '/../Views/auth/login.php';
        require __DIR__ . '/../Views/layouts/main.php';
    }

    public function login(): void
    {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === 'admin' && $password === 'admin123') {
            $_SESSION['user_id'] = 1;
            $_SESSION['user_name'] = 'Admin';
            $_SESSION['logged_in'] = true;
            $_SESSION['flash'] = 'Selamat datang, Admin';

            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        $error = 'Username atau password salah.';
        $title = 'Login | SI Akademik';
        $content = __DIR__ . '/../Views/auth/login.php';
        require __DIR__ . '/../Views/layouts/main.php';
    }

    public function logout(): void
    {
        $_SESSION = [];
        session_destroy();
        session_start();
        $_SESSION['flash'] = 'Anda telah logout.';

        header('Location: ' . BASE_URL . '/login');
        exit;
    }

    private function getFlash(): ?string
    {
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        return $flash;
    }
}