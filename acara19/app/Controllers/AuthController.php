<?php

namespace App\Controllers;

use App\Core\Response;
use App\Models\User;

class AuthController
{
    private User $model;

    public function __construct()
    {
        $this->model = new User();
    }

    public function loginForm(): void
    {
        if (!empty($_SESSION['logged_in'])) {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        $flash = $this->getFlash();
        $title = 'Login | SI Akademik';
        $content = \VIEW_PATH . '/auth/login.php';
        require \VIEW_PATH . '/layouts/main.php';
    }

    public function login(): void
    {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        $user = $this->model->authenticate($username, $password);

        if ($user !== null) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['username'];
            $_SESSION['logged_in'] = true;
            $_SESSION['flash'] = 'Selamat datang, ' . $user['username'];

            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        $error = 'Username atau password salah.';
        $title = 'Login | SI Akademik';
        $content = \VIEW_PATH . '/auth/login.php';
        require \VIEW_PATH . '/layouts/main.php';
    }

    public function registerForm(): void
    {
        if (!empty($_SESSION['logged_in'])) {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        $title = 'Register | SI Akademik';
        $content = \VIEW_PATH . '/auth/register.php';
        $error = $_SESSION['register_error'] ?? null;
        unset($_SESSION['register_error']);
        require \VIEW_PATH . '/layouts/main.php';
    }

    public function register(): void
    {
        $data = $_POST;

        if (empty($data)) {
            $rawInput = file_get_contents('php://input');
            $jsonData = $rawInput !== false && trim($rawInput) !== '' ? json_decode($rawInput, true) : [];
            if (is_array($jsonData)) {
                $data = $jsonData;
            }
        }

        $username = trim((string) ($data['username'] ?? ''));
        $email = trim((string) ($data['email'] ?? ''));
        $password = (string) ($data['password'] ?? '');

        if ($username === '' || $email === '' || $password === '') {
            $errors = [];
            if ($username === '') {
                $errors['username'] = 'Username wajib diisi';
            }
            if ($email === '') {
                $errors['email'] = 'Email wajib diisi';
            }
            if ($password === '') {
                $errors['password'] = 'Password wajib diisi';
            }

            $this->handleRegisterFailure('Username, email, dan password wajib diisi', 422, $errors);
            return;
        }

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $this->handleRegisterFailure('Format email tidak valid', 422, ['email' => 'Format email tidak valid']);
            return;
        }

        if ($this->model->findByUsername($username) !== null) {
            $this->handleRegisterFailure('Username sudah terdaftar', 409);
            return;
        }

        if ($this->model->findByEmail($email) !== null) {
            $this->handleRegisterFailure('Email sudah terdaftar', 409);
            return;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $userId = $this->model->create([
            'username' => $username,
            'email' => $email,
            'password' => $hashedPassword,
        ]);

        if ($userId === false || $userId <= 0) {
            $this->handleRegisterFailure('Registrasi gagal, silakan coba lagi');
            return;
        }

        if ($this->isJsonRequest()) {
            Response::json(201, true, 'Registrasi berhasil', ['id' => $userId]);
            return;
        }

        $_SESSION['flash'] = 'Registrasi berhasil. Silakan login.';
        header('Location: ' . BASE_URL . '/login');
        exit;
    }

    private function handleRegisterFailure(string $message, int $status = 400, array $errors = []): void
    {
        if ($this->isJsonRequest()) {
            Response::json($status, false, $message, null, $errors);
            return;
        }

        $_SESSION['register_error'] = $message;
        header('Location: ' . BASE_URL . '/register');
        exit;
    }

    private function isJsonRequest(): bool
    {
        $contentType = strtolower($_SERVER['CONTENT_TYPE'] ?? '');

        return str_contains($contentType, 'application/json');
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