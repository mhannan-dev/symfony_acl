<?php

declare(strict_types=1);

namespace App\Controller\Api;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait ApiResponseTrait
{
    /**
     * Returns a standard JSON response for successful API requests.
     */
    protected function apiSuccess(mixed $data = null, int $status = Response::HTTP_OK, array $headers = []): JsonResponse
    {
        return new JsonResponse([
            'status' => 'success',
            'data'   => $data,
        ], $status, $headers);
    }

    /**
     * Returns a standard JSON response for failed API requests.
     */
    protected function apiError(string $message, int $status = Response::HTTP_BAD_REQUEST, array $errors = [], array $headers = []): JsonResponse
    {
        $payload = [
            'status'  => 'error',
            'message' => $message,
        ];

        if (!empty($errors)) {
            $payload['errors'] = $errors;
        }

        return new JsonResponse($payload, $status, $headers);
    }
}
