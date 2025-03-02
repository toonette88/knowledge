<?php

namespace App\Controller\Payment;

use App\Entity\Order;
use App\Entity\Billing;
use App\Enum\OrderStatus;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class StripePaymentController extends AbstractController {

    // Route to create a Stripe checkout session for a given order
    #[Route('/payment/create-session/{orderId}', name:'payment_create_session', methods: ['POST'])]
    #[IsGranted('ROLE_USER')] // Only accessible by authenticated users
    public function createSession(int $orderId, EntityManagerInterface $entityManager)
    {
        // Retrieve the order by ID
        $order = $entityManager->getRepository(Order::class)->find($orderId);

        // If the order is not found or does not belong to the current user, return an error
        if (!$order || $order->getUser() !== $this->getUser()) {
            return $this->json(['error' => 'Order not found or unauthorized access.'], 404);
        }

        // Set Stripe API key
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        // Create a new Stripe Checkout session
        $checkoutSession = Session::create([
            'payment_method_types' => ['card'], // Accept card payments
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur', // Set the currency to EUR
                    'product_data' => [
                        'name' => 'Purchase of training courses', // Product name
                    ],
                    'unit_amount' => (int) ($order->getTotal() * 100), // Amount in cents
                ],
                'quantity' => 1, // Quantity of the product
            ]],
            'mode' => 'payment', // Payment mode (single payment)
            'success_url' => $this->generateUrl(
                'payment_success', 
                ['orderId' => $order->getId()], 
                UrlGeneratorInterface::ABSOLUTE_URL // Redirect to success page after payment
            ),
            'cancel_url' => $this->generateUrl(
                'payment_cancel', 
                ['orderId' => $order->getId()], 
                UrlGeneratorInterface::ABSOLUTE_URL // Redirect to cancel page if payment is canceled
            ),
            'metadata' => [
                'order_id' => $order->getId(), // Add order ID to metadata for reference
            ],
        ]);

        // Redirect to the Stripe checkout page
        return $this->redirect($checkoutSession->url);
    }

    // Route to handle the success response after a successful payment
    #[Route('/payment/success/{orderId}', name:'payment_success')]
    #[IsGranted('ROLE_USER')] // Only accessible by authenticated users
    public function success(int $orderId, EntityManagerInterface $em)
    {
        // Retrieve the order by ID
        $order = $em->getRepository(Order::class)->find($orderId);

        // Check if the order exists and belongs to the current user
        if (!$order || $order->getUser() !== $this->getUser()){
            return $this->json(['error' => 'Order not found or unauthorized access.'], 404);
        }

        // Render the order success template with order data
        return $this->render('order/OrderSuccess.html.twig', ['order' => $order]);
    }

    // Route to handle the cancel response if the payment is canceled
    #[Route('/payment/cancel/{orderId}', name:'payment_cancel')]
    #[IsGranted('ROLE_USER')] // Only accessible by authenticated users
    public function cancel(int $orderId, EntityManagerInterface $em)
    {
        // Retrieve the order by ID
        $order = $em->getRepository(Order::class)->find($orderId);

        // Check if the order exists and belongs to the current user
        if (!$order || $order->getUser() !== $this->getUser()){
            return $this->json(['error' => 'Order not found or unauthorized access.'], 404);
        }

        // Set the order status to canceled
        $order->setStatus(OrderStatus::CANCELED);
        $em->persist($order);
        $em->flush();

        // Add a flash message indicating the cancellation
        $this->addFlash('error', 'The payment was canceled. Your order has been canceled.');

        // Redirect to the catalog page
        return $this->redirectToRoute('catalog');
    }
}
