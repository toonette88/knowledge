<?php

namespace App\Tests\Controller\User;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationTest extends WebTestCase
{
    // Test to ensure the registration page displays correctly
    public function testRegistrationPageDisplay(): void
    {
        // Create a client to simulate a web browser request
        $client = static::createClient();

        // Request the registration page
        $crawler = $client->request('GET', '/register');

        // Check that the response is successful (HTTP 200)
        $this->assertResponseIsSuccessful();

        // Verify that the page contains the text "S'inscrire" in an <h2> tag (this is the title of the registration page)
        $this->assertSelectorTextContains('h2', 'S\'inscrire');

        // Ensure that the registration form contains input fields for "email", "name", "firstname", "password", and "confirm_password"
        $this->assertCount(1, $crawler->filter('input[name="registration_form[email]"]'));
        $this->assertCount(1, $crawler->filter('input[name="registration_form[name]"]'));
        $this->assertCount(1, $crawler->filter('input[name="registration_form[firstname]"]'));
        $this->assertCount(1, $crawler->filter('input[name="registration_form[plainPassword][first]"]'));
        $this->assertCount(1, $crawler->filter('input[name="registration_form[plainPassword][second]"]'));

        // Ensure that the page contains a submit button for the form
        $this->assertCount(1, $crawler->filter('button[type="submit"]'));
    }

    // Test for successful registration with valid credentials
    public function testRegistrationWithValidCredentials(): void
    {
        $client = static::createClient();
    
        // Accéder à la page d'inscription
        $crawler = $client->request('GET', '/register');
    
        // Remplir le formulaire avec des données valides
        $form = $crawler->selectButton('Créer un compte')->form([
            'registration_form[email]' => 'newuser@example.com',
            'registration_form[name]' => 'newuser',
            'registration_form[firstname]' => 'newuserfirstname',
            'registration_form[plainPassword][first]' => 'newpassword1234',
            'registration_form[plainPassword][second]' => 'newpassword1234',
        ]);
    
        // Soumettre le formulaire
        $client->submit($form);
 
    
        // Suivre la redirection et vérifier le contenu de la page d'accueil
        $client->followRedirect();
        $this->assertSelectorTextContains('h2', 'Page authentifiée');
    }
    

    // Test for failed registration with mismatched passwords
    public function testRegistrationWithMismatchedPasswords(): void
    {
        $client = static::createClient();

        // Accéder à la page d'inscription
        $crawler = $client->request('GET', '/register');

        // Remplir le formulaire avec des mots de passe différents
        $form = $crawler->selectButton('Créer un compte')->form([
            'registration_form[email]' => 'newuser2@example.com',
            'registration_form[name]' => 'newuser',
            'registration_form[firstname]' => 'newuserfirstname',
            'registration_form[plainPassword][first]' => 'newpassword1234',
            'registration_form[plainPassword][second]' => 'wrongpassword',
        ]);

        // Soumettre le formulaire
        $client->submit($form);

        // Vérifier qu'un message d'erreur est affiché
        $this->assertSelectorTextContains('li', 'Les mots de passe doivent correspondre');
    }

    // Test for failed registration with an already used email
    public function testRegistrationWithUsedEmail(): void
    {
        // Create a client to simulate a web browser request
        $client = static::createClient();

        // Request the registration page
        $crawler = $client->request('GET', '/register');

        // Fill in the form with the used email
        $form = $crawler->selectButton('Créer un compte')->form([
            'registration_form[email]' => 'user2@example.fr',  // Used email
            'registration_form[name]' => 'newuser',
            'registration_form[firstname]' => 'newuserfirstname',
            'registration_form[plainPassword][first]' => 'newpassword1234',
            'registration_form[plainPassword][second]' => 'newpassword1234',
        ]);
        
        // Submit the form
        $client->submit($form);

        // Assert that the response is not a redirection
        $this->assertSelectorTextContains('h2', 'S\'inscrire'); // Ensure we stay on the registration page

        // Assert that the error message is shown
        $this->assertSelectorTextContains('li', 'Il existe déjà un compte avec cet e-mail');
    }


}

