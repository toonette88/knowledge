<?php

namespace App\Tests\Controller;

use App\DataFixtures\AppFixtures;
use App\Entity\User;
use App\Entity\Lesson;
use App\Entity\Progression;
use App\Entity\Order;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;

class ProfileControllerTest extends WebTestCase
{
    // Test for user profile and course purchase
    public function testProfileUser(): void
    {
        $client = static::createClient();
        $entityManager = $client->getContainer()->get('doctrine')->getManager();
        
        // Retrieve the user from the database by email
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => 'user8@example.fr']);

        // User login
        $crawler = $client->request('GET', '/login');
        
        // Fill and submit the login form
        $form = $crawler->selectButton('Se Connecter')->form([
            'email' => 'user8@example.fr',
            'password' => 'pass_1234',
        ]);
        $client->submit($form);

        // Access a course page
        $client->request('GET', '/course/5');
        
        // Assert that the course page loads successfully
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Cursus d’initiation à la cuisine');

        // Simulate purchasing the full course
        $client->submitForm('Acheter le cursus complet');
        $client->followRedirect();

        // Assert that the order summary page is displayed
        $this->assertSelectorTextContains('h2', 'Récapitulatif de votre commande');

        // Retrieve the order for the user
        $order = $entityManager->getRepository(Order::class)->findOneBy(['user' => $user]);
        $this->assertNotNull($order, 'Order not found.');

        // Simulate successful payment from Stripe
        $client->request('GET', '/payment/success/' . $order->getId());

        // Simulate the webhook from Stripe indicating successful payment
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

        // Access the user's purchased lessons page
        $client->request('GET', '/user/lessons');
        
        // Assert that the purchased lessons page loads and contains the course
        $this->assertSelectorTextContains('h3', 'Mes Leçons Achetées');
        /*$this->assertSelectorTextContains('table td', "Les modes de cuissons");

        // Access a specific lesson
        $client->request('GET', '/user/lesson/10/1');

        // Verify that the lesson page loads successfully
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('h3', "Les modes de cuisson");

        // Retrieve the lesson and the user's progression
        $lessonRepository = $entityManager->getRepository(Lesson::class);
        $lesson = $lessonRepository->find(10);

        // Verify the user's progression for the lesson
        $progression = $entityManager->getRepository(Progression::class)->findOneBy([
            'user' => $user,
            'lesson' => $lesson,
        ]);
        $this->assertNotNull($progression);

        // Move to the next chapter
        $client->request('GET', '/user/lesson/10/next');
        $this->assertResponseRedirects('/user/lesson/10/2');
        
        // Verify that the progression has been updated and the user moved to the next chapter
        $updatedProgression = $entityManager->getRepository(Progression::class)->findOneBy([
            'user' => $user,
            'lesson' => $lesson,
        ]);
        $this->assertNotNull($updatedProgression);
        $this->assertEquals(2, $updatedProgression->getChapter()); // Verify that the user has moved to chapter 2*/
    }
}
