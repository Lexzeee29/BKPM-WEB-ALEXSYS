<?php

namespace App\Core;

use PDO;

class BaseModel
{
    protected PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::connection();
    }
}