<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CategoryControllerTest extends WebTestCase
{
    // Test for creating a category
    public function testCreateCategory()
    {
        $client = static::createClient();
        
        // Log in as admin
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Se Connecter')->form([
            'email' => 'admin@example.fr',
            'password' => 'pass_1234',
        ]);
        $client->submit($form);

        // Access the category creation page
        $crawler = $client->request('GET', 'admin/category/new');

        // Ensure the creation page loads successfully
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h3', 'Créer une nouvelle catégorie');

        // Submit the category creation form
        $form = $crawler->selectButton('Enregistrer')->form([
            'category[name]' => 'Test Category',
        ]);
        $client->submit($form);

        // Check if redirection happens to the category list
        $this->assertResponseRedirects('/admin/category');

        // Follow the redirection and ensure the category appears in the list
        $client->followRedirect();
        $this->assertSelectorTextContains('table tbody', 'Test Category');
    }

    // Test for editing a category
    public function testEditCategory()
    {
        $client = static::createClient();
        
        // Log in as admin
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Se Connecter')->form([
            'email' => 'admin@example.fr',
            'password' => 'pass_1234',
        ]);
        $client->submit($form);
        
        // Access the category edit page (ensure the ID is correct)
        $crawler = $client->request('GET', 'admin/category/5/edit');  

        // Submit the category update form
        $form = $crawler->selectButton('Enregistrer')->form([
            'category[name]' => 'Updated Category',
        ]);
        $client->submit($form);

        // Check for redirection after update
        $this->assertResponseRedirects('/admin/category');
        $client->followRedirect();

        // Ensure the category was successfully updated
        $this->assertSelectorTextContains('table tbody tr:nth-child(5) td', 'Updated Category');
    }

    // Test for deleting a category
    public function testDeleteCategory()
    {
        $client = static::createClient();
        
        // Log in as admin
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Se Connecter')->form([
            'email' => 'admin@example.fr',
            'password' => 'pass_1234',
        ]);
        $client->submit($form);
        
        // Access the category list page
        $crawler = $client->request('GET', '/admin/category');
        
        // Check that the category "Updated Category" exists in the table before deletion
        $this->assertSelectorTextContains('table tbody tr:nth-child(5) td', 'Updated Category');
        
        // Access the category edit page for deletion (ID 5)
        $crawler = $client->request('GET', '/admin/category/5/edit');
        $this->assertSelectorTextContains('h3', 'Modifier la Catégorie');
        
        // Submit the delete form
        $deleteForm = $crawler->selectButton('Supprimer')->form();
        $client->submit($deleteForm);
        
        // Check for redirection after deletion
        $this->assertResponseRedirects('/admin/category');
        $client->followRedirect();
        
        // Attempt to access the deleted category's edit page (ID 5)
        $client->request('GET', '/admin/category/5/edit');
        
        // Check that a 404 is returned because the category no longer exists
        $this->assertResponseStatusCodeSame(404);
    }
}
