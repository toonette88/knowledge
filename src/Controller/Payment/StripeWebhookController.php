<?php

namespace App\Controller\Payment;

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
            Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);
            $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (\Exception $e) {
            $logger->error('Webhook signature verification failed: ' . $e->getMessage());
            return new JsonResponse(['error' => 'Invalid signature'], 400);
        }

        // Logs pour vérifier les données de l'événement
        $logger->info('Stripe webhook received', ['event' => $event]);

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            $orderId = $session->metadata->order_id ?? null;
            
            if (!$orderId) {
                $logger->error('Metadata order_id is missing in session', ['session' => $session]);
                return new JsonResponse(['error' => 'Metadata order_id is missing'], 400);
            }

            $order = $em->getRepository(Order::class)->find($orderId);
            if (!$order) {
                $logger->error('Order not found for order_id', ['order_id' => $orderId]);
                return new JsonResponse(['error' => 'Order not found'], 404);
            }

            // Mise à jour du statut de la commande
            $order->setStatus(OrderStatus::PAID);
            $em->persist($order);
            $em->flush();

            // Création de la facturation
            $billing = new Billing();
            $billing->setOrder($order);
            $billing->setUser($order->getUser());
            $billing->setStripePaymentId((string) $session->payment_intent);
            $billing->setAmount($order->getTotal());
            $billing->setCreatedAt(new \DateTimeImmutable());

            // Persistance de la facturation
            $em->persist($billing);
            $em->flush();

            $logger->info('Payment processed successfully', ['order_id' => $orderId, 'billing' => $billing]);
        } else {
            $logger->info('Event type not handled', ['event_type' => $event->type]);
        }

        return new JsonResponse(['status' => 'success']);
    }
}
