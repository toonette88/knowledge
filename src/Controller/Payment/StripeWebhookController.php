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
        $logger->info(' Webhook Stripe reçu');
        dump(' Webhook Stripe reçu');

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
        $logger->info(' Type d\'événement Stripe reçu', ['event_type' => $event->type]);
        dump(' Type d\'événement Stripe reçu', $event->type);

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            $orderId = $session->metadata->order_id ?? null;
            
            $logger->info(' Récupération de order_id depuis metadata', ['order_id' => $orderId]);
            dump(' order_id extrait', $orderId);

            if (!$orderId) {
                $logger->error('Metadata order_id is missing in session', ['session' => $session]);
                return new JsonResponse(['error' => 'Metadata order_id is missing'], 400);
            }

            $order = $em->getRepository(Order::class)->find($orderId);
            dump('🔍 Order ID reçu dans le webhook', $orderId);

            if (!$order) {
                $logger->error(' Commande non trouvée', ['order_id' => $orderId]);
                dump(' Commande non trouvée pour order_id', $orderId);
            } else {
                $logger->info(' Commande trouvée', ['order_id' => $order->getId(), 'status' => $order->getStatus()]);
                dump(' Commande trouvée', $order->getId(), $order->getStatus());
            }
            
            // Mise à jour du statut de la commande
            $order->setStatus(OrderStatus::PAID);
            $em->persist($order);
            $em->flush();
            
            $logger->info(' Statut de la commande mis à jour', ['order_id' => $order->getId(), 'nouveau_statut' => $order->getStatus()]);
            dump(' Statut mis à jour', $order->getStatus());
            

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
