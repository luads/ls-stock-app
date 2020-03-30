<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Share\Exception\RateLimitedException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ApiExceptionSubscriber implements EventSubscriberInterface
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $statusCode = Response::HTTP_BAD_REQUEST;

        if ($exception instanceof RateLimitedException) {
            $statusCode = Response::HTTP_TOO_MANY_REQUESTS;
        }

        $response = new JsonResponse(['message' => $exception->getMessage()], $statusCode);

        $event->setResponse($response);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }
}
