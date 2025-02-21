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
        $response->headers->set('Content-Security-Policy', "script-src 'self' 'sha256-...' https://js.stripe.com;");
    }

    public static function getSubscribedEvents()
    {
        // Le listener sur l'événement de réponse HTTP
        return [
            KernelEvents::RESPONSE => 'onKernelResponse',
        ];
    }
}
