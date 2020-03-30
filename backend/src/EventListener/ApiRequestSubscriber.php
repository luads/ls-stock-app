<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class ApiRequestSubscriber implements EventSubscriberInterface
{
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $user = (string) $request->headers->get('X-User');

        if (!$this->requestNeedsAuth($request->getRequestUri())) {
            return;
        }

        if (!$user) {
            throw new BadRequestHttpException('Invalid user');
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }

    private function requestNeedsAuth(string $uri): bool
    {
        return strpos($uri, '/v1') !== false;
    }
}
