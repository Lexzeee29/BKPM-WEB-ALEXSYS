<?php

namespace App\Controllers;

class HomeController
{
    public function index(): void
    {
        header('Location: ' . BASE_URL . '/mahasiswa');
        exit;
    }
}