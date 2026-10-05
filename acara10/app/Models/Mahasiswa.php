<?php

namespace App\Models;

use InvalidArgumentException;

class Mahasiswa
{
    private ?int $id = null;
    private string $nim;
    private string $nama;
    private string $email;
    private int $prodiId;
    private int $angkatan;
    private string $status = 'aktif';

    public function __construct(array $data)
    {
        if (isset($data['id'])) {
            $this->setId((int) $data['id']);
        }

        $this->setNim((string) ($data['nim'] ?? ''));
        $this->setNama((string) ($data['nama'] ?? ''));
        $this->setEmail((string) ($data['email'] ?? ''));
        $this->setProdiId((int) ($data['prodi_id'] ?? 0));
        $this->setAngkatan((int) ($data['angkatan'] ?? 0));
        $this->setStatus((string) ($data['status'] ?? 'aktif'));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        if ($id < 1) {
            throw new InvalidArgumentException('ID mahasiswa harus lebih besar dari 0.');
        }
        $this->id = $id;
    }

    public function getNim(): string
    {
        return $this->nim;
    }

    public function setNim(string $nim): void
    {
        if ($nim === '' || !ctype_digit($nim)) {
            throw new InvalidArgumentException('NIM harus diisi dengan angka.');
        }
        $this->nim = $nim;
    }

    public function getNama(): string
    {
        return $this->nama;
    }

    public function setNama(string $nama): void
    {
        $nama = trim($nama);
        if ($nama === '') {
            throw new InvalidArgumentException('Nama mahasiswa tidak boleh kosong.');
        }
        $this->nama = $nama;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Format email tidak valid.');
        }
        $this->email = $email;
    }

    public function getProdiId(): int
    {
        return $this->prodiId;
    }

    public function setProdiId(int $prodiId): void
    {
        if ($prodiId < 1) {
            throw new InvalidArgumentException('Program studi tidak valid.');
        }
        $this->prodiId = $prodiId;
    }

    public function getAngkatan(): int
    {
        return $this->angkatan;
    }

    public function setAngkatan(int $angkatan): void
    {
        if ($angkatan < 2000 || $angkatan > 2100) {
            throw new InvalidArgumentException('Angkatan harus antara 2000 dan 2100.');
        }
        $this->angkatan = $angkatan;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        if (!in_array($status, ['aktif', 'cuti', 'lulus'], true)) {
            throw new InvalidArgumentException('Status mahasiswa tidak valid.');
        }
        $this->status = $status;
    }
}