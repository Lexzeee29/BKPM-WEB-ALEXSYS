<?php

namespace App\Services;

use App\Core\Logger;
use App\Repositories\MahasiswaRepository;
use App\Repositories\ProdiRepository;
use Throwable;

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

        $data['status'] = 'aktif';

        try {
            if ($this->mahasiswaRepository->existsByNim($data['nim'])) {
                $this->setFlash('NIM sudah terdaftar.', 'danger');

                return false;
            }

            $created = $this->mahasiswaRepository->create($data);
        } catch (Throwable $exception) {
            Logger::error($exception);
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

        $data['status'] = $input['status'] ?? $current['status'];

        try {
            if ($this->mahasiswaRepository->existsByNim($data['nim'], $id)) {
                $this->setFlash('NIM sudah terdaftar.', 'danger');

                return false;
            }

            $updated = $this->mahasiswaRepository->update($id, $data);
        } catch (Throwable $exception) {
            Logger::error($exception);
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

    public function delete(int $id): bool
    {
        try {
            $this->mahasiswaRepository->delete($id);
            $this->setFlash('Data mahasiswa berhasil dihapus.');

            return true;
        } catch (Throwable $exception) {
            Logger::error($exception);
            $this->setFlash('Data gagal dihapus.', 'danger');

            return false;
        }
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
            !preg_match('/^[0-9]{1,20}$/', $data['nim']) ||
            $data['nama'] === '' ||
            strlen($data['nama']) > 100 ||
            strlen($data['email']) > 100 ||
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