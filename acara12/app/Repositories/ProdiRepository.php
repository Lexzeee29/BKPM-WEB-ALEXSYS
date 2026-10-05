<?php

namespace App\Repositories;

use App\Core\BaseModel;

class ProdiRepository extends BaseModel
{
    public function all(): array
    {
        return $this->db->query('SELECT id, kode, nama FROM prodi ORDER BY nama ASC')->fetchAll();
    }

    public function find(int $id): ?array
    {
        $statement = $this->db->prepare('SELECT id, kode, nama FROM prodi WHERE id = :id');
        $statement->execute(['id' => $id]);
        $prodi = $statement->fetch();

        return $prodi ?: null;
    }
}