<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?Database $instance = null;

    private PDO $connection;

    private function __construct()
    {
        $host = '127.0.0.1';
        $dbname = 'si_akademik';
        $username = 'root';
        $password = '';

        $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";

        try {
            $this->connection = new PDO(
                $dsn,
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $e) {
            die(
                'Koneksi database gagal: ' .
                $e->getMessage()
            );
        }
    }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }

        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }

    /*
     * Digunakan oleh Model seperti Prodi dan Matakuliah.
     */
    public static function connection(): PDO
    {
        return self::getInstance()->getConnection();
    }
}