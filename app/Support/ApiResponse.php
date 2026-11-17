<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiResponse
{
    /**
     * Return a standardized success JSON response.
     *
     * @param  array<string, mixed>  $meta
     */
    public static function success(mixed $data = null, string $message = 'Success', int $code = 200, array $meta = []): JsonResponse
    {
        $payload = [
            'success' => true,
            'message' => $message,
            'data' => $data,
        ];

        if (! empty($meta)) {
            $payload['meta'] = $meta;
        }

        return response()->json($payload, $code);
    }

    /**
     * Return a standardized error JSON response.
     */
    public static function error(string $message = 'Error', int $code = 400, mixed $errors = null): JsonResponse
    {
        $payload = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $payload['errors'] = $errors;
        }

        return response()->json($payload, $code);
    }

    /**
     * Attach deterministic ETag and safe Cache-Control/Vary headers (returning 304 on match).
     */
    public static function withConditionalCache(
        Request $request,
        JsonResponse $response,
        string $scope,
        bool $isPublic = false,
        int $maxAge = 0
    ): JsonResponse {
        $etag = sha1($scope.'|'.(string) $response->getContent());

        if ($isPublic) {
            $response->setPublic();
            $response->setMaxAge($maxAge);
        } else {
            $response->setPrivate();
            $response->setMaxAge($maxAge);
            $response->headers->addCacheControlDirective('no-cache', true);
            $response->headers->addCacheControlDirective('must-revalidate', true);
            $response->setVary('Authorization', false);
        }

        $response->setEtag($etag);
        $response->isNotModified($request);

        return $response;
    }
}
