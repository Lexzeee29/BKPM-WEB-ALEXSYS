<?php

namespace App\Controllers;

use App\Models\Mahasiswa;

class MahasiswaController
{
    private Mahasiswa $model;

    public function __construct()
    {
        $this->model = new Mahasiswa();
    }

    public function index(): void
    {
        $mahasiswa = $this->model->all();

        $title = 'Data Mahasiswa | SI Akademik';
        $content = __DIR__ . '/../Views/mahasiswa/index.php';
        require __DIR__ . '/../Views/layouts/main.php';
    }

    public function create(): void
    {
        $prodi = $this->model->prodiAll();
        $title = 'Tambah Mahasiswa | SI Akademik';
        $content = __DIR__ . '/../Views/mahasiswa/create.php';
        require __DIR__ . '/../Views/layouts/main.php';
    }

    public function show(int $id): void
    {
        $item = $this->model->find($id);

        if ($item === null) {
            http_response_code(404);
            echo '404 - Data mahasiswa tidak ditemukan';
            return;
        }

        $title = 'Detail Mahasiswa | SI Akademik';
        $content = __DIR__ . '/../Views/mahasiswa/show.php';
        require __DIR__ . '/../Views/layouts/main.php';
    }

    public function store(): void
    {
        $nim = trim($_POST['nim'] ?? '');
        $nama = trim($_POST['nama'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $prodiId = (int) ($_POST['prodi_id'] ?? 0);
        $angkatan = (int) ($_POST['angkatan'] ?? 0);

        if ($nim === '' || $nama === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $prodiId < 1 || $angkatan < 2000) {
            http_response_code(422);
            $error = 'NIM, nama, email, program studi, dan angkatan harus diisi dengan benar.';
            $prodi = $this->model->prodiAll();
            $title = 'Tambah Mahasiswa | SI Akademik';
            $content = __DIR__ . '/../Views/mahasiswa/create.php';
            require __DIR__ . '/../Views/layouts/main.php';
            return;
        }

        $this->model->create([
            'nim' => $nim,
            'nama' => $nama,
            'email' => $email,
            'prodi_id' => $prodiId,
            'angkatan' => $angkatan,
            'status' => 'aktif',
        ]);

        header('Location: ' . BASE_URL . '/mahasiswa');
        exit;
    }
}