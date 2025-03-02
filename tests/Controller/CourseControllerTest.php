<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CourseControllerTest extends WebTestCase
{
    // Test for creating a course
    public function testCreateCourse()
    {
        $client = static::createClient();
        
        // Log in as admin
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Se Connecter')->form([
            'email' => 'admin@example.fr',
            'password' => 'pass_1234',
        ]);
        $client->submit($form);
        
        // Access the create course page
        $crawler = $client->request('GET', 'admin/course/new');
        
        // Check that the page loads successfully
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h3', 'Créer un nouveau Cursus');

        // Submit the course creation form
        $form = $crawler->selectButton('Enregistrer')->form([
            'course[title]' => 'Test Course',
            'course[description]' => 'Description of test course',
            'course[price]' => 100,
        ]);
        $client->submit($form);
        
        // Check if it redirects to the course list
        $this->assertResponseRedirects('/admin/course');
        
        // Follow the redirect and verify the course was created
        $client->followRedirect();
        $this->assertSelectorTextContains('table tbody', 'Test Course');
    }

    // Test for editing a course
    public function testEditCourse()
    {
        $client = static::createClient();
        
        // Log in as admin
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Se Connecter')->form([
            'email' => 'admin@example.fr',
            'password' => 'pass_1234',
        ]);
        $client->submit($form);

        // Access the course edit page (ensure the ID is correct)
        $crawler = $client->request('GET', '/admin/course/7/edit');
        $this->assertSelectorTextContains('h3', 'Modifier le Cursus');

        // Submit the course edit form
        $form = $crawler->selectButton('Mettre à jour')->form([
            'course[title]' => 'Updated Course',
            'course[description]' => 'Updated description',
            'course[price]' => 120
        ]);
        $client->submit($form);

        // Check for redirection after update
        $this->assertResponseRedirects('/admin/course');
        
        // Follow the redirect and ensure the course is updated
        $client->followRedirect();
        $this->assertSelectorTextContains('table tbody', 'Updated Course');
    }

    // Test for deleting a course
    public function testDeleteCourse()
    {
        $client = static::createClient();
        
        // Log in as admin
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Se Connecter')->form([
            'email' => 'admin@example.fr',
            'password' => 'pass_1234',
        ]);
        $client->submit($form);

        // Access the course edit page for deletion (ensure the ID is correct)
        $crawler = $client->request('GET', 'admin/course/7/edit');
        $this->assertSelectorTextContains('h3', 'Modifier le Cursus');

        // Submit the delete form
        $deleteForm = $crawler->selectButton('Supprimer')->form();
        $client->submit($deleteForm);

        // Check for redirection after deletion
        $this->assertResponseRedirects('/admin/course');
        $client->followRedirect();
        
        // Attempt to access the deleted course's edit page (ID 7)
        $crawler = $client->request('GET', 'admin/course/7/edit');
        
        // Check that a 404 error is returned because the course no longer exists
        $this->assertResponseStatusCodeSame(404);
    }
}
