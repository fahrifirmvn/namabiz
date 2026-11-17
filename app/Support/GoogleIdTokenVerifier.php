<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class GoogleIdTokenVerifier
{
    private const CERTS_URL = 'https://www.googleapis.com/oauth2/v1/certs';

    private const CACHE_KEY = 'google.oauth2.v1.certs';

    /**
     * Verify a Google ID token signature (RS256) and standard claims.
     *
     * @return array{sub: string, email: string, name: ?string, avatar: ?string}
     */
    public function verify(string $idToken): array
    {
        $clientIds = array_values(array_filter((array) config('services.google.client_ids', [])));
        if ($clientIds === []) {
            throw new RuntimeException('Google OAuth client IDs are not configured.');
        }

        $segments = explode('.', trim($idToken));
        if (count($segments) !== 3) {
            throw new RuntimeException('Malformed Google ID token.');
        }

        [$headerB64, $payloadB64, $signatureB64] = $segments;

        $header = $this->decodeJsonSegment($headerB64);
        $payload = $this->decodeJsonSegment($payloadB64);
        $signature = $this->decodeBase64Url($signatureB64);

        if (($header['alg'] ?? null) !== 'RS256' || ! is_string($header['kid'] ?? null) || $header['kid'] === '') {
            throw new RuntimeException('Unsupported Google ID token header.');
        }

        $certificate = $this->resolveCertificate($header['kid']);
        $publicKey = openssl_pkey_get_public($certificate);
        if ($publicKey === false) {
            throw new RuntimeException('Unable to load Google public key certificate.');
        }

        $verified = openssl_verify(
            "{$headerB64}.{$payloadB64}",
            $signature,
            $publicKey,
            OPENSSL_ALGO_SHA256
        );

        if ($verified !== 1) {
            throw new RuntimeException('Invalid Google ID token signature.');
        }

        $issuer = (string) ($payload['iss'] ?? '');
        if (! in_array($issuer, ['accounts.google.com', 'https://accounts.google.com'], true)) {
            throw new RuntimeException('Invalid Google ID token issuer.');
        }

        $audience = (string) ($payload['aud'] ?? '');
        if (! in_array($audience, $clientIds, true)) {
            throw new RuntimeException('Invalid Google ID token audience.');
        }

        $expiresAt = $payload['exp'] ?? null;
        if (! is_numeric($expiresAt) || (int) $expiresAt <= time()) {
            throw new RuntimeException('Google ID token has expired.');
        }

        $sub = (string) ($payload['sub'] ?? '');
        $email = strtolower(trim((string) ($payload['email'] ?? '')));
        $emailVerified = $payload['email_verified'] ?? false;

        if ($sub === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL) || ! in_array($emailVerified, [true, 1, '1', 'true'], true)) {
            throw new RuntimeException('Google ID token lacks a verified email.');
        }

        $picture = is_string($payload['picture'] ?? null) ? trim($payload['picture']) : null;
        if ($picture !== null && ! filter_var($picture, FILTER_VALIDATE_URL)) {
            $picture = null;
        }

        $name = is_string($payload['name'] ?? null) ? trim($payload['name']) : null;

        return [
            'sub' => $sub,
            'email' => $email,
            'name' => $name !== '' ? $name : null,
            'avatar' => $picture,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function decodeJsonSegment(string $segment): array
    {
        $decoded = json_decode($this->decodeBase64Url($segment), true);
        if (! is_array($decoded)) {
            throw new RuntimeException('Invalid Google ID token JSON segment.');
        }

        return $decoded;
    }

    private function decodeBase64Url(string $segment): string
    {
        $remainder = strlen($segment) % 4;
        if ($remainder > 0) {
            $segment .= str_repeat('=', 4 - $remainder);
        }

        $binary = base64_decode(strtr($segment, '-_', '+/'), true);
        if ($binary === false || $binary === '') {
            throw new RuntimeException('Invalid Google ID token base64url encoding.');
        }

        return $binary;
    }

    private function resolveCertificate(string $kid): string
    {
        $certs = $this->fetchCertificates();
        if (isset($certs[$kid]) && is_string($certs[$kid])) {
            return $certs[$kid];
        }

        Cache::forget(self::CACHE_KEY);
        $certs = $this->fetchCertificates();

        if (! isset($certs[$kid]) || ! is_string($certs[$kid])) {
            throw new RuntimeException('Matching Google signing certificate not found.');
        }

        return $certs[$kid];
    }

    /**
     * @return array<string, string>
     */
    private function fetchCertificates(): array
    {
        $cached = Cache::get(self::CACHE_KEY);
        if (is_array($cached) && $cached !== []) {
            return $cached;
        }

        $response = Http::timeout(5)->acceptJson()->get(self::CERTS_URL);
        if (! $response->successful()) {
            throw new RuntimeException('Unable to fetch Google signing certificates.');
        }

        $certs = $response->json();
        if (! is_array($certs) || $certs === []) {
            throw new RuntimeException('Google signing certificates response was empty.');
        }

        $normalized = [];
        foreach ($certs as $key => $pem) {
            if (is_string($key) && is_string($pem) && str_contains($pem, 'BEGIN CERTIFICATE')) {
                $normalized[$key] = $pem;
            }
        }

        if ($normalized !== []) {
            Cache::put(self::CACHE_KEY, $normalized, now()->addHours(1));
        }

        return $normalized;
    }
}
