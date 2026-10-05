<?php

namespace App\Services;

use App\Models\Mahasiswa;
use App\Repositories\MahasiswaRepository;
use App\Repositories\ProdiRepository;
use InvalidArgumentException;

class MahasiswaService
{
    public function __construct(
        private MahasiswaRepository $mahasiswaRepository,
        private ProdiRepository $prodiRepository
    ) {
    }

    public function create(array $data): void
    {
        try {
            $mahasiswa = $this->validatedMahasiswa($data);
            $this->mahasiswaRepository->create($mahasiswa);
        } catch (InvalidArgumentException $exception) {
            throw new InvalidArgumentException($this->failureMessage($exception));
        }
    }

    public function update(int $id, array $data): void
    {
        $item = $this->mahasiswaRepository->find($id);
        if ($item === null) {
            throw new InvalidArgumentException('Data mahasiswa tidak ditemukan.');
        }

        try {
            $data['status'] = $data['status'] ?? $item['status'];
            $mahasiswa = $this->validatedMahasiswa($data);
            $this->mahasiswaRepository->update($id, $mahasiswa);
        } catch (InvalidArgumentException $exception) {
            throw new InvalidArgumentException($this->failureMessage($exception));
        }
    }

    private function validatedMahasiswa(array $data): Mahasiswa
    {
        $mahasiswa = new Mahasiswa([
            'nim' => trim((string) ($data['nim'] ?? '')),
            'nama' => trim((string) ($data['nama'] ?? '')),
            'email' => trim((string) ($data['email'] ?? '')),
            'prodi_id' => (int) ($data['prodi_id'] ?? 0),
            'angkatan' => (int) ($data['angkatan'] ?? 0),
            'status' => (string) ($data['status'] ?? 'aktif'),
        ]);

        if ($this->prodiRepository->find($mahasiswa->getProdiId()) === null) {
            throw new InvalidArgumentException('Program studi tidak ditemukan.');
        }

        return $mahasiswa;
    }

    private function failureMessage(InvalidArgumentException $exception): string
    {
        if (str_contains($exception->getMessage(), 'NIM tersebut sudah')) {
            return 'NIM sudah terdaftar. Data gagal disimpan.';
        }

        return 'Data gagal disimpan: ' . $exception->getMessage();
    }
}