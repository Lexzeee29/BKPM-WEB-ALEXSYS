<?php

namespace App\Controllers;

use App\Repositories\MahasiswaRepository;

class MahasiswaController
{
    private MahasiswaRepository $repository;

    public function __construct(
        MahasiswaRepository $repository
    ) {
        $this->repository = $repository;
    }

    public function index(): void
    {
        $search = trim($_GET['search'] ?? '');

        $page = max(
            1,
            (int) ($_GET['page'] ?? 1)
        );

        $perPage = 10;

        $total = $this->repository->count($search);

        $offset = ($page - 1) * $perPage;

        $mahasiswa = $this->repository->all(
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

        require __DIR__ .
            '/../Views/layouts/main.php';
    }

    public function create(): void
    {
        $prodi = $this->repository->prodiAll();

        $title = 'Tambah Mahasiswa | SI Akademik';

        $content = __DIR__ .
            '/../Views/mahasiswa/create.php';

        require __DIR__ .
            '/../Views/layouts/main.php';
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

        $content = __DIR__ .
            '/../Views/mahasiswa/edit.php';

        require __DIR__ .
            '/../Views/layouts/main.php';
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

        $content = __DIR__ .
            '/../Views/mahasiswa/show.php';

        require __DIR__ .
            '/../Views/layouts/main.php';
    }

    public function store(): void
    {
        $data = $this->validatedData();

        if ($data === null) {
            http_response_code(422);

            $error =
                'NIM, nama, email, program studi, dan angkatan harus diisi dengan benar.';

            $formData = $_POST;

            $prodi = $this->repository->prodiAll();

            $title = 'Tambah Mahasiswa | SI Akademik';

            $content = __DIR__ .
                '/../Views/mahasiswa/create.php';

            require __DIR__ .
                '/../Views/layouts/main.php';

            return;
        }

        $data['status'] = 'aktif';

        $created = $this->repository->create($data);

        if (!$created) {
            http_response_code(422);

            $error =
                'NIM tersebut sudah terdaftar. Gunakan NIM lain.';

            $formData = $data;

            $prodi = $this->repository->prodiAll();

            $title = 'Tambah Mahasiswa | SI Akademik';

            $content = __DIR__ .
                '/../Views/mahasiswa/create.php';

            require __DIR__ .
                '/../Views/layouts/main.php';

            return;
        }

        $_SESSION['flash'] =
            'Data mahasiswa berhasil ditambahkan.';

        header(
            'Location: ' .
            BASE_URL .
            '/mahasiswa'
        );

        exit;
    }

    public function update(int $id): void
    {
        $item = $this->repository->find($id);

        if ($item === null) {
            http_response_code(404);
            echo '404 - Data mahasiswa tidak ditemukan';
            return;
        }

        $data = $this->validatedData();

        if ($data === null) {
            http_response_code(422);

            $error =
                'NIM, nama, email, program studi, dan angkatan harus diisi dengan benar.';

            $item = array_merge(
                $item,
                $_POST
            );

            $prodi = $this->repository->prodiAll();

            $title = 'Edit Mahasiswa | SI Akademik';

            $content = __DIR__ .
                '/../Views/mahasiswa/edit.php';

            require __DIR__ .
                '/../Views/layouts/main.php';

            return;
        }

        $status = $_POST['status'] ?? $item['status'];

        $data['status'] = $status;

        $updated = $this->repository->update(
            $id,
            $data
        );

        if (!$updated) {
            http_response_code(422);

            $error =
                'NIM tersebut sudah digunakan oleh mahasiswa lain.';

            $item = array_merge(
                $item,
                $data
            );

            $prodi = $this->repository->prodiAll();

            $title = 'Edit Mahasiswa | SI Akademik';

            $content = __DIR__ .
                '/../Views/mahasiswa/edit.php';

            require __DIR__ .
                '/../Views/layouts/main.php';

            return;
        }

        $_SESSION['flash'] =
            'Data mahasiswa berhasil diperbarui.';

        header(
            'Location: ' .
            BASE_URL .
            '/mahasiswa'
        );

        exit;
    }

    public function destroy(int $id): void
    {
        $this->repository->delete($id);

        $_SESSION['flash'] =
            'Data mahasiswa berhasil dihapus.';

        header(
            'Location: ' .
            BASE_URL .
            '/mahasiswa'
        );

        exit;
    }

    private function validatedData(): ?array
    {
        $data = [
            'nim' => trim(
                $_POST['nim'] ?? ''
            ),

            'nama' => trim(
                $_POST['nama'] ?? ''
            ),

            'email' => trim(
                $_POST['email'] ?? ''
            ),

            'prodi_id' => (int) (
                $_POST['prodi_id'] ?? 0
            ),

            'angkatan' => (int) (
                $_POST['angkatan'] ?? 0
            ),
        ];

        if (
            $data['nim'] === '' ||
            $data['nama'] === '' ||
            !filter_var(
                $data['email'],
                FILTER_VALIDATE_EMAIL
            ) ||
            $data['prodi_id'] <= 0 ||
            $data['angkatan'] < 2000
        ) {
            return null;
        }

        return $data;
    }
}