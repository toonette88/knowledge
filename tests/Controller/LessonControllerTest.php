<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LessonControllerTest extends WebTestCase
{
    public function testCreateLesson()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
    
        $form = $crawler->selectButton('Se Connecter')->form([
            'email' => 'admin@example.fr',
            'password' => 'pass_1234',
        ]);
        $client->submit($form);
        
        // Accède à la page de création de la leçon
        $crawler = $client->request('GET', 'admin/lesson/new');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h3', 'Ajouter une nouvelle Leçon');

        // Soumet le formulaire de création
        $form = $crawler->selectButton('Enregistrer')->form([
            'lesson[title]' => 'Test Lesson',
            'lesson[price]' => 25,
            'lesson[course]' => "1", 
        ]);

        $client->submit($form);

        $this->assertResponseRedirects('/admin/lesson/');
        
        // Vérifie que la leçon a bien été créée
        $client->followRedirect();
        $this->assertSelectorTextContains('h3', 'Liste des Leçons');
        $this->assertSelectorTextContains('table tbody', 'Test Lesson');
    }

  public function testEditLesson()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');
    
        $form = $crawler->selectButton('Se Connecter')->form([
            'email' => 'admin@example.fr',
            'password' => 'pass_1234',
        ]);
        $client->submit($form);

        // Accède à la page d'édition
        $crawler = $client->request('GET', '/admin/lesson/13/edit');
        $this->assertSelectorTextContains('h3', 'Modifier la Leçon');

        // Soumet les modifications du formulaire
        $form = $crawler->selectButton('Mettre à jour')->form([
            'lesson[title]' => 'Updated Lesson',
            'lesson[price]' => 30,
            'lesson[course]' => "1",
        ]);
        $client->submit($form);

        $this->assertResponseRedirects('/admin/lesson/');
        
        // Vérifie que la leçon a bien été mise à jour
        $client->followRedirect();
        $this->assertSelectorTextContains('table tbody', 'Updated Lesson');
    }

    public function testDeleteLesson()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Se Connecter')->form([
            'email' => 'admin@example.fr',
            'password' => 'pass_1234',
        ]);
        $client->submit($form);

        $crawler = $client->request('GET', 'admin/lesson/12/edit');
        $this->assertSelectorTextContains('h3', 'Modifier la Leçon');
        $deleteForm = $crawler->selectButton('Supprimer')->form();

        // Soumet le formulaire de suppression
        $client->submit($deleteForm);
    


        $this->assertResponseRedirects('/admin/lesson');
        dump($client->getResponse()->getContent());
        $client->followRedirect();

        $crawler = $client->request('GET', 'admin/lesson/12/edit');
        $this->assertResponseStatusCodeSame(404);
    }
}