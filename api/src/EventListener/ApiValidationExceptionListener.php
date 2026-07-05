<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Controller\Api\ApiResponseTrait;
use App\Exception\ApiValidationException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

#[AsEventListener]
class ApiValidationExceptionListener
{
    use ApiResponseTrait;

    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (!$exception instanceof ApiValidationException) {
            return;
        }

        $violations = $exception->getViolations();
        $errors = [];

        foreach ($violations as $violation) {
            $errors[$violation->getPropertyPath()] = $violation->getMessage();
        }

        $response = $this->apiError('Validation failed', Response::HTTP_UNPROCESSABLE_ENTITY, $errors);

        $event->setResponse($response);
    }
}
