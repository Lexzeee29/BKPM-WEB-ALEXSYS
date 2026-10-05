<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Mahasiswa
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::connection();
    }

    public function all(): array
    {
        $statement = $this->db->query(
            'SELECT mahasiswa.id, mahasiswa.nim, mahasiswa.nama, mahasiswa.email,
                    mahasiswa.prodi_id, mahasiswa.angkatan, mahasiswa.status,
                    prodi.kode AS prodi_kode, prodi.nama AS prodi_nama
             FROM mahasiswa
             INNER JOIN prodi ON prodi.id = mahasiswa.prodi_id
             ORDER BY mahasiswa.id ASC'
        );

        return $statement->fetchAll();
    }

    public function prodiAll(): array
    {
        return $this->db->query('SELECT id, kode, nama FROM prodi ORDER BY nama ASC')->fetchAll();
    }

    public function find(int $id): ?array
    {
        $statement = $this->db->prepare(
            'SELECT mahasiswa.id, mahasiswa.nim, mahasiswa.nama, mahasiswa.email,
                    mahasiswa.prodi_id, mahasiswa.angkatan, mahasiswa.status,
                    prodi.kode AS prodi_kode, prodi.nama AS prodi_nama
             FROM mahasiswa
             INNER JOIN prodi ON prodi.id = mahasiswa.prodi_id
             WHERE mahasiswa.id = :id'
        );
        $statement->execute(['id' => $id]);
        $mahasiswa = $statement->fetch();

        return $mahasiswa ?: null;
    }

    public function create(array $data): void
    {
        $statement = $this->db->prepare(
            'INSERT INTO mahasiswa (nim, nama, email, prodi_id, angkatan, status)
             VALUES (:nim, :nama, :email, :prodi_id, :angkatan, :status)'
        );
        $statement->execute($data);
    }
}