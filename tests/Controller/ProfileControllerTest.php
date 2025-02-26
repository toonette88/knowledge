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
    public function testProfileUser(): void
    {
        $client = static::createClient();
        $entityManager = $client->getContainer()->get('doctrine')->getManager();
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => 'user8@example.fr']);

        // Connexion de l'utilisateur
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

        // Récupérer la commande
        $order = $entityManager->getRepository(Order::class)->findOneBy(['user' => $user]);
        $this->assertNotNull($order, 'La commande n\'a pas été trouvée.');

        // Simuler le retour de Stripe après paiement réussi
        $client->request('GET', '/payment/success/' . $order->getId());

   
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

        $client->request('GET', '/user/lessons');
        $this->assertSelectorTextContains('h3', 'Mes Leçons Achetées');
        /*$this->assertSelectorTextContains('table td', "Les modes de cuissons");

        // Accéder à la leçon
        $client->request('GET', '/user/lesson/10/1');

        // Vérifier que la page de leçon s'affiche correctement
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('h3', "Les modes de cuisson");

        // Récupérer la leçon et la progression
        $lessonRepository = $entityManager->getRepository(Lesson::class);
        $lesson = $lessonRepository->find(10);

        // Vérifier la progression de l'utilisateur
        $progression = $entityManager->getRepository(Progression::class)->findOneBy([
            'user' => $user,
            'lesson' => $lesson,
        ]);
        $this->assertNotNull($progression);

        // Passer au chapitre suivant
        $client->request('GET', '/user/lesson/10/next');
        $this->assertResponseRedirects('/user/lesson/10/2');
        
        // Vérifier que la progression a été mise à jour
        $updatedProgression = $entityManager->getRepository(Progression::class)->findOneBy([
            'user' => $user,
            'lesson' => $lesson,
        ]);
        $this->assertNotNull($updatedProgression);
        $this->assertEquals(2, $updatedProgression->getChapter()); // Vérifie que l'utilisateur est passé au chapitre 2*/
    }
}
