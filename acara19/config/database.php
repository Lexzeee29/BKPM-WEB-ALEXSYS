<?php

defined('APP_ENV') || define(
    'APP_ENV',
    $_ENV['APP_ENV'] ?? $_SERVER['APP_ENV'] ?? 'development'
);

return [
    'host' => 'localhost',
    'dbname' => 'si_akademik',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
    'app_env' => APP_ENV,
];
