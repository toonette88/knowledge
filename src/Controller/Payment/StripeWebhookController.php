<?php

namespace App\Payment\Controller;

use App\Entity\Order;
use App\Entity\Billing;
use App\Enum\OrderStatus;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Stripe\Event;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class StripeWebhookController extends AbstractController
{
    #[Route('/webhook/stripe', name: 'stripe_webhook', methods: ['POST'])]
    public function handleStripeWebhook(Request $request, EntityManagerInterface $em, LoggerInterface $logger): JsonResponse
    {
        $payload = $request->getContent();
        $sigHeader = $request->headers->get('stripe-signature');
        $secret = $_ENV['STRIPE_WEBHOOK_SECRET'];

        if (!$secret) {
            $logger->error('STRIPE_WEBHOOK_SECRET is not set');
            return new JsonResponse(['error' => 'Webhook secret is missing'], 400);
        }

        try {
            $event = Event::constructFrom(json_decode($payload, true));
        } catch (\UnexpectedValueException $e) {
            $logger->error('Webhook error: Invalid payload');
            return new JsonResponse(['error' => 'Invalid payload'], 400);
        }

        // Vérification de la signature webhook
        try {
            Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sigHeader,
                $secret
            );
        } catch (\Exception $e) {
            $logger->error('Webhook signature verification failed: '. $e->getMessage());
            return new JsonResponse(['error' => 'Invalid signature'], 400);
        }

        // Traiter les événements Stripe
        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            $orderId = $session->metadata->order_id ?? null;

            $orderId = $session->metadata->order_id ?? null;
            if (!$orderId) {
                $logger->error('Metadata order_id is missing');
                return new JsonResponse(['error' => 'Metadata order_id is missing'], 400);
            }

            $order = $em->getRepository(Order::class)->find($orderId);
            if ($order) {
                $order->setStatus(OrderStatus::PAID);

                // Création de la facture
                $billing = new Billing();
                $billing->setOrder($order);
                $billing->setUser($order->getUser());
                $billing->setStripePaymentId((string) $session->payment_intent);
                $billing->setAmount($order->getTotal());
                $billing->setCreatedAt(new \DateTimeImmutable());

                $em->persist($billing);
                $em->flush();
            }
        }

        return new JsonResponse(['status' => 'success']);
    }
}
