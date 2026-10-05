<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Models\Prodi;

class ProdiController extends BaseController
{
    private Prodi $model;

    public function __construct() { $this->model = new Prodi(); }

    public function index(): void
    {
        $items = $this->model->all();
        $title = 'Program Studi | SI Akademik';
        $this->view('prodi/index', compact('items', 'title'));
    }

    public function create(): void
    {
        $title = 'Tambah Program Studi | SI Akademik';
        $this->view('prodi/form', compact('title'));
    }

    public function edit(int $id): void
    {
        $item = $this->model->find($id);
        if ($item === null) { http_response_code(404); echo '404 - Program studi tidak ditemukan'; return; }
        $title = 'Edit Program Studi | SI Akademik';
        $this->view('prodi/form', compact('item', 'title'));
    }

    public function store(): void
    {
        $data = $this->data();
        if ($data === null) {
            http_response_code(422);
            $error = 'Kode dan nama program studi wajib diisi.';
            $title = 'Tambah Program Studi | SI Akademik';
            $this->view('prodi/form', compact('error', 'title'));
            return;
        }
        try {
            $this->model->create($data);
        } catch (\InvalidArgumentException $exception) {
            http_response_code(422);
            $error = $exception->getMessage();
            $title = 'Tambah Program Studi | SI Akademik';
            $this->view('prodi/form', compact('error', 'title'));
            return;
        }
        $_SESSION['flash'] = 'Program studi berhasil ditambahkan.';
        $this->redirect(BASE_URL . '/prodi');
    }

    public function update(int $id): void
    {
        $data = $this->data();
        if ($data === null) {
            http_response_code(422);
            $error = 'Kode dan nama program studi wajib diisi.';
            $item = $this->model->find($id);
            $this->editView($item, $error);
            return;
        }
        try {
            $this->model->update($id, $data);
        } catch (\InvalidArgumentException $exception) {
            http_response_code(422);
            $error = $exception->getMessage();
            $item = $this->model->find($id);
            $this->editView($item, $error);
            return;
        }
        $_SESSION['flash'] = 'Program studi berhasil diperbarui.';
        $this->redirect(BASE_URL . '/prodi');
    }

    public function destroy(int $id): void
    {
        try { $this->model->delete($id); $_SESSION['flash'] = 'Program studi berhasil dihapus.'; }
        catch (\PDOException $exception) { $_SESSION['flash'] = 'Program studi tidak dapat dihapus karena masih dipakai mahasiswa atau mata kuliah.'; }
        $this->redirect(BASE_URL . '/prodi');
    }

    private function data(): ?array
    {
        $data = ['kode' => trim($_POST['kode'] ?? ''), 'nama' => trim($_POST['nama'] ?? '')];
        return $data['kode'] !== '' && $data['nama'] !== '' ? $data : null;
    }

    private function editView(?array $item, string $error): void
    {
        if ($item === null) { http_response_code(404); echo '404 - Program studi tidak ditemukan'; return; }
        $title = 'Edit Program Studi | SI Akademik';
        $this->view('prodi/form', compact('item', 'error', 'title'));
    }
}