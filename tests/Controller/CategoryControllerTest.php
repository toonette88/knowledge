<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CategoryControllerTest extends WebTestCase
{
    public function testCreateCategory()
{
    $client = static::createClient();
    $crawler = $client->request('GET', '/login');
    
    $form = $crawler->selectButton('Se Connecter')->form([
        'email' => 'admin@example.fr',
        'password' => 'pass_1234',
    ]);
    $client->submit($form);

    // Accède à la page de création de la catégorie
    $crawler = $client->request('GET', 'admin/category/new');

    // Vérifie que la page de création est bien chargée
    $this->assertResponseIsSuccessful();
    $this->assertSelectorTextContains('h3', 'Créer une nouvelle catégorie');

    // Soumet le formulaire de création
    $form = $crawler->selectButton('Créer la catégorie')->form([
        'category[name]' => 'Test Category',
    ]);
    $client->submit($form);

    // Vérifie que la redirection a bien eu lieu vers '/admin/category'
    $this->assertResponseRedirects('/admin/category');

    // Simule la soumission de la page et vérifie que la catégorie a bien été ajoutée
    $client->followRedirect();

    // Vérifie que la catégorie "Test Category" est bien présente dans la liste
    $this->assertSelectorTextContains('table tbody', 'Test Category');
}


    public function testEditCategory()
    {
        $client = static::createClient();
        
        $crawler = $client->request('GET', '/login');
        
        $form = $crawler->selectButton('Se Connecter')->form([
            'email' => 'admin@example.fr',
            'password' => 'pass_1234',
        ]);
        $client->submit($form);
        
        // Accède à la page d'édition de la catégorie
        $crawler = $client->request('GET', 'admin/category/5/edit');  // Assure-toi que l'ID est correct ici

        // Soumet les modifications du formulaire
        $form = $crawler->selectButton('Mettre à jour')->form([
            'category[name]' => 'Updated Category',
        ]);
        $client->submit($form);

        // Vérifie la redirection
        $this->assertResponseRedirects('/admin/category');
        $client->followRedirect();

        // Vérifie que la catégorie a bien été mise à jour
        $this->assertSelectorTextContains('table tbody', 'Updated Category');
    }


    public function testDeleteCategory()
    {
        $client = static::createClient();
        
        // Connexion en tant qu'administrateur
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Se Connecter')->form([
            'email' => 'admin@example.fr',
            'password' => 'pass_1234',
        ]);
        $client->submit($form);
        
        // Accède à la page de la liste des catégories
        $crawler = $client->request('GET', '/admin/category');
        
        // Vérifie que la catégorie "Updated Category" existe dans la table avant suppression
        $this->assertSelectorTextContains('table tbody', 'Updated Category');
        
        // Accède à la page de suppression de la catégorie avec l'ID 5
        $crawler = $client->request('GET', '/admin/category/5/edit');

        $this->assertSelectorTextContains('h3', 'Modifier la Catégorie');
        
        // Soumet le formulaire de suppression
        $deleteForm = $crawler->selectButton('Supprimer')->form();
        $client->submit($deleteForm);
        
        // Vérifie la redirection après suppression
        $this->assertResponseRedirects('/admin/category');
        $client->followRedirect();
        
        // Essaye d'accéder à la page de la catégorie supprimée (ID 5)
        $client->request('GET', '/admin/category/5/edit');
        
        // Vérifie qu'un 404 est retourné car la catégorie n'existe plus
        $this->assertResponseStatusCodeSame(404);
    }

    
       
}
