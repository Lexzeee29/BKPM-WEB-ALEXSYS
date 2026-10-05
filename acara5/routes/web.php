<?php

$routes = [
    'GET' => [
        '/' => ['HomeController', 'index'],
        '/mahasiswa' => ['MahasiswaController', 'index'],
        '/mahasiswa/create' => ['MahasiswaController', 'create'],
        '/mahasiswa/{id}' => ['MahasiswaController', 'show'],
    ],
    'POST' => [
        '/mahasiswa' => ['MahasiswaController', 'store'],
    ],
];