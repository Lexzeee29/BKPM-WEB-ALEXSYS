<?php

namespace App\Repositories;

use App\Core\BaseModel;
use App\Core\Database;
use PDO;

class MahasiswaRepository extends BaseModel
{
    public function __construct(Database $database)
    {
        parent::__construct($database->getConnection());
    }

    public function all(
        string $search = '',
        int $limit = 10,
        int $offset = 0
    ): array {
        $statement = $this->db->prepare(
            'SELECT
                mahasiswa.id,
                mahasiswa.nim,
                mahasiswa.nama,
                mahasiswa.email,
                mahasiswa.prodi_id,
                mahasiswa.angkatan,
                mahasiswa.status,
                prodi.kode AS prodi_kode,
                prodi.nama AS prodi_nama
             FROM mahasiswa
             INNER JOIN prodi ON prodi.id = mahasiswa.prodi_id
             WHERE mahasiswa.nim LIKE :nim_search
                OR mahasiswa.nama LIKE :nama_search
             ORDER BY mahasiswa.id ASC
             LIMIT :limit OFFSET :offset'
        );

        $searchValue = '%' . $search . '%';

        $statement->bindValue(
            ':nim_search',
            $searchValue,
            PDO::PARAM_STR
        );

        $statement->bindValue(
            ':nama_search',
            $searchValue,
            PDO::PARAM_STR
        );

        $statement->bindValue(
            ':limit',
            $limit,
            PDO::PARAM_INT
        );

        $statement->bindValue(
            ':offset',
            $offset,
            PDO::PARAM_INT
        );

        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function count(string $search = ''): int
    {
        $statement = $this->db->prepare(
            'SELECT COUNT(*)
             FROM mahasiswa
             WHERE nim LIKE :nim_search
                OR nama LIKE :nama_search'
        );

        $searchValue = '%' . $search . '%';

        $statement->execute([
            'nim_search' => $searchValue,
            'nama_search' => $searchValue,
        ]);

        return (int) $statement->fetchColumn();
    }

    public function find(int $id): ?array
    {
        $statement = $this->db->prepare(
            'SELECT
                mahasiswa.id,
                mahasiswa.nim,
                mahasiswa.nama,
                mahasiswa.email,
                mahasiswa.prodi_id,
                mahasiswa.angkatan,
                mahasiswa.status,
                prodi.kode AS prodi_kode,
                prodi.nama AS prodi_nama
             FROM mahasiswa
             INNER JOIN prodi ON prodi.id = mahasiswa.prodi_id
             WHERE mahasiswa.id = :id'
        );

        $statement->execute([
            'id' => $id
        ]);

        $mahasiswa = $statement->fetch(PDO::FETCH_ASSOC);

        return $mahasiswa ?: null;
    }

    public function nimExists(
        string $nim,
        ?int $exceptId = null
    ): bool {
        $sql = 'SELECT 1
                FROM mahasiswa
                WHERE nim = :nim';

        $params = [
            'nim' => $nim
        ];

        if ($exceptId !== null) {
            $sql .= ' AND id != :id';

            $params['id'] = $exceptId;
        }

        $sql .= ' LIMIT 1';

        $statement = $this->db->prepare($sql);

        $statement->execute($params);

        return $statement->fetchColumn() !== false;
    }

    public function create(array $data): bool
    {
        if ($this->nimExists($data['nim'])) {
            return false;
        }

        $statement = $this->db->prepare(
            'INSERT INTO mahasiswa
                (nim, nama, email, prodi_id, angkatan, status)
             VALUES
                (:nim, :nama, :email, :prodi_id, :angkatan, :status)'
        );

        $statement->execute([
            'nim' => $data['nim'],
            'nama' => $data['nama'],
            'email' => $data['email'],
            'prodi_id' => $data['prodi_id'],
            'angkatan' => $data['angkatan'],
            'status' => $data['status'],
        ]);

        return true;
    }

    public function update(
        int $id,
        array $data
    ): bool {
        if ($this->nimExists($data['nim'], $id)) {
            return false;
        }

        $statement = $this->db->prepare(
            'UPDATE mahasiswa
             SET
                nim = :nim,
                nama = :nama,
                email = :email,
                prodi_id = :prodi_id,
                angkatan = :angkatan,
                status = :status
             WHERE id = :id'
        );

        $statement->execute([
            'nim' => $data['nim'],
            'nama' => $data['nama'],
            'email' => $data['email'],
            'prodi_id' => $data['prodi_id'],
            'angkatan' => $data['angkatan'],
            'status' => $data['status'],
            'id' => $id,
        ]);

        return true;
    }

    public function delete(int $id): void
    {
        $statement = $this->db->prepare(
            'DELETE FROM mahasiswa
             WHERE id = :id'
        );

        $statement->execute([
            'id' => $id
        ]);
    }
}