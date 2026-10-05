<?php

namespace App\Controllers;

use App\Models\Matakuliah;

class MatakuliahController
{
    private Matakuliah $model;

    public function __construct() { $this->model = new Matakuliah(); }

    public function index(): void
    {
        $items = $this->model->all(); $title = 'Mata Kuliah | SI Akademik'; $content = __DIR__ . '/../Views/matakuliah/index.php';
        require __DIR__ . '/../Views/layouts/main.php';
    }

    public function create(): void
    {
        $prodi = $this->model->prodiAll(); $title = 'Tambah Mata Kuliah | SI Akademik'; $content = __DIR__ . '/../Views/matakuliah/form.php';
        require __DIR__ . '/../Views/layouts/main.php';
    }

    public function edit(int $id): void
    {
        $item = $this->model->find($id); if ($item === null) { http_response_code(404); echo '404 - Mata kuliah tidak ditemukan'; return; }
        $prodi = $this->model->prodiAll(); $title = 'Edit Mata Kuliah | SI Akademik'; $content = __DIR__ . '/../Views/matakuliah/form.php';
        require __DIR__ . '/../Views/layouts/main.php';
    }

    public function store(): void
    {
        $data = $this->data();
        if ($data === null) { http_response_code(422); $error = 'Kode, nama, SKS, dan program studi wajib diisi.'; $item = $_POST; $prodi = $this->model->prodiAll(); $title = 'Tambah Mata Kuliah | SI Akademik'; $content = __DIR__ . '/../Views/matakuliah/form.php'; require __DIR__ . '/../Views/layouts/main.php'; return; }
        if (!$this->model->create($data)) { http_response_code(422); $error = 'Nama mata kuliah tersebut sudah terdaftar.'; $item = $data; $prodi = $this->model->prodiAll(); $title = 'Tambah Mata Kuliah | SI Akademik'; $content = __DIR__ . '/../Views/matakuliah/form.php'; require __DIR__ . '/../Views/layouts/main.php'; return; }
        $_SESSION['flash'] = 'Mata kuliah berhasil ditambahkan.'; header('Location: ' . BASE_URL . '/matakuliah'); exit;
    }

    public function update(int $id): void
    {
        $item = $this->model->find($id); if ($item === null) { http_response_code(404); echo '404 - Mata kuliah tidak ditemukan'; return; }
        $data = $this->data(); if ($data === null) { http_response_code(422); $error = 'Kode, nama, SKS, dan program studi wajib diisi.'; $item = [...$item, ...$_POST]; $prodi = $this->model->prodiAll(); $title = 'Edit Mata Kuliah | SI Akademik'; $content = __DIR__ . '/../Views/matakuliah/form.php'; require __DIR__ . '/../Views/layouts/main.php'; return; }
        if (!$this->model->update($id, $data)) { http_response_code(422); $error = 'Nama mata kuliah tersebut sudah digunakan oleh mata kuliah lain.'; $item = [...$item, ...$data]; $prodi = $this->model->prodiAll(); $title = 'Edit Mata Kuliah | SI Akademik'; $content = __DIR__ . '/../Views/matakuliah/form.php'; require __DIR__ . '/../Views/layouts/main.php'; return; }
        $_SESSION['flash'] = 'Mata kuliah berhasil diperbarui.'; header('Location: ' . BASE_URL . '/matakuliah'); exit;
    }

    public function destroy(int $id): void { $this->model->delete($id); $_SESSION['flash'] = 'Mata kuliah berhasil dihapus.'; header('Location: ' . BASE_URL . '/matakuliah'); exit; }

    private function data(): ?array
    {
        $data = ['kode' => trim($_POST['kode'] ?? ''), 'nama' => trim($_POST['nama'] ?? ''), 'sks' => (int) ($_POST['sks'] ?? 0), 'prodi_id' => (int) ($_POST['prodi_id'] ?? 0)];
        return $data['kode'] !== '' && $data['nama'] !== '' && $data['sks'] > 0 && $data['prodi_id'] > 0 ? $data : null;
    }
}