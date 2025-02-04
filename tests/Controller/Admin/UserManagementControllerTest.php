<?php

namespace App\Tests\Controller\Admin;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserManagementControllerTest extends WebTestCase
{
    public function testAdminCanAccessUsersList(): void
    {
        $client = static::createClient();

        // Log in as admin
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Se Connecter')->form([
            'email' => 'admin@example.fr', 
            'password' => 'pass_1234',    
        ]);
        $client->submit($form);

        // Access the users list
        $client->request('GET', '/admin/users');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('table');
        $this->assertSelectorTextContains('h2', 'Liste des utilisateurs');
    }

    public function testAdminCanEditUser(): void
    {
        $client = static::createClient();

        // Connecter l'admin
        $client->request('POST', '/login', [
            'email' => 'admin@example.fr',
            'password' => 'pass_1234',
        ]);

        // Accéder au formulaire d'édition
        $crawler = $client->request('GET', '/admin/users/1/edit');

        $this->assertResponseIsSuccessful();

        // Soumettre le formulaire d'édition
        $form = $crawler->selectButton('Modifier')->form([
            'user[name]' => 'UpdatedUser',
            'user[email]' => 'updateduser@example.fr',
        ]);

        $client->submit($form);

        $this->assertResponseRedirects('/admin/users'); // Redirection après succès
        $client->followRedirect();

        $this->assertSelectorTextContains('.alert-success', 'Utilisateur mis à jour avec succès.');
    }

    public function testAdminCanDeleteUser(): void
    {
        $client = static::createClient();

        // Connecter l'admin
        $client->request('POST', '/login', [
            'email' => 'admin@example.fr',
            'password' => 'pass_1234',
        ]);

        // Soumettre la suppression
        $crawler = $client->request('GET', '/admin/users');
        $deleteForm = $crawler->selectButton('Supprimer')->form([
            '_token' => $crawler->filter('input[name="_token"]')->attr('value'),
        ]);

        $client->submit($deleteForm);

        $this->assertResponseRedirects('/admin/users');
        $client->followRedirect();

        $this->assertSelectorTextContains('.alert-success', 'Utilisateur supprimé avec succès.');
    }

    public function testNonAdminCannotAccessAdminRoutes(): void
    {
        $client = static::createClient();

        // Log in as regular user
        $client->request('POST', '/login', [
            'email' => 'user1@example.fr',
            'password' => 'pass_1234',
        ]);

        // Attempt to access admin route
        $client->request('GET', '/admin/users');

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    
}
