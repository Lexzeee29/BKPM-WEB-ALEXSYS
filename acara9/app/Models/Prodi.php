<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Prodi
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::connection();
    }

    public function all(): array
    {
        return $this->db->query('SELECT id, kode, nama FROM prodi ORDER BY nama ASC')->fetchAll();
    }

    public function find(int $id): ?array
    {
        $statement = $this->db->prepare('SELECT id, kode, nama FROM prodi WHERE id = :id');
        $statement->execute(['id' => $id]);
        return ($item = $statement->fetch()) ?: null;
    }

    public function namaExists(string $nama, ?int $exceptId = null): bool
    {
        $sql = 'SELECT 1 FROM prodi WHERE nama = :nama';
        $params = ['nama' => $nama];

        if ($exceptId !== null) {
            $sql .= ' AND id != :id';
            $params['id'] = $exceptId;
        }

        $statement = $this->db->prepare($sql . ' LIMIT 1');
        $statement->execute($params);
        return $statement->fetchColumn() !== false;
    }

    public function create(array $data): bool
    {
        if ($this->namaExists($data['nama'])) {
            return false;
        }

        $statement = $this->db->prepare('INSERT INTO prodi (kode, nama) VALUES (:kode, :nama)');
        $statement->execute($data);
        return true;
    }

    public function update(int $id, array $data): bool
    {
        if ($this->namaExists($data['nama'], $id)) {
            return false;
        }

        $data['id'] = $id;
        $statement = $this->db->prepare('UPDATE prodi SET kode = :kode, nama = :nama WHERE id = :id');
        $statement->execute($data);
        return true;
    }

    public function delete(int $id): void
    {
        $statement = $this->db->prepare('DELETE FROM prodi WHERE id = :id');
        $statement->execute(['id' => $id]);
    }
}