<?php

namespace App\Services;

use App\Repositories\MahasiswaRepository;
use App\Repositories\ProdiRepository;
use PDOException;

class MahasiswaService
{
    public function __construct(
        private MahasiswaRepository $mahasiswaRepository,
        private ProdiRepository $prodiRepository
    ) {
    }

    public function all(
        string $search = '',
        int $limit = 10,
        int $offset = 0
    ): array {
        return $this->mahasiswaRepository->all($search, $limit, $offset);
    }

    public function count(string $search = ''): int
    {
        return $this->mahasiswaRepository->count($search);
    }

    public function prodiAll(): array
    {
        return $this->prodiRepository->all();
    }

    public function find(int $id): ?array
    {
        return $this->mahasiswaRepository->find($id);
    }

    public function create(array $input): bool
    {
        $data = $this->validatedData($input);

        if ($data === null) {
            $this->setFlash(
                'Data mahasiswa gagal disimpan. Periksa kembali data yang dimasukkan.',
                'danger'
            );

            return false;
        }

        if ($this->mahasiswaRepository->nimExists($data['nim'])) {
            $this->setFlash('NIM sudah terdaftar.', 'danger');

            return false;
        }

        $data['status'] = 'aktif';

        try {
            $created = $this->mahasiswaRepository->create($data);
        } catch (PDOException) {
            $this->setFlash('Data mahasiswa gagal disimpan.', 'danger');

            return false;
        }

        if (!$created) {
            $this->setFlash('NIM sudah terdaftar.', 'danger');

            return false;
        }

        $this->setFlash('Data mahasiswa berhasil ditambahkan.');

        return true;
    }

    public function update(int $id, array $input): bool
    {
        $current = $this->mahasiswaRepository->find($id);
        $data = $this->validatedData($input);

        if ($current === null || $data === null) {
            $this->setFlash(
                'Data mahasiswa gagal disimpan. Periksa kembali data yang dimasukkan.',
                'danger'
            );

            return false;
        }

        if ($this->mahasiswaRepository->nimExists($data['nim'], $id)) {
            $this->setFlash('NIM sudah terdaftar.', 'danger');

            return false;
        }

        $data['status'] = $input['status'] ?? $current['status'];

        try {
            $updated = $this->mahasiswaRepository->update($id, $data);
        } catch (PDOException) {
            $this->setFlash('Data mahasiswa gagal disimpan.', 'danger');

            return false;
        }

        if (!$updated) {
            $this->setFlash('NIM sudah terdaftar.', 'danger');

            return false;
        }

        $this->setFlash('Data mahasiswa berhasil diubah.');

        return true;
    }

    public function delete(int $id): void
    {
        $this->mahasiswaRepository->delete($id);
    }

    private function validatedData(array $input): ?array
    {
        $data = [
            'nim' => trim($input['nim'] ?? ''),
            'nama' => trim($input['nama'] ?? ''),
            'email' => trim($input['email'] ?? ''),
            'prodi_id' => (int) ($input['prodi_id'] ?? 0),
            'angkatan' => (int) ($input['angkatan'] ?? 0),
        ];

        if (
            $data['nim'] === '' ||
            $data['nama'] === '' ||
            !filter_var($data['email'], FILTER_VALIDATE_EMAIL) ||
            $data['prodi_id'] <= 0 ||
            $data['angkatan'] < 2000
        ) {
            return null;
        }

        return $data;
    }

    private function setFlash(string $message, string $type = 'success'): void
    {
        $_SESSION['flash'] = $message;
        $_SESSION['flash_type'] = $type;
    }
}