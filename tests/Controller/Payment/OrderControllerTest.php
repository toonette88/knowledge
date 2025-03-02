<?php

namespace App\Tests\Controller\Payment;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;
use App\Entity\Order;
use App\Enum\OrderStatus;
use Stripe\Webhook;
use Stripe\Stripe;

class OrderControllerTest extends WebTestCase
{
    /*public function testOrderCreationAndPayment(): void
    {
        // Create a client instance to make requests
        $client = static::createClient();
        
        // Get the Entity Manager to interact with the database
        $entityManager = $client->getContainer()->get('doctrine')->getManager();
        
        // Retrieve a test user from the database
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => 'user3@example.fr']);
        
        // User login step
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Se Connecter')->form([
            'email' => 'user3@example.fr',
            'password' => 'pass_1234',
        ]);
        $client->submit($form);
        
        // User accessing a course page
        $client->request('GET', '/course/5');
        
        // Assert the response is successful and that the course title is displayed
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Cursus d’initiation à la cuisine');
        
        // User submits a form to purchase the course
        $client->submitForm('Acheter le cursus complet');
        $client->followRedirect();
        
        // Assert the user sees a summary of their order
        $this->assertSelectorTextContains('h2', 'Récapitulatif de votre commande');
        
        // Retrieve the created order from the database
        $order = $entityManager->getRepository(Order::class)->findOneBy(['user' => $user]);
        
        // Assert that the order exists in the database
        $this->assertNotNull($order, 'Order was not found.');
        
        // Simulate a payment request
        $client->request('POST', '/order/payment', [
            'paymentMethod' => 'credit_card',
            'amount' => 50,
        ]);

        // Simulate Stripe webhook request after a successful payment
        $client->request('POST', '/webhook/stripe', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'metadata' => ['order_id' => $order->getId()],
                ],
            ],
        ]));
        
        // Reload the order from the database
        $entityManager->clear();
        $order = $entityManager->getRepository(Order::class)->find($order->getId());
        
        // Assert the final order status is 'PAID'
        $this->assertEquals(OrderStatus::PAID, $order->getStatus(), 'The order status was not updated.');
    }*/
}
