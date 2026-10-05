<?php

namespace App\Models;

use App\Core\BaseModel;

class Prodi extends BaseModel
{
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

    public function create(array $data): void
    {
        if ($this->nameExists($data['nama'])) {
            throw new \InvalidArgumentException('Nama program studi tersebut sudah terdaftar.');
        }

        $statement = $this->db->prepare('INSERT INTO prodi (kode, nama) VALUES (:kode, :nama)');
        $statement->execute($data);
    }

    public function update(int $id, array $data): void
    {
        if ($this->nameExists($data['nama'], $id)) {
            throw new \InvalidArgumentException('Nama program studi tersebut sudah digunakan program studi lain.');
        }

        $data['id'] = $id;
        $statement = $this->db->prepare('UPDATE prodi SET kode = :kode, nama = :nama WHERE id = :id');
        $statement->execute($data);
    }

    private function nameExists(string $name, ?int $excludeId = null): bool
    {
        $sql = 'SELECT 1 FROM prodi WHERE nama = :nama';
        $parameters = ['nama' => $name];
        if ($excludeId !== null) {
            $sql .= ' AND id <> :exclude_id';
            $parameters['exclude_id'] = $excludeId;
        }

        $statement = $this->db->prepare($sql);
        $statement->execute($parameters);

        return $statement->fetchColumn() !== false;
    }

    public function delete(int $id): void
    {
        $statement = $this->db->prepare('DELETE FROM prodi WHERE id = :id');
        $statement->execute(['id' => $id]);
    }
}