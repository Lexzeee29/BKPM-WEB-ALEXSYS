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

    public function namaExists(string $nama, ?int $exceptId = null): bool
    {
        $sql = 'SELECT 1 FROM matakuliah WHERE nama = :nama';
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

        $statement = $this->db->prepare('INSERT INTO matakuliah (kode, nama, sks, prodi_id) VALUES (:kode, :nama, :sks, :prodi_id)');
        $statement->execute($data);
        return true;
    }

    public function update(int $id, array $data): bool
    {
        if ($this->namaExists($data['nama'], $id)) {
            return false;
        }

        $data['id'] = $id;
        $statement = $this->db->prepare('UPDATE matakuliah SET kode = :kode, nama = :nama, sks = :sks, prodi_id = :prodi_id WHERE id = :id');
        $statement->execute($data);
        return true;
    }

    public function delete(int $id): void
    {
        $statement = $this->db->prepare('DELETE FROM matakuliah WHERE id = :id');
        $statement->execute(['id' => $id]);
    }
}