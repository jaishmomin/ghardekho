<?php
declare(strict_types=1);

namespace App\Services;

/**
 * Minimal JWT helper for issuing and verifying tokens.
 * You can expand this later as needed.
 */
class JWTAuth
{
    private string $secret;

    public function __construct(?string $secret = null)
    {
        $this->secret = $secret ?? (getenv('JWT_SECRET') ?: 'changeme-secret-key');
    }

    /**
     * Generate a JWT token with HS256.
     */
    public function generate(array $payload, int $ttlSeconds = 3600): string
    {
        $header = ['alg' => 'HS256', 'typ' => 'JWT'];

        $now = time();
        $payload['iat'] = $payload['iat'] ?? $now;
        $payload['exp'] = $payload['exp'] ?? ($now + $ttlSeconds);

        $segments = [
            $this->base64UrlEncode(json_encode($header, JSON_UNESCAPED_UNICODE)),
            $this->base64UrlEncode(json_encode($payload, JSON_UNESCAPED_UNICODE))
        ];

        $signingInput = implode('.', $segments);
        $signature    = hash_hmac('sha256', $signingInput, $this->secret, true);
        $segments[]   = $this->base64UrlEncode($signature);

        return implode('.', $segments);
    }

    /**
     * Verify token and return payload array, or null if invalid/expired.
     */
    public function verify(string $token): ?array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }

        [$headerB64, $payloadB64, $sigB64] = $parts;
        $signingInput = $headerB64 . '.' . $payloadB64;

        $expectedSig = $this->base64UrlEncode(
            hash_hmac('sha256', $signingInput, $this->secret, true)
        );

        if (!hash_equals($expectedSig, $sigB64)) {
            return null;
        }

        $payloadJson = $this->base64UrlDecode($payloadB64);
        $payload     = json_decode($payloadJson, true);

        if (!is_array($payload)) {
            return null;
        }

        // check expiry
        if (isset($payload['exp']) && time() > (int)$payload['exp']) {
            return null;
        }

        return $payload;
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function base64UrlDecode(string $data): string
    {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $padLen = 4 - $remainder;
            $data .= str_repeat('=', $padLen);
        }
        return base64_decode(strtr($data, '-_', '+/')) ?: '';
    }
}
