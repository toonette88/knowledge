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
    // Route for Stripe Webhook to handle payment notifications
    #[Route('/webhook/stripe', name: 'stripe_webhook', methods: ['POST'])]
    public function handleStripeWebhook(Request $request, EntityManagerInterface $em, LoggerInterface $logger): JsonResponse
    {
        // Log received webhook for debugging
        $logger->info('Stripe Webhook received');
        dump('Stripe Webhook received');

        // Get the content of the POST request and the Stripe signature header
        $payload = $request->getContent();
        $sigHeader = $request->headers->get('stripe-signature');
        $secret = $_ENV['STRIPE_WEBHOOK_SECRET']; // Secret key for webhook verification

        // Check if the webhook secret is missing from the environment
        if (!$secret) {
            $logger->error('STRIPE_WEBHOOK_SECRET is not set');
            return new JsonResponse(['error' => 'Webhook secret is missing'], 400);
        }

        try {
            // Set Stripe's API key for authentication
            Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);
            // Verify the webhook's signature to ensure it's from Stripe
            $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (\Exception $e) {
            // Log an error if signature verification fails
            $logger->error('Webhook signature verification failed: ' . $e->getMessage());
            return new JsonResponse(['error' => 'Invalid signature'], 400);
        }

        // Log the type of event received for debugging
        $logger->info('Stripe event type received', ['event_type' => $event->type]);
        dump('Stripe event type received', $event->type);

        // Handle the event if it’s a checkout session completion
        if ($event->type === 'checkout.session.completed') {
            // Get the session data from the event
            $session = $event->data->object;
            // Retrieve the order ID from session metadata
            $orderId = $session->metadata->order_id ?? null;

            // Log the order ID
            $logger->info('Retrieving order_id from metadata', ['order_id' => $orderId]);
            dump('Order ID extracted', $orderId);

            // Check if the order ID is available
            if (!$orderId) {
                $logger->error('Metadata order_id is missing in session', ['session' => $session]);
                return new JsonResponse(['error' => 'Metadata order_id is missing'], 400);
            }

            // Retrieve the corresponding order from the database
            $order = $em->getRepository(Order::class)->find($orderId);
            dump('🔍 Order ID received in webhook', $orderId);

            // Check if the order exists
            if (!$order) {
                $logger->error('Order not found', ['order_id' => $orderId]);
                dump('Order not found for order_id', $orderId);
            } else {
                // Log if the order was found successfully
                $logger->info('Order found', ['order_id' => $order->getId(), 'status' => $order->getStatus()]);
                dump('Order found', $order->getId(), $order->getStatus());
            }

            // Update the order status to 'paid'
            $order->setStatus(OrderStatus::PAID);
            $em->persist($order); // Persist the updated order
            $em->flush(); // Commit the changes

            // Log the status update
            $logger->info('Order status updated', ['order_id' => $order->getId(), 'new_status' => $order->getStatus()]);
            dump('Status updated', $order->getStatus());

            // Create a new billing record for this order
            $billing = new Billing();
            $billing->setOrder($order);
            $billing->setUser($order->getUser());
            $billing->setStripePaymentId((string) $session->payment_intent); // Store the Stripe payment ID
            $billing->setAmount($order->getTotal()); // Set the amount for the billing
            $billing->setCreatedAt(new \DateTimeImmutable()); // Set the current date and time

            // Persist the billing data
            $em->persist($billing);
            $em->flush(); // Commit the changes

            // Log the successful payment processing
            $logger->info('Payment processed successfully', ['order_id' => $orderId, 'billing' => $billing]);
        } else {
            // Log any events that are not handled by this controller
            $logger->info('Event type not handled', ['event_type' => $event->type]);
        }

        // Return a successful response to Stripe
        return new JsonResponse(['status' => 'success']);
    }
}
