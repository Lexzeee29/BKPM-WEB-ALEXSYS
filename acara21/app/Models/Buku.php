<?php

namespace App\Models;

use App\Core\BaseModel;

class Buku extends BaseModel
{
    public function all(): array
    {
        return $this->db->query(
            'SELECT id, judul, penulis, penerbit, tahun_terbit, created_at
             FROM buku
             ORDER BY id DESC'
        )->fetchAll();
    }

    public function find(int $id): ?array
    {
        $statement = $this->db->prepare(
            'SELECT id, judul, penulis, penerbit, tahun_terbit, created_at
             FROM buku
             WHERE id = :id'
        );
        $statement->execute(['id' => $id]);
        $book = $statement->fetch();

        return $book ?: null;
    }

    public function create(array $data): ?array
    {
        $statement = $this->db->prepare(
            'INSERT INTO buku (judul, penulis, penerbit, tahun_terbit)
             VALUES (:judul, :penulis, :penerbit, :tahun_terbit)'
        );
        $statement->execute($data);

        return $this->find((int) $this->db->lastInsertId());
    }

    public function update(int $id, array $data): ?array
    {
        $statement = $this->db->prepare(
            'UPDATE buku
             SET judul = :judul, penulis = :penulis,
                 penerbit = :penerbit, tahun_terbit = :tahun_terbit
             WHERE id = :id'
        );
        $statement->execute([
            'judul' => $data['judul'],
            'penulis' => $data['penulis'],
            'penerbit' => $data['penerbit'],
            'tahun_terbit' => $data['tahun_terbit'],
            'id' => $id,
        ]);

        return $this->find($id);
    }

    public function delete(int $id): void
    {
        $statement = $this->db->prepare('DELETE FROM buku WHERE id = :id');
        $statement->execute(['id' => $id]);
    }
}