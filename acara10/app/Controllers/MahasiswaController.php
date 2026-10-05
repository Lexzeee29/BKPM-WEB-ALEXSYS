<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Models\Mahasiswa;
use App\Repositories\MahasiswaRepository;

class MahasiswaController extends BaseController
{
    private MahasiswaRepository $repository;

    public function __construct(MahasiswaRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(): void
    {
        $search = trim($_GET['search'] ?? '');
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 10;
        $total = $this->repository->count($search);
        $mahasiswa = $this->repository->all($search, $perPage, ($page - 1) * $perPage);
        $totalPages = max(1, (int) ceil($total / $perPage));
        $page = min($page, $totalPages);

        $title = 'Data Mahasiswa | SI Akademik';
        $this->view('mahasiswa/index', compact('mahasiswa', 'search', 'total', 'page', 'totalPages', 'title'));
    }

    public function create(): void
    {
        $prodi = $this->repository->prodiAll();
        $title = 'Tambah Mahasiswa | SI Akademik';
        $this->view('mahasiswa/create', compact('prodi', 'title'));
    }

    public function edit(int $id): void
    {
        $item = $this->repository->find($id);
        if ($item === null) {
            http_response_code(404);
            echo '404 - Data mahasiswa tidak ditemukan';
            return;
        }

        $prodi = $this->repository->prodiAll();
        $title = 'Edit Mahasiswa | SI Akademik';
        $this->view('mahasiswa/edit', compact('item', 'prodi', 'title'));
    }

    public function show(int $id): void
    {
        $item = $this->repository->find($id);

        if ($item === null) {
            http_response_code(404);
            echo '404 - Data mahasiswa tidak ditemukan';
            return;
        }

        $title = 'Detail Mahasiswa | SI Akademik';
        $this->view('mahasiswa/show', compact('item', 'title'));
    }

    public function store(): void
    {
        $mahasiswa = $this->validatedData();

        if ($mahasiswa === null) {
            http_response_code(422);
            $error = 'NIM, nama, email, program studi, dan angkatan harus diisi dengan benar.';
            $prodi = $this->repository->prodiAll();
            $title = 'Tambah Mahasiswa | SI Akademik';
            $this->view('mahasiswa/create', compact('error', 'prodi', 'title'));
            return;
        }

        try {
            $this->repository->create($mahasiswa);
        } catch (\InvalidArgumentException $exception) {
            http_response_code(422);
            $error = $exception->getMessage();
            $prodi = $this->repository->prodiAll();
            $title = 'Tambah Mahasiswa | SI Akademik';
            $this->view('mahasiswa/create', compact('error', 'prodi', 'title'));
            return;
        }

        $_SESSION['flash'] = 'Data mahasiswa berhasil ditambahkan.';
    $this->redirect(BASE_URL . '/mahasiswa');
    }

    public function update(int $id): void
    {
        $mahasiswa = $this->validatedData();
        $item = $this->repository->find($id);

        if ($item === null) {
            http_response_code(404);
            echo '404 - Data mahasiswa tidak ditemukan';
            return;
        }

        if ($mahasiswa === null) {
            http_response_code(422);
            $error = 'NIM, nama, email, program studi, dan angkatan harus diisi dengan benar.';
            $prodi = $this->repository->prodiAll();
            $title = 'Edit Mahasiswa | SI Akademik';
            $this->view('mahasiswa/edit', compact('error', 'item', 'prodi', 'title'));
            return;
        }

        try {
            $mahasiswa->setStatus($_POST['status'] ?? $item['status']);
        } catch (\InvalidArgumentException) {
            http_response_code(422);
            $error = 'Status mahasiswa tidak valid.';
            $prodi = $this->repository->prodiAll();
            $title = 'Edit Mahasiswa | SI Akademik';
            $this->view('mahasiswa/edit', compact('error', 'item', 'prodi', 'title'));
            return;
        }

        try {
            $this->repository->update($id, $mahasiswa);
        } catch (\InvalidArgumentException $exception) {
            http_response_code(422);
            $error = $exception->getMessage();
            $prodi = $this->repository->prodiAll();
            $title = 'Edit Mahasiswa | SI Akademik';
            $this->view('mahasiswa/edit', compact('error', 'item', 'prodi', 'title'));
            return;
        }

        $_SESSION['flash'] = 'Data mahasiswa berhasil diperbarui.';
        $this->redirect(BASE_URL . '/mahasiswa');
    }

    public function destroy(int $id): void
    {
        $this->repository->delete($id);
        $_SESSION['flash'] = 'Data mahasiswa berhasil dihapus.';
        $this->redirect(BASE_URL . '/mahasiswa');
    }

    private function validatedData(): ?Mahasiswa
    {
        try {
            return new Mahasiswa([
                'nim' => trim($_POST['nim'] ?? ''),
                'nama' => trim($_POST['nama'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'prodi_id' => (int) ($_POST['prodi_id'] ?? 0),
                'angkatan' => (int) ($_POST['angkatan'] ?? 0),
            ]);
        } catch (\InvalidArgumentException) {
            return null;
        }
    }
}