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
    $client = static::createClient();
    $entityManager = $client->getContainer()->get('doctrine')->getManager();
    $user = $entityManager->getRepository(User::class)->findOneBy(['email' => 'user3@example.fr']);

    // Connexion de l'utilisateur
    $crawler = $client->request('GET', '/login');
    $form = $crawler->selectButton('Se Connecter')->form([
        'email' => 'user3@example.fr',
        'password' => 'pass_1234',
    ]);
    $client->submit($form);

    // Accéder à un cursus
    $client->request('GET', '/course/5');
    $this->assertResponseIsSuccessful();
    $this->assertSelectorTextContains('h1', 'Cursus d’initiation à la cuisine');

    $client->submitForm('Acheter le cursus complet');
    $client->followRedirect();

    $this->assertSelectorTextContains('h2', 'Récapitulatif de votre commande');

    // Récupérer la commande créée
    $order = $entityManager->getRepository(Order::class)->findOneBy(['user' => $user]);
    $this->assertNotNull($order, 'La commande n\'a pas été trouvée.');

    // Simuler un paiement
    $client->request('POST', '/order/payment', [
        'paymentMethod' => 'credit_card',
        'amount' => 50,
    ]);
    //dump(' Soumission du formulaire de paiement...');

    //dump(' Simulation du webhook Stripe...');

    // Simuler l'appel du webhook Stripe
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
    //dump(' Webhook Stripe simulé envoyé');

    //dump(' Rechargement de la commande depuis la base de données...');
    $entityManager->clear();
    $order = $entityManager->getRepository(Order::class)->find($order->getId());

    //dump(' Commande après rechargement', ['id' => $order->getId(), 'statut' => $order->getStatus()]);

    //dump(' Vérification du statut final de la commande...', $order->getStatus());
    $this->assertEquals(OrderStatus::PAID, $order->getStatus(), 'Le statut de la commande n\'a pas été mis à jour.');

}*/
    }
