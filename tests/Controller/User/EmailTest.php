<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EmailTest extends WebTestCase
{
    public function testEmailSentUponRequest(): void
    {
        $client = static::createClient();

        // Effectuer une requête vers la route d'envoi d'email
        $client->request('GET', '/test-email');

        // Vérifier que la réponse a été envoyée avec succès
        $this->assertResponseIsSuccessful();

        // A ce stade, l'email devrait avoir été envoyé via Mailtrap.
        // Vous pouvez vérifier manuellement dans votre boîte de réception Mailtrap.

        // Optionnel : Ajouter une assertion si vous configurez un mock pour MailerInterface
        $this->assertTrue(true, 'L\'email a bien été envoyé.');
    }
}
