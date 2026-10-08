<?php

require_once __DIR__ . '/../app/Core/Logger.php';
require_once __DIR__ . '/../app/Core/Database.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $connection = \App\Core\Database::connection();
    $statement = $connection->query(
        'SELECT id, nim, nama, email FROM mahasiswa ORDER BY id ASC'
    );

    echo json_encode([
        'success' => true,
        'message' => 'Data berhasil diambil',
        'data' => $statement->fetchAll(),
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} catch (Throwable $exception) {
    http_response_code(500);

    echo json_encode([
        'success' => false,
        'message' => 'Data mahasiswa gagal diambil',
        'data' => [],
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}