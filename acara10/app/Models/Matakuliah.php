<?php

namespace App\Models;

use App\Core\BaseModel;

class Matakuliah extends BaseModel
{
    public function all(): array
    {
        return $this->db->query(
            'SELECT matakuliah.*, prodi.kode AS prodi_kode, prodi.nama AS prodi_nama
             FROM matakuliah INNER JOIN prodi ON prodi.id = matakuliah.prodi_id
             ORDER BY matakuliah.kode ASC'
        )->fetchAll();
    }

    public function find(int $id): ?array
    {
        $statement = $this->db->prepare('SELECT * FROM matakuliah WHERE id = :id');
        $statement->execute(['id' => $id]);
        return ($item = $statement->fetch()) ?: null;
    }

    public function prodiAll(): array
    {
        return $this->db->query('SELECT id, kode, nama FROM prodi ORDER BY nama ASC')->fetchAll();
    }

    public function create(array $data): void
    {
        if ($this->nameExists($data['nama'])) {
            throw new \InvalidArgumentException('Nama mata kuliah tersebut sudah terdaftar.');
        }

        $statement = $this->db->prepare('INSERT INTO matakuliah (kode, nama, sks, prodi_id) VALUES (:kode, :nama, :sks, :prodi_id)');
        $statement->execute($data);
    }

    public function update(int $id, array $data): void
    {
        if ($this->nameExists($data['nama'], $id)) {
            throw new \InvalidArgumentException('Nama mata kuliah tersebut sudah digunakan mata kuliah lain.');
        }

        $data['id'] = $id;
        $statement = $this->db->prepare('UPDATE matakuliah SET kode = :kode, nama = :nama, sks = :sks, prodi_id = :prodi_id WHERE id = :id');
        $statement->execute($data);
    }

    private function nameExists(string $name, ?int $excludeId = null): bool
    {
        $sql = 'SELECT 1 FROM matakuliah WHERE nama = :nama';
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
        $statement = $this->db->prepare('DELETE FROM matakuliah WHERE id = :id');
        $statement->execute(['id' => $id]);
    }
}