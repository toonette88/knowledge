<?php

namespace App\Service;

use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class CspMiddleware implements EventSubscriberInterface
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function onKernelResponse(ResponseEvent $event)
    {
        // Retrieve the response object from the event
        $response = $event->getResponse();

        // Generate a random nonce for the request (this will be used for inline scripts)
        $nonce = base64_encode(random_bytes(16)); // Generating a unique nonce for this request

        // Set the Content-Security-Policy header with the generated nonce
        $response->headers->set('Content-Security-Policy',"script-src 'self' https://js.stripe.com 'nonce-{$nonce}'");

        // Store the nonce in the request attributes to pass it to Twig templates
        $this->requestStack->getCurrentRequest()->attributes->set('csp_nonce', $nonce);
    }

    public static function getSubscribedEvents(): array
    {
        // Listen to the RESPONSE event to modify the response headers
        return [
            KernelEvents::RESPONSE => 'onKernelResponse',
        ];
    }
}
