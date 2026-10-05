<?php

require_once __DIR__ . '/../app/Models/Mahasiswa.php';

use App\Models\Mahasiswa;

$page = $_GET['page'] ?? 'index';

if ($page === 'create') {
	$title = 'Tambah Mahasiswa | SI Akademik';
	$content = __DIR__ . '/../app/Views/mahasiswa/create.php';
	require __DIR__ . '/../app/Views/layouts/main.php';
	exit;
}

$mahasiswa = [
	new Mahasiswa('1230001', 'Andi Setiawan', 'Teknik Informatika'),
	new Mahasiswa('E41230002', 'Siti Rahma', 'Manajemen Informatika'),
	new Mahasiswa('E41224003', 'Budi Santoso', 'Teknik Komputer'),
	new Mahasiswa('E41266004', 'Ani Pratiwi', 'Teknik Informatika'),
];

$title = 'Data Mahasiswa | SI Akademik';
$content = __DIR__ . '/../app/Views/mahasiswa/index.php';
require __DIR__ . '/../app/Views/layouts/main.php';
