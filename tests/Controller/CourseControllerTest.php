<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CourseControllerTest extends WebTestCase
{
    public function testCreateCourse()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
    
        $form = $crawler->selectButton('Se Connecter')->form([
            'email' => 'admin@example.fr',
            'password' => 'pass_1234',
        ]);
        $client->submit($form);
        
        // Accède à la page de création de cursus
        $crawler = $client->request('GET', 'admin/course/new');
        
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h3', 'Créer un nouveau Cursus');

        // Soumet le formulaire de création
        $form = $crawler->selectButton('Enregistrer')->form([
            'course[title]' => 'Test Course',
            'course[description]' => 'Description of test course',
            'course[price]' => 100,
        ]);

        $client->submit($form);
        $this->assertResponseRedirects('/admin/course');
        
        // Vérifie que le cours a été créé
        $client->followRedirect();
        $this->assertSelectorTextContains('table tbody', 'Test Course');
    }

   public function testEditCourse()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
    
        $form = $crawler->selectButton('Se Connecter')->form([
            'email' => 'admin@example.fr',
            'password' => 'pass_1234',
        ]);
        $client->submit($form);

        // Accède à la page d'édition
        $crawler = $client->request('GET', '/admin/course/7/edit');
        $this->assertSelectorTextContains('h3', 'Modifier le Cursus');


        // Soumet le formulaire d'édition
        $form = $crawler->selectButton('Mettre à jour')->form([
            'course[title]' => 'Updated Course',
            'course[description]' => 'Updated description',
            'course[price]' => 120
        ]);
        $client->submit($form);

        $this->assertResponseRedirects('/admin/course');
        
        // Vérifie que le cours a bien été mis à jour
        $client->followRedirect();
        $this->assertSelectorTextContains('table tbody', 'Updated Course');
    }

    public function testDeleteCourse()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
    
        $form = $crawler->selectButton('Se Connecter')->form([
            'email' => 'admin@example.fr',
            'password' => 'pass_1234',
        ]);
        $client->submit($form);

        // Accède à la page de suppression
        $crawler = $client->request('GET', 'admin/course/7/edit');
        $this->assertSelectorTextContains('h3', 'Modifier le Cursus');

        $deleteForm = $crawler->selectButton('Supprimer')->form();

        // Soumet le formulaire de suppression
        $client->submit($deleteForm);

        $this->assertResponseRedirects('/admin/course');
        $client->followRedirect();
        
        $crawler = $client->request('GET', 'admin/course/7/edit');
        $this->assertResponseStatusCodeSame(404);

    }
}
