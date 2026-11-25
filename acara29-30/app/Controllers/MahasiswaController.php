<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Core\Auth;
use App\Core\Response;
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

        $content = \VIEW_PATH . '/mahasiswa/index.php';

        $this->view($content, get_defined_vars());
    }

    public function create(): void
    {
        $formData = $_SESSION['old_input'] ?? [];
        unset($_SESSION['old_input']);

        $prodi = $this->service->prodiAll();

        $title = 'Tambah Mahasiswa | SI Akademik';

        $content = \VIEW_PATH . '/mahasiswa/create.php';

        $this->view($content, get_defined_vars());
    }

    public function edit(int $id): void
    {
        $item = $this->service->find($id);

        if ($item === null) {
            Response::json(404, false, 'Data mahasiswa tidak ditemukan');
        }

        $oldInput = $_SESSION['old_input'] ?? [];
        unset($_SESSION['old_input']);
        $item = array_merge($item, $oldInput);

        $prodi = $this->service->prodiAll();

        $title = 'Edit Mahasiswa | SI Akademik';

        $content = \VIEW_PATH . '/mahasiswa/edit.php';

        $this->view($content, get_defined_vars());
    }

    public function show(int $id): void
    {
        $item = $this->service->find($id);

        if ($item === null) {
            Response::json(404, false, 'Data mahasiswa tidak ditemukan');
        }

        $title = 'Detail Mahasiswa | SI Akademik';

        $content = \VIEW_PATH . '/mahasiswa/show.php';

        $this->view($content, get_defined_vars());
    }

    public function store(): void
    {
        Auth::requireAdmin();

        $data = $_POST;
        $data['foto'] = $this->storePhoto($_FILES['foto'] ?? null);

        if (!$this->service->create($data)) {
            $this->removePhoto($data['foto']);
            $_SESSION['old_input'] = $_POST;
            $this->redirect('/mahasiswa/create');

            return;
        }

        $this->redirect('/mahasiswa');
    }

    public function update(int $id): void
    {
        Auth::requireAdmin();

        $item = $this->service->find($id);

        if ($item === null) {
            Response::json(404, false, 'Data mahasiswa tidak ditemukan');
        }

        $data = $_POST;
        $data['foto'] = $this->storePhoto($_FILES['foto'] ?? null, $item['foto'] ?? null);

        if (!$this->service->update($id, $data)) {
            if ($data['foto'] !== ($item['foto'] ?? null)) {
                $this->removePhoto($data['foto']);
            }
            $_SESSION['old_input'] = $_POST;
            $this->redirect('/mahasiswa/' . $id . '/edit');

            return;
        }

        if ($data['foto'] !== ($item['foto'] ?? null)) {
            $this->removePhoto($item['foto'] ?? null);
        }

        $this->redirect('/mahasiswa');
    }
    public function destroy(int $id): void
    {
        Auth::requireAdmin();

        $this->service->delete($id);
        $this->redirect('/mahasiswa');
    }

    private function storePhoto(?array $file, ?string $currentPhoto = null): ?string
    {
        if ($file === null || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return $currentPhoto;
        }

        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            Response::json(422, false, 'Berkas foto gagal diunggah');
        }

        if (($file['size'] ?? 0) > 2 * 1024 * 1024) {
            Response::json(422, false, 'Ukuran berkas maksimal 2MB');
        }

        $extension = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));
        $image = getimagesize($file['tmp_name'] ?? '');
        $validImages = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png'];

        if (
            !isset($validImages[$extension]) ||
            $image === false ||
            ($image['mime'] ?? '') !== $validImages[$extension]
        ) {
            Response::json(422, false, 'Berkas harus berupa gambar JPG/PNG yang valid');
        }

        $uploadDirectory = __DIR__ . '/../../public/uploads';
        if (!is_dir($uploadDirectory) && !mkdir($uploadDirectory, 0755, true) && !is_dir($uploadDirectory)) {
            Response::json(500, false, 'Folder penyimpanan foto tidak dapat dibuat');
        }

        $newName = bin2hex(random_bytes(16)) . '.' . $extension;
        if (!move_uploaded_file($file['tmp_name'], $uploadDirectory . '/' . $newName)) {
            Response::json(500, false, 'Foto gagal disimpan');
        }

        return $newName;
    }

    private function removePhoto(?string $filename): void
    {
        if ($filename === null || basename($filename) !== $filename) {
            return;
        }

        $path = __DIR__ . '/../../public/uploads/' . $filename;
        if (is_file($path)) {
            unlink($path);
        }
    }
}