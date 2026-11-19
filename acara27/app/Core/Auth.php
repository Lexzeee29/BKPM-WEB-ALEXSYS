<?php

namespace App\Core;

use JsonException;
use RuntimeException;

class Auth
{
    private const TOKEN_TTL = 300;

    private static function base64url(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function decodeBase64url(string $data): string|false
    {
        if ($data === '' || !preg_match('/^[A-Za-z0-9_-]+$/D', $data)) {
            return false;
        }

        $remainder = strlen($data) % 4;

        if ($remainder === 1) {
            return false;
        }

        $decoded = base64_decode(
            strtr($data, '-_', '+/') . str_repeat('=', (4 - $remainder) % 4),
            true
        );

        return $decoded === false ? false : $decoded;
    }

    private static function secret(): string
    {
        $secret = getenv('JWT_SECRET');

        if ($secret === false || $secret === '') {
            $secret = $_ENV['JWT_SECRET'] ?? $_SERVER['JWT_SECRET'] ?? '';
        }

        if (!is_string($secret) || strlen($secret) < 32) {
            throw new RuntimeException('JWT_SECRET must be configured with at least 32 characters.');
        }

        return $secret;
    }

    public static function generateToken(array $payload): string
    {
        try {
            $header = self::base64url(json_encode(
                ['alg' => 'HS256', 'typ' => 'JWT'],
                JSON_THROW_ON_ERROR
            ));
            $payload['exp'] = time() + self::TOKEN_TTL;
            $payloadEnc = self::base64url(json_encode($payload, JSON_THROW_ON_ERROR));
        } catch (JsonException $exception) {
            throw new RuntimeException('JWT data could not be encoded.', 0, $exception);
        }

        $signature = self::base64url(hash_hmac(
            'sha256',
            "$header.$payloadEnc",
            self::secret(),
            true
        ));

        return "$header.$payloadEnc.$signature";
    }

    public static function validateToken(string $token): array|false
    {
        $parts = explode('.', $token);

        if (count($parts) !== 3) {
            return false;
        }

        [$header, $payload, $signature] = $parts;
        $headerJson = self::decodeBase64url($header);
        $payloadJson = self::decodeBase64url($payload);
        $providedSignature = self::decodeBase64url($signature);

        if ($headerJson === false || $payloadJson === false || $providedSignature === false) {
            return false;
        }

        try {
            $headerData = json_decode($headerJson, true, 512, JSON_THROW_ON_ERROR);
            $data = json_decode($payloadJson, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return false;
        }

        if (
            !is_array($headerData) ||
            ($headerData['alg'] ?? null) !== 'HS256' ||
            ($headerData['typ'] ?? null) !== 'JWT' ||
            !is_array($data) ||
            !isset($data['exp']) ||
            !is_int($data['exp']) ||
            $data['exp'] <= time()
        ) {
            return false;
        }

        $expectedSignature = hash_hmac(
            'sha256',
            "$header.$payload",
            self::secret(),
            true
        );

        if (!hash_equals($expectedSignature, $providedSignature)) {
            return false;
        }

        return $data;
    }

    public static function requireAuth(): array
    {
        $headers = function_exists('getallheaders') ? getallheaders() : [];
        $authorization = '';

        if (is_array($headers)) {
            foreach ($headers as $name => $value) {
                if (strcasecmp((string) $name, 'Authorization') === 0 && is_string($value)) {
                    $authorization = $value;
                    break;
                }
            }
        }

        if ($authorization === '') {
            $authorization = $_SERVER['HTTP_AUTHORIZATION']
                ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION']
                ?? '';
        }

        $matches = [];

        if (!is_string($authorization) || !preg_match('/^Bearer\s+(\S+)$/i', trim($authorization), $matches)) {
            Response::json(401, false, 'Token tidak ditemukan');
        }

        $payload = self::validateToken($matches[1]);

        if ($payload === false) {
            Response::json(401, false, 'Token tidak valid atau kedaluwarsa');
        }

        return $payload;
    }

    public static function requireAdmin(): array
    {
        $payload = self::requireAuth();

        if (($payload['role'] ?? null) !== 'admin') {
            Response::json(403, false, 'Akses ditolak, hanya admin yang diizinkan');
        }

        return $payload;
    }
}
