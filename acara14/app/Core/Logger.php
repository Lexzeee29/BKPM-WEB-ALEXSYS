<?php

namespace App\Core;

use Throwable;

class Logger
{
    public static function error(Throwable $exception): void
    {
        $directory = dirname(__DIR__, 2) . '/storage/logs';

        if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
            error_log($exception->__toString());
            return;
        }

        $message = sprintf(
            "[%s] %s: %s in %s:%d\n%s\n",
            date(DATE_ATOM),
            get_class($exception),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            $exception->getTraceAsString()
        );

        error_log($message, 3, $directory . '/app.log');
    }
}