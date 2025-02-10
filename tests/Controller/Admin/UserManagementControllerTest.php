<?php

namespace App\Tests\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
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

    public function testAdminCanShowUser(): void
    {
        $client = static::createClient();

        // Connecter l'admin   
        $crawler = $client->request('GET', '/login');

        // Soumettre le formulaire avec des informations valides
        $form = $crawler->selectButton('Se Connecter')->form([
            'email' => 'admin@example.fr', // Un utilisateur valide depuis vos fixtures
            'password' => 'pass_1234',   // Le mot de passe valide correspondant
        ]);
        $client->submit($form);

        $this->assertResponseRedirects('/admin/dashboard');  // ou la page d'accueil de l'admin
        $client->followRedirect();

        // Accéder à la page d'un utilisateur
        $crawler = $client->request('GET', '/admin/users/7');


        $this->assertSelectorTextContains('h5', 'user6');
    }

    public function testAdminCanDeleteUser(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
    
        $form = $crawler->selectButton('Se Connecter')->form([
            'email' => 'admin@example.fr',
            'password' => 'pass_1234',
        ]);
        $client->submit($form);
    
        // Accéder à la page d'un utilisateur
        $crawler = $client->request('GET', '/admin/users/8');
    
        // Récupérer le formulaire de suppression
        $form = $crawler->filter('form[action$="/admin/users/8/delete"]')->form();
    
        // Soumettre la suppression
        $client->submit($form);
    
        // Vérifier la redirection après suppression
        $this->assertResponseRedirects('/admin/users');
        $client->followRedirect();
    
        // Vérifier qu'un message de succès est affiché
        $this->assertSelectorTextContains('.alert-success', 'Utilisateur supprimé avec succès.');
    }
    
    

    public function testNonAdminCannotAccessAdminRoutes(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');

        // Soumettre le formulaire avec des informations valides
        $form = $crawler->selectButton('Se Connecter')->form([
            'email' => 'user4@example.fr', // Un utilisateur valide depuis vos fixtures
            'password' => 'pass_1234',   // Le mot de passe valide correspondant
        ]);
        $client->submit($form);

        // Accéder à une page réservée aux admins
        $crawler = $client->request('GET', '/admin/users');

        // Vérifier que l'utilisateur est bien redirigé vers la page de connexion ou une erreur 403
        $this->assertResponseStatusCodeSame(403); // Si accès interdit
        // OU
        // $this->assertResponseRedirects('/login'); // Si redirigé

        // Vérifier que le bouton "Supprimer" n'est pas affiché
        $this->assertSelectorNotExists('a[href*="admin_user_delete"]');
    }
     
}
