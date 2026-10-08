<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Services\MahasiswaService;

class MahasiswaController extends BaseController
{
    private MahasiswaService $service;

    public function __construct(
        MahasiswaService $service
    ) {
        $this->service = $service;
    }

    public function index(): void
    {
        $search = trim($_GET['search'] ?? '');

        $page = max(
            1,
            (int) ($_GET['page'] ?? 1)
        );

        $perPage = 10;

        $total = $this->service->count($search);

        $offset = ($page - 1) * $perPage;

        $mahasiswa = $this->service->all(
            $search,
            $perPage,
            $offset
        );

        $totalPages = max(
            1,
            (int) ceil($total / $perPage)
        );

        $page = min($page, $totalPages);

        $title = 'Data Mahasiswa | SI Akademik';

        $content = __DIR__ .
            '/../Views/mahasiswa/index.php';

        $this->view($content, get_defined_vars());
    }

    public function create(): void
    {
        $formData = $_SESSION['old_input'] ?? [];
        unset($_SESSION['old_input']);

        $prodi = $this->service->prodiAll();

        $title = 'Tambah Mahasiswa | SI Akademik';

        $content = __DIR__ .
            '/../Views/mahasiswa/create.php';

        $this->view($content, get_defined_vars());
    }

    public function edit(int $id): void
    {
        $item = $this->service->find($id);

        if ($item === null) {
            http_response_code(404);
            echo '404 - Data mahasiswa tidak ditemukan';
            return;
        }

        $oldInput = $_SESSION['old_input'] ?? [];
        unset($_SESSION['old_input']);
        $item = array_merge($item, $oldInput);

        $prodi = $this->service->prodiAll();

        $title = 'Edit Mahasiswa | SI Akademik';

        $content = __DIR__ .
            '/../Views/mahasiswa/edit.php';

        $this->view($content, get_defined_vars());
    }

    public function show(int $id): void
    {
        $item = $this->service->find($id);

        if ($item === null) {
            http_response_code(404);
            echo '404 - Data mahasiswa tidak ditemukan';
            return;
        }

        $title = 'Detail Mahasiswa | SI Akademik';

        $content = __DIR__ .
            '/../Views/mahasiswa/show.php';

        $this->view($content, get_defined_vars());
    }

    public function store(): void
    {
        if (!$this->service->create($_POST)) {
            $_SESSION['old_input'] = $_POST;
            $this->redirect('/mahasiswa/create');

            return;
        }

        $this->redirect('/mahasiswa');
    }

    public function update(int $id): void
    {
        $item = $this->service->find($id);

        if ($item === null) {
            http_response_code(404);
            echo '404 - Data mahasiswa tidak ditemukan';
            return;
        }

        if (!$this->service->update($id, $_POST)) {
            $_SESSION['old_input'] = $_POST;
            $this->redirect('/mahasiswa/' . $id . '/edit');

            return;
        }

        $this->redirect('/mahasiswa');
    }
    public function destroy(int $id): void
    {
        $this->service->delete($id);
        $this->redirect('/mahasiswa');
    }

}