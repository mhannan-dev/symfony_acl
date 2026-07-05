<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Exception\InertiaValidationException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

#[AsEventListener]
class InertiaValidationExceptionListener
{
    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (!$exception instanceof InertiaValidationException) {
            return;
        }

        $request = $event->getRequest();
        $violations = $exception->getViolations();
        $errors = [];

        foreach ($violations as $violation) {
            $errors[$violation->getPropertyPath()] = $violation->getMessage();
        }

        // Store errors directly into the session to avoid IDE getFlashBag warnings
        $request->getSession()->set('_login_errors', $errors);

        // Redirect back to the referring page (where the form is)
        $referer = $request->headers->get('referer', '/');
        // HTTP 303 See Other is standard for Inertia post-submit redirects
        $event->setResponse(new RedirectResponse($referer, 303));
    }
}
