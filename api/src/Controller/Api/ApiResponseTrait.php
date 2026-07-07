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
            'data' => $data,
        ], $status, $headers);
    }

    /**
     * Returns a standard JSON response for failed API requests.
     */
    protected function apiError(string $message, int $status = Response::HTTP_BAD_REQUEST, array $errors = [], array $headers = []): JsonResponse
    {
        $payload = [
            'status' => 'error',
            'message' => $message,
        ];

        if (!empty($errors)) {
            $payload['errors'] = $errors;
        }

        return new JsonResponse($payload, $status, $headers);
    }

    /**
     * Returns a standard JSON response for server errors (500).
     */
    protected function apiServerError(string $message = 'Internal Server Error', ?\Throwable $exception = null, array $headers = []): JsonResponse
    {
        $payload = [
            'status' => 'error',
            'message' => $message,
        ];

        // In a real application, you might want to only expose exception details in dev environment
        if ($exception && 'prod' !== $_ENV['APP_ENV']) {
            $payload['exception'] = [
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ];
        }

        return new JsonResponse($payload, Response::HTTP_INTERNAL_SERVER_ERROR, $headers);
    }

    /**
     * Returns a standard JSON response for not found errors (404).
     */
    protected function apiNotFound(string $message = 'Resource not found', array $headers = []): JsonResponse
    {
        return $this->apiError($message, Response::HTTP_NOT_FOUND, [], $headers);
    }

    /**
     * Returns a standard JSON response for unauthorized errors (401).
     */
    protected function apiUnauthorized(string $message = 'Unauthorized', array $headers = []): JsonResponse
    {
        return $this->apiError($message, Response::HTTP_UNAUTHORIZED, [], $headers);
    }

    /**
     * Returns a standard JSON response for forbidden errors (403).
     */
    protected function apiForbidden(string $message = 'Forbidden', array $headers = []): JsonResponse
    {
        return $this->apiError($message, Response::HTTP_FORBIDDEN, [], $headers);
    }

    /**
     * Returns a standard JSON response for validation errors (422).
     */
    protected function apiValidationError(string $message = 'Validation failed', array $errors = [], array $headers = []): JsonResponse
    {
        return $this->apiError($message, Response::HTTP_UNPROCESSABLE_ENTITY, $errors, $headers);
    }
}
