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
        $form = $crawler->selectButton('Se Connecter')->form([  // "Se Connecter" means "Log In" in French
            'email' => 'admin@example.fr', 
            'password' => 'pass_1234',    
        ]);
        $client->submit($form);

        // Access the users list page
        $client->request('GET', '/admin/users');

        // Assert the response is successful
        $this->assertResponseIsSuccessful();
        // Assert the presence of a table element in the page
        $this->assertSelectorExists('table');
        // Assert the heading contains 'Liste des utilisateurs' which means 'Users List'
        $this->assertSelectorTextContains('h2', 'Liste des utilisateurs');
    }

    public function testAdminCanShowUser(): void
    {
        $client = static::createClient();

        // Log in as admin
        $crawler = $client->request('GET', '/login');

        // Submit the login form with valid credentials
        $form = $crawler->selectButton('Se Connecter')->form([ 
            'email' => 'admin@example.fr', // A valid user from your fixtures
            'password' => 'pass_1234',   // The valid password corresponding to the user
        ]);
        $client->submit($form);

        // Assert the admin is redirected to the dashboard page after logging in
        $this->assertResponseRedirects('/admin/dashboard');
        $client->followRedirect();

        // Access a specific user page
        $crawler = $client->request('GET', '/admin/users/7');

        // Assert the page contains the username 'user6'
        $this->assertSelectorTextContains('h5', 'user6');
    }

    public function testAdminCanDeleteUser(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
    
        // Submit login credentials
        $form = $crawler->selectButton('Se Connecter')->form([  
            'email' => 'admin@example.fr', 
            'password' => 'pass_1234',
        ]);
        $client->submit($form);
    
        // Access a specific user's page
        $crawler = $client->request('GET', '/admin/users/8');
    
        // Get the delete form associated with the user
        $form = $crawler->filter('form[action$="/admin/users/8/delete"]')->form();
    
        // Submit the form to delete the user
        $client->submit($form);
    
        // Assert the user is redirected to the users list after deletion
        $this->assertResponseRedirects('/admin/users');
        $client->followRedirect();
    
        // Assert that a success message is displayed indicating the user was deleted
        $this->assertSelectorTextContains('.alert-success', 'Utilisateur supprimé avec succès.'); // "User deleted successfully"
    }

    public function testNonAdminCannotAccessAdminRoutes(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');

        // Submit valid login credentials for a non-admin user
        $form = $crawler->selectButton('Se Connecter')->form([ 
            'email' => 'user4@example.fr', // A non-admin user from your fixtures
            'password' => 'pass_1234',   // The valid password for the non-admin user
        ]);
        $client->submit($form);

        // Try accessing an admin-only page
        $crawler = $client->request('GET', '/admin/users');

        // Assert that a 403 Forbidden status code is returned
        $this->assertResponseStatusCodeSame(403);
        // OR, if redirected to login page
        // $this->assertResponseRedirects('/login'); 

        // Assert that the "Delete" button is not visible to non-admin users
        $this->assertSelectorNotExists('a[href*="admin_user_delete"]');
    }
}
