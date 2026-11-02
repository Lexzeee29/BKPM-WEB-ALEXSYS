<?php

require_once __DIR__ . '/../app/Core/autoload.php';

use App\Config\Database;
use App\Core\Auth;
use App\Core\Logger;
use App\Core\Response;

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

$respond = static function (int $status, array $body): void {
    http_response_code($status);
    echo json_encode(
        $body,
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );
    exit;
};

$requestPath = $_SERVER['API_REQUEST_PATH'] ?? parse_url(
    $_SERVER['REQUEST_URI'] ?? '',
    PHP_URL_PATH
);

if (isset($_SERVER['API_REQUEST_PATH'])) {
    $routePattern = '#^/api/mahasiswa(?:/(.*))?$#';
} else {
    $routePattern = '#(?:^|/)api/mahasiswa(?:\.php)?(?:/(.*))?/?$#';
}

if (!is_string($requestPath) || !preg_match($routePattern, $requestPath, $matches)) {
    $respond(404, [
        'success' => false,
        'message' => 'Endpoint tidak ditemukan',
        'data' => null,
    ]);
}

$idSegment = $matches[1] ?? '';
$id = null;

if ($idSegment !== '') {
    if (!ctype_digit($idSegment) || (int) $idSegment < 1) {
        $respond(404, [
            'success' => false,
            'message' => 'Endpoint tidak ditemukan',
            'data' => null,
        ]);
    }

    $id = (int) $idSegment;
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$allowedMethods = ['GET', 'POST', 'PUT', 'DELETE'];

if ($method === 'POST' && $id !== null) {
    $method = 'PUT';
}

if (!in_array($method, $allowedMethods, true)) {
    header('Allow: GET, POST, PUT, DELETE');
    $respond(405, [
        'success' => false,
        'message' => 'HTTP method tidak diizinkan',
        'data' => null,
    ]);
}

if (
    ($id === null && !in_array($method, ['GET', 'POST'], true)) ||
    ($id !== null && !in_array($method, ['GET', 'PUT', 'DELETE'], true))
) {
    header('Allow: ' . ($id === null ? 'GET, POST' : 'GET, PUT, DELETE'));
    $respond(405, [
        'success' => false,
        'message' => 'HTTP method tidak diizinkan untuk endpoint ini',
        'data' => null,
    ]);
}

if (in_array($method, ['POST', 'PUT', 'DELETE'], true)) {
    Auth::requireAdmin();
}

$input = [];
$nim = '';
$nama = '';
$email = '';

if (in_array($method, ['POST', 'PUT'], true)) {
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

    if (preg_match('#^multipart/form-data(?:\s*;|$)#i', $contentType)) {
        $input = $_POST;
    } elseif (preg_match('#^application/json(?:\s*;|$)#i', $contentType)) {
        $rawBody = file_get_contents('php://input');

        if (!is_string($rawBody)) {
            $respond(400, [
                'success' => false,
                'message' => 'Body request tidak dapat dibaca',
                'data' => null,
            ]);
        }

        try {
            $decodedBody = json_decode(
                $rawBody,
                false,
                512,
                JSON_THROW_ON_ERROR
            );
        } catch (\JsonException $exception) {
            $respond(400, [
                'success' => false,
                'message' => 'Format JSON tidak valid',
                'data' => null,
            ]);
        }

        if (!$decodedBody instanceof \stdClass) {
            $respond(400, [
                'success' => false,
                'message' => 'Body request harus berupa object JSON',
                'data' => null,
            ]);
        }

        $input = get_object_vars($decodedBody);
    } else {
        $respond(415, [
            'success' => false,
            'message' => 'Content-Type harus application/json atau multipart/form-data',
            'data' => null,
        ]);
    }
    $errors = [];
    $nim = isset($input['nim']) && is_string($input['nim'])
        ? trim($input['nim'])
        : '';
    $nama = isset($input['nama']) && is_string($input['nama'])
        ? trim($input['nama'])
        : '';
    $email = isset($input['email']) && is_string($input['email'])
        ? trim($input['email'])
        : '';

    if ($nim !== '' && (strlen($nim) !== 9 || !ctype_digit($nim))) {
        Response::json(
            422,
            false,
            'Format NIM tidak valid, harus 9 digit',
            null,
            ['nim' => 'Format NIM tidak valid, harus 9 digit']
        );
    }

    if ($nim === '') {
        $errors['nim'] = 'NIM wajib diisi.';
    }

    if ($nama === '') {
        $errors['nama'] = 'Nama wajib diisi.';
    } elseif (strlen($nama) > 100) {
        $errors['nama'] = 'Nama maksimal 100 karakter.';
    }

    if ($email === '') {
        $errors['email'] = 'Email wajib diisi.';
    } elseif (
        strlen($email) > 100 ||
        filter_var($email, FILTER_VALIDATE_EMAIL) === false
    ) {
        $errors['email'] = 'Format email tidak valid.';
    }

    if ($errors !== []) {
        Response::json(
            400,
            false,
            'Data request tidak lengkap atau tidak valid',
            null,
            $errors
        );
    }
}

try {
    $connection = Database::connection();
    $selectStudent = static function (\PDO $connection, int $studentId): ?array {
        $statement = $connection->prepare(
            'SELECT id, nim, nama, email, prodi_id, angkatan, status, foto
             FROM mahasiswa
             WHERE id = :id'
        );
        $statement->execute(['id' => $studentId]);
        $student = $statement->fetch(\PDO::FETCH_ASSOC);

        return $student ?: null;
    };

    if ($method === 'GET' && $id === null) {
        $statement = $connection->query(
            'SELECT mahasiswa.id, mahasiswa.nim, mahasiswa.nama, mahasiswa.email,
                    mahasiswa.prodi_id, prodi.nama AS prodi,
                    mahasiswa.angkatan, mahasiswa.status, mahasiswa.foto
             FROM mahasiswa
             INNER JOIN prodi ON prodi.id = mahasiswa.prodi_id
             ORDER BY mahasiswa.id ASC'
        );

        $respond(200, [
            'success' => true,
            'message' => 'Data mahasiswa berhasil diambil',
            'data' => $statement->fetchAll(\PDO::FETCH_ASSOC),
        ]);
    }

    if ($method === 'GET' && $id !== null) {
        $student = $selectStudent($connection, $id);

        if ($student === null) {
            $respond(404, [
                'success' => false,
                'message' => 'Data mahasiswa tidak ditemukan',
                'data' => null,
            ]);
        }

        $respond(200, [
            'success' => true,
            'message' => 'Data mahasiswa berhasil diambil',
            'data' => $student,
        ]);
    }

    if ($method === 'DELETE') {
        $student = $selectStudent($connection, $id);

        if ($student === null) {
            $respond(404, [
                'success' => false,
                'message' => 'Data mahasiswa tidak ditemukan',
                'data' => null,
            ]);
        }

        $statement = $connection->prepare('DELETE FROM mahasiswa WHERE id = :id');
        $statement->execute(['id' => $id]);
        if (!empty($student['foto'])) {
            $photoPath = __DIR__ . '/../public/uploads/' . basename($student['foto']);
            if (is_file($photoPath)) {
                unlink($photoPath);
            }
        }

        $respond(200, [
            'success' => true,
            'message' => 'Data mahasiswa berhasil dihapus',
            'data' => $student,
        ]);
    }

    $currentStudent = null;

    if ($method === 'PUT') {
        $currentStudent = $selectStudent($connection, $id);

        if ($currentStudent === null) {
            $respond(404, [
                'success' => false,
                'message' => 'Data mahasiswa tidak ditemukan',
                'data' => null,
            ]);
        }
    }

    if (array_key_exists('prodi_id', $input)) {
        $prodiId = filter_var($input['prodi_id'], FILTER_VALIDATE_INT);

        if ($prodiId === false || $prodiId < 1) {
            $respond(400, [
                'success' => false,
                'message' => 'prodi_id harus berupa bilangan bulat positif',
                'data' => null,
            ]);
        }
    } elseif ($currentStudent !== null) {
        $prodiId = (int) $currentStudent['prodi_id'];
    } else {
        $programStatement = $connection->query(
            'SELECT id FROM prodi ORDER BY id ASC LIMIT 1'
        );
        $prodiId = $programStatement->fetchColumn();

        if ($prodiId === false) {
            $respond(422, [
                'success' => false,
                'message' => 'Data program studi belum tersedia',
                'data' => null,
            ]);
        }

        $prodiId = (int) $prodiId;
    }

    $programStatement = $connection->prepare(
        'SELECT 1 FROM prodi WHERE id = :id'
    );
    $programStatement->execute(['id' => $prodiId]);

    if ($programStatement->fetchColumn() === false) {
        $respond(400, [
            'success' => false,
            'message' => 'Program studi tidak ditemukan',
            'data' => null,
        ]);
    }

    if (array_key_exists('angkatan', $input)) {
        $angkatan = filter_var($input['angkatan'], FILTER_VALIDATE_INT);

        if ($angkatan === false || $angkatan < 2000 || $angkatan > 2155) {
            $respond(400, [
                'success' => false,
                'message' => 'angkatan harus berupa tahun antara 2000 dan 2155',
                'data' => null,
            ]);
        }
    } elseif ($currentStudent !== null) {
        $angkatan = (int) $currentStudent['angkatan'];
    } else {
        $angkatan = (int) date('Y');
    }

    $duplicateSql = 'SELECT 1 FROM mahasiswa WHERE nim = :nim';
    $duplicateParameters = ['nim' => $nim];

    if ($id !== null) {
        $duplicateSql .= ' AND id <> :id';
        $duplicateParameters['id'] = $id;
    }

    $duplicateStatement = $connection->prepare($duplicateSql . ' LIMIT 1');
    $duplicateStatement->execute($duplicateParameters);

    if ($duplicateStatement->fetchColumn() !== false) {
        $respond(409, [
            'success' => false,
            'message' => 'NIM sudah terdaftar',
            'data' => null,
        ]);
    }

    $photo = $currentStudent['foto'] ?? null;
    $uploadedPhotoPath = null;
    $file = $_FILES['foto'] ?? null;

    if (is_array($file) && ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            $respond(422, [
                'success' => false,
                'message' => 'Berkas foto gagal diunggah',
                'data' => null,
            ]);
        }

        if (($file['size'] ?? 0) > 2 * 1024 * 1024) {
            $respond(422, [
                'success' => false,
                'message' => 'Ukuran berkas maksimal 2MB',
                'data' => null,
            ]);
        }

        $extension = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));
        $image = getimagesize($file['tmp_name'] ?? '');
        $validImages = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
        ];

        if (
            !isset($validImages[$extension]) ||
            $image === false ||
            ($image['mime'] ?? '') !== $validImages[$extension]
        ) {
            $respond(422, [
                'success' => false,
                'message' => 'Berkas harus berupa gambar JPG/PNG yang valid',
                'data' => null,
            ]);
        }

        $uploadDirectory = __DIR__ . '/../public/uploads';
        if (!is_dir($uploadDirectory) && !mkdir($uploadDirectory, 0755, true) && !is_dir($uploadDirectory)) {
            $respond(500, [
                'success' => false,
                'message' => 'Folder penyimpanan foto tidak dapat dibuat',
                'data' => null,
            ]);
        }

        $photo = bin2hex(random_bytes(16)) . '.' . $extension;
        $uploadedPhotoPath = $uploadDirectory . '/' . $photo;
        if (!move_uploaded_file($file['tmp_name'], $uploadedPhotoPath)) {
            $respond(500, [
                'success' => false,
                'message' => 'Foto gagal disimpan',
                'data' => null,
            ]);
        }
    }

    if ($method === 'POST') {
        $statement = $connection->prepare(
            "INSERT INTO mahasiswa (nim, nama, email, prodi_id, angkatan, status, foto)
             VALUES (:nim, :nama, :email, :prodi_id, :angkatan, 'aktif', :foto)"
        );
        $statement->execute([
            'nim' => $nim,
            'nama' => $nama,
            'email' => $email,
            'prodi_id' => $prodiId,
            'angkatan' => $angkatan,
            'foto' => $photo,
        ]);

        $student = $selectStudent($connection, (int) $connection->lastInsertId());

        $respond(201, [
            'success' => true,
            'message' => 'Data mahasiswa berhasil ditambahkan',
            'data' => $student,
        ]);
    }

    if ($method === 'PUT') {
        $statement = $connection->prepare(
            'UPDATE mahasiswa
             SET nim = :nim, nama = :nama, email = :email,
                 prodi_id = :prodi_id, angkatan = :angkatan, foto = :foto
             WHERE id = :id'
        );
        $statement->execute([
            'nim' => $nim,
            'nama' => $nama,
            'email' => $email,
            'prodi_id' => $prodiId,
            'angkatan' => $angkatan,
            'foto' => $photo,
            'id' => $id,
        ]);

        if ($uploadedPhotoPath !== null && !empty($currentStudent['foto'])) {
            $oldPhotoPath = __DIR__ . '/../public/uploads/' . basename($currentStudent['foto']);
            if (is_file($oldPhotoPath)) {
                unlink($oldPhotoPath);
            }
        }

        $respond(200, [
            'success' => true,
            'message' => 'Data mahasiswa berhasil diubah',
            'data' => $selectStudent($connection, $id),
        ]);
    }

} catch (\Throwable $exception) {
    if (isset($uploadedPhotoPath) && $uploadedPhotoPath !== null && is_file($uploadedPhotoPath)) {
        unlink($uploadedPhotoPath);
    }
    Logger::error($exception);

    $respond(500, [
        'success' => false,
        'message' => 'Terjadi kesalahan pada server saat memproses data mahasiswa',
        'data' => null,
    ]);
}
