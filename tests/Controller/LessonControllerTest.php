<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LessonControllerTest extends WebTestCase
{
    // Test for creating a lesson
    public function testCreateLesson()
    {
        $client = static::createClient();
        
        // Log in as admin
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Se Connecter')->form([
            'email' => 'admin@example.fr',
            'password' => 'pass_1234',
        ]);
        $client->submit($form);
        
        // Access the lesson creation page
        $crawler = $client->request('GET', 'admin/lesson/new');

        // Check that the page loads successfully
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h3', 'Ajouter une nouvelle Leçon');

        // Submit the lesson creation form
        $form = $crawler->selectButton('Enregistrer')->form([
            'lesson[title]' => 'Test Lesson',
            'lesson[price]' => 25,
            'lesson[course]' => "1", // Assuming "1" is the valid course ID
        ]);
        $client->submit($form);

        // Check for redirection after form submission
        $this->assertResponseRedirects('/admin/lesson/');
        
        // Follow the redirect and verify the lesson appears in the lesson list
        $client->followRedirect();
        $this->assertSelectorTextContains('h3', 'Liste des Leçons');
        $this->assertSelectorTextContains('table tbody', 'Test Lesson');
    }

    // Test for editing a lesson
    public function testEditLesson()
    {
        $client = static::createClient();

        // Log in as admin
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Se Connecter')->form([
            'email' => 'admin@example.fr',
            'password' => 'pass_1234',
        ]);
        $client->submit($form);

        // Access the edit page for lesson with ID 13
        $crawler = $client->request('GET', '/admin/lesson/13/edit');
        $this->assertSelectorTextContains('h3', 'Modifier la Leçon');

        // Submit the form with updated data
        $form = $crawler->selectButton('Mettre à jour')->form([
            'lesson[title]' => 'Updated Lesson',
            'lesson[price]' => 30,
            'lesson[course]' => "1", // Assuming "1" is the valid course ID
        ]);
        $client->submit($form);

        // Check for redirection after form submission
        $this->assertResponseRedirects('/admin/lesson/');
        
        // Follow the redirect and verify the lesson was updated
        $client->followRedirect();
        $this->assertSelectorTextContains('table tbody', 'Updated Lesson');
    }

    // Test for deleting a lesson
    public function testDeleteLesson()
    {
        $client = static::createClient();
        
        // Log in as admin
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Se Connecter')->form([
            'email' => 'admin@example.fr',
            'password' => 'pass_1234',
        ]);
        $client->submit($form);

        // Access the lesson with ID 13
        $crawler = $client->request('GET', 'admin/lesson/13');
        $this->assertSelectorTextContains('h3', 'Updated Lesson');

        // Submit the delete form
        $deleteForm = $crawler->selectButton('Supprimer')->form();
        $client->submit($deleteForm);

        // Check for redirection after deletion
        $this->assertResponseRedirects('/admin/lesson/');
        $client->followRedirect();

        // Attempt to access the deleted lesson and check that it returns a 404
        $crawler = $client->request('GET', 'admin/lesson/13');
        $this->assertResponseStatusCodeSame(404);
    }
}
