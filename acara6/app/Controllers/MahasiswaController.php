<?php

namespace App\Controllers;

use App\Models\Mahasiswa;

class MahasiswaController
{
    private function getMahasiswa(): array
    {
        return $_SESSION['mahasiswa'] ?? [
            new Mahasiswa('E41230001', 'Andi Setiawan', 'Teknik Informatika'),
            new Mahasiswa('E41230002', 'Siti Rahma', 'Manajemen Informatika'),
            new Mahasiswa('E41224003', 'Budi Santoso', 'Teknik Komputer'),
        ];
    }

    public function index(): void
    {
        $mahasiswa = $this->getMahasiswa();

        $title = 'Data Mahasiswa | SI Akademik';
        $content = __DIR__ . '/../Views/mahasiswa/index.php';
        require __DIR__ . '/../Views/layouts/main.php';
    }

    public function create(): void
    {
        $title = 'Tambah Mahasiswa | SI Akademik';
        $content = __DIR__ . '/../Views/mahasiswa/create.php';
        require __DIR__ . '/../Views/layouts/main.php';
    }

    public function show(int $id): void
    {
        $mahasiswa = $this->getMahasiswa();
        $index = $id - 1;

        if (!isset($mahasiswa[$index])) {
            http_response_code(404);
            echo '404 - Data mahasiswa tidak ditemukan';
            return;
        }

        $item = $mahasiswa[$index];
        $title = 'Detail Mahasiswa | SI Akademik';
        $content = __DIR__ . '/../Views/mahasiswa/show.php';
        require __DIR__ . '/../Views/layouts/main.php';
    }

    public function store(): void
    {
        $nim = trim($_POST['nim'] ?? '');
        $nama = trim($_POST['nama'] ?? '');
        $prodi = trim($_POST['prodi'] ?? '');

        if ($nim === '' || $nama === '' || $prodi === '') {
            http_response_code(422);
            $error = 'Semua data mahasiswa wajib diisi.';
            $title = 'Tambah Mahasiswa | SI Akademik';
            $content = __DIR__ . '/../Views/mahasiswa/create.php';
            require __DIR__ . '/../Views/layouts/main.php';
            return;
        }

        $mahasiswa = $this->getMahasiswa();
        $mahasiswa[] = new Mahasiswa($nim, $nama, $prodi);
        $_SESSION['mahasiswa'] = $mahasiswa;

        header('Location: ' . BASE_URL . '/mahasiswa');
        exit;
    }
}