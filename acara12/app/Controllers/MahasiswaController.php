<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Repositories\MahasiswaRepository;
use App\Repositories\ProdiRepository;
use App\Services\MahasiswaService;
use InvalidArgumentException;

class MahasiswaController extends BaseController
{
    private MahasiswaRepository $repository;
    private ProdiRepository $prodiRepository;
    private MahasiswaService $service;

    public function __construct(
        MahasiswaRepository $repository,
        ProdiRepository $prodiRepository,
        MahasiswaService $service
    ) {
        $this->repository = $repository;
        $this->prodiRepository = $prodiRepository;
        $this->service = $service;
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
        $prodi = $this->prodiRepository->all();
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

        $prodi = $this->prodiRepository->all();
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
        try {
            $this->service->create($_POST);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Data mahasiswa berhasil ditambahkan.'];
        } catch (InvalidArgumentException $exception) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => $exception->getMessage()];
        }

        $this->redirect(BASE_URL . '/mahasiswa');
    }

    public function update(int $id): void
    {
        try {
            $this->service->update($id, $_POST);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Data mahasiswa berhasil diperbarui.'];
        } catch (InvalidArgumentException $exception) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => $exception->getMessage()];
        }

        $this->redirect(BASE_URL . '/mahasiswa');
    }

    public function destroy(int $id): void
    {
        $this->repository->delete($id);
        $_SESSION['flash'] = 'Data mahasiswa berhasil dihapus.';
        $this->redirect(BASE_URL . '/mahasiswa');
    }

}