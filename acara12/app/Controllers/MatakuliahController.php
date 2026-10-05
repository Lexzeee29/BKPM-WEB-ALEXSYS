<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Models\Matakuliah;

class MatakuliahController extends BaseController
{
    private Matakuliah $model;

    public function __construct() { $this->model = new Matakuliah(); }

    public function index(): void
    {
        $items = $this->model->all(); $title = 'Mata Kuliah | SI Akademik';
        $this->view('matakuliah/index', compact('items', 'title'));
    }

    public function create(): void
    {
        $prodi = $this->model->prodiAll(); $title = 'Tambah Mata Kuliah | SI Akademik';
        $this->view('matakuliah/form', compact('prodi', 'title'));
    }

    public function edit(int $id): void
    {
        $item = $this->model->find($id); if ($item === null) { http_response_code(404); echo '404 - Mata kuliah tidak ditemukan'; return; }
        $prodi = $this->model->prodiAll(); $title = 'Edit Mata Kuliah | SI Akademik';
        $this->view('matakuliah/form', compact('item', 'prodi', 'title'));
    }

    public function store(): void
    {
        $data = $this->data();
        if ($data === null) {
            http_response_code(422);
            $error = 'Kode, nama, SKS, dan program studi wajib diisi.';
            $prodi = $this->model->prodiAll();
            $title = 'Tambah Mata Kuliah | SI Akademik';
            $this->view('matakuliah/form', compact('error', 'prodi', 'title'));
            return;
        }
        try {
            $this->model->create($data);
        } catch (\InvalidArgumentException $exception) {
            http_response_code(422);
            $error = $exception->getMessage();
            $prodi = $this->model->prodiAll();
            $title = 'Tambah Mata Kuliah | SI Akademik';
            $this->view('matakuliah/form', compact('error', 'prodi', 'title'));
            return;
        }
        $_SESSION['flash'] = 'Mata kuliah berhasil ditambahkan.'; $this->redirect(BASE_URL . '/matakuliah');
    }

    public function update(int $id): void
    {
        $data = $this->data();
        if ($data === null) {
            http_response_code(422);
            $error = 'Kode, nama, SKS, dan program studi wajib diisi.';
            $item = $this->model->find($id);
            $prodi = $this->model->prodiAll();
            $title = 'Edit Mata Kuliah | SI Akademik';
            $this->view('matakuliah/form', compact('error', 'item', 'prodi', 'title'));
            return;
        }
        try {
            $this->model->update($id, $data);
        } catch (\InvalidArgumentException $exception) {
            http_response_code(422);
            $error = $exception->getMessage();
            $item = $this->model->find($id);
            $prodi = $this->model->prodiAll();
            $title = 'Edit Mata Kuliah | SI Akademik';
            $this->view('matakuliah/form', compact('error', 'item', 'prodi', 'title'));
            return;
        }
        $_SESSION['flash'] = 'Mata kuliah berhasil diperbarui.'; $this->redirect(BASE_URL . '/matakuliah');
    }

    public function destroy(int $id): void { $this->model->delete($id); $_SESSION['flash'] = 'Mata kuliah berhasil dihapus.'; $this->redirect(BASE_URL . '/matakuliah'); }

    private function data(): ?array
    {
        $data = ['kode' => trim($_POST['kode'] ?? ''), 'nama' => trim($_POST['nama'] ?? ''), 'sks' => (int) ($_POST['sks'] ?? 0), 'prodi_id' => (int) ($_POST['prodi_id'] ?? 0)];
        return $data['kode'] !== '' && $data['nama'] !== '' && $data['sks'] > 0 && $data['prodi_id'] > 0 ? $data : null;
    }
}