<?php

namespace App\Tests\Controller\Payment;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;
use App\Entity\Order;
use App\Enum\OrderStatus;

class OrderControllerTest extends WebTestCase
{
    public function testOrderCreationAndPayment(): void
    {
        $client = static::createClient();
        $entityManager = $client->getContainer()->get('doctrine')->getManager();
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => 'user8@example.fr']);

        $crawler = $client->request('GET', '/login');
        
        $form = $crawler->selectButton('Se Connecter')->form([
            'email' => 'user8@example.fr',
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
        

        $form = $crawler->selectButton('Payer avec stripe');

        // Simuler un paiement
        $client->request('POST', '/order/payment', [
            'paymentMethod' => 'credit_card',
            'amount' => 50,
        ]);
        
        // Vérifier la mise à jour du statut de la commande
        $order = $entityManager->getRepository(Order::class)->findOneBy(['user' => $user]);
        $this->assertNotNull($order, 'La commande n\'a pas été trouvée.');
        $this->assertEquals(OrderStatus::PAID, $order->getStatus());

    }
}
