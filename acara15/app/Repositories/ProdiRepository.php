<?php

namespace App\Repositories;

use App\Core\BaseModel;
use App\Core\Database;
use PDO;

class ProdiRepository extends BaseModel
{
    public function __construct(Database $database)
    {
        parent::__construct($database->getConnection());
    }

    public function all(): array
    {
        $statement = $this->db->query(
            'SELECT id, kode, nama
             FROM prodi
             ORDER BY nama ASC'
        );

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}