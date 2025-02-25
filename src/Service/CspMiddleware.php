<?php 

namespace App\Service;

use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CspMiddleware implements EventSubscriberInterface
{
    public function onKernelResponse(ResponseEvent $event)
    {
        $response = $event->getResponse();
        $response->headers->set('Content-Security-Policy', "script-src 'self'  https://js.stripe.com 'nonce-20a83e1045b0cc7f93bdbe64d2a764d6';");
    }

    public static function getSubscribedEvents(): array
    {
        // Le listener sur l'événement de réponse HTTP
        return [
            KernelEvents::RESPONSE => 'onKernelResponse',
        ];
    }
}
