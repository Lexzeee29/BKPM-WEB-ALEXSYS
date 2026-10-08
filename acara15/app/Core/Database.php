<?php

namespace App\Core;

use PDO;
use PDOException;
use RuntimeException;

class Database
{
    private static ?Database $instance = null;

    private PDO $connection;

    private function __construct()
    {
        $config = require dirname(__DIR__, 2) . '/config/database.php';

        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            $config['host'],
            $config['dbname'],
            $config['charset']
        );

        try {
            $this->connection = new PDO(
                $dsn,
                $config['username'],
                $config['password'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $e) {
            Logger::error($e);
            throw new RuntimeException('Koneksi database gagal.');
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