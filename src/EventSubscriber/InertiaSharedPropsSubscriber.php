<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use Rompetomp\InertiaBundle\Architecture\InertiaInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class InertiaSharedPropsSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly InertiaInterface $inertia
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            // Priority 0 is usually fine, runs after session starts
            KernelEvents::REQUEST => ['onKernelRequest', 0],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        
        if (!$request->hasSession()) {
            return;
        }

        $session = $request->getSession();
        
        // Always share an errors object (Inertia Vue expects this to exist)
        $errors = (object)[];
        
        if ($session->getFlashBag()->has('inertia_errors')) {
            $flashedErrors = $session->getFlashBag()->peek('inertia_errors')[0] ?? [];
            if (!empty($flashedErrors)) {
                $errors = $flashedErrors;
            }
        }
        
        $this->inertia->share('errors', $errors);
    }
}
