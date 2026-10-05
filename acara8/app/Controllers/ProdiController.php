<?php

namespace App\Controllers;

use App\Models\Prodi;

class ProdiController
{
    private Prodi $model;

    public function __construct() { $this->model = new Prodi(); }

    public function index(): void
    {
        $items = $this->model->all();
        $title = 'Program Studi | SI Akademik';
        $content = __DIR__ . '/../Views/prodi/index.php';
        require __DIR__ . '/../Views/layouts/main.php';
    }

    public function create(): void
    {
        $title = 'Tambah Program Studi | SI Akademik';
        $content = __DIR__ . '/../Views/prodi/form.php';
        require __DIR__ . '/../Views/layouts/main.php';
    }

    public function edit(int $id): void
    {
        $item = $this->model->find($id);
        if ($item === null) { http_response_code(404); echo '404 - Program studi tidak ditemukan'; return; }
        $title = 'Edit Program Studi | SI Akademik';
        $content = __DIR__ . '/../Views/prodi/form.php';
        require __DIR__ . '/../Views/layouts/main.php';
    }

    public function store(): void
    {
        $data = $this->data();
        if ($data === null) {
            http_response_code(422);
            $error = 'Kode dan nama program studi wajib diisi.';
            $title = 'Tambah Program Studi | SI Akademik';
            $content = __DIR__ . '/../Views/prodi/form.php';
            require __DIR__ . '/../Views/layouts/main.php';
            return;
        }

        if (!$this->model->create($data)) {
            http_response_code(422);
            $error = 'Nama program studi tersebut sudah terdaftar.';
            $title = 'Tambah Program Studi | SI Akademik';
            $content = __DIR__ . '/../Views/prodi/form.php';
            require __DIR__ . '/../Views/layouts/main.php';
            return;
        }

        $_SESSION['flash'] = 'Program studi berhasil ditambahkan.';
        header('Location: ' . BASE_URL . '/prodi'); exit;
    }

    public function update(int $id): void
    {
        $item = $this->model->find($id);
        if ($item === null) {
            http_response_code(404);
            echo '404 - Program studi tidak ditemukan';
            return;
        }

        $data = $this->data();
        if ($data === null) {
            http_response_code(422);
            $error = 'Kode dan nama program studi wajib diisi.';
            $this->editView($item, $error);
            return;
        }

        if (!$this->model->update($id, $data)) {
            http_response_code(422);
            $error = 'Nama program studi tersebut sudah digunakan oleh program studi lain.';
            $item = [...$item, ...$data];
            $this->editView($item, $error);
            return;
        }

        $_SESSION['flash'] = 'Program studi berhasil diperbarui.';
        header('Location: ' . BASE_URL . '/prodi'); exit;
    }

    public function destroy(int $id): void
    {
        try { $this->model->delete($id); $_SESSION['flash'] = 'Program studi berhasil dihapus.'; }
        catch (\PDOException $exception) { $_SESSION['flash'] = 'Program studi tidak dapat dihapus karena masih dipakai mahasiswa atau mata kuliah.'; }
        header('Location: ' . BASE_URL . '/prodi'); exit;
    }

    private function data(): ?array
    {
        $data = ['kode' => trim($_POST['kode'] ?? ''), 'nama' => trim($_POST['nama'] ?? '')];
        return $data['kode'] !== '' && $data['nama'] !== '' ? $data : null;
    }

    private function editView(?array $item, string $error): void
    {
        if ($item === null) { http_response_code(404); echo '404 - Program studi tidak ditemukan'; return; }
        $title = 'Edit Program Studi | SI Akademik'; $content = __DIR__ . '/../Views/prodi/form.php';
        require __DIR__ . '/../Views/layouts/main.php';
    }
}