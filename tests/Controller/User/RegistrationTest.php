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
    
        // Access the registration page
        $crawler = $client->request('GET', '/register');
    
        // Fill in the form with valid data
        $form = $crawler->selectButton('Créer un compte')->form([
            'registration_form[email]' => 'newuser@example.com',
            'registration_form[name]' => 'newuser',
            'registration_form[firstname]' => 'newuserfirstname',
            'registration_form[plainPassword][first]' => 'newpassword1234',
            'registration_form[plainPassword][second]' => 'newpassword1234',
        ]);
    
        // Submit the form
        $client->submit($form);
 
        // Follow the redirection and check the content of the home page
        $client->followRedirect();
        $this->assertSelectorTextContains('h2', 'Bienvenue');
    }
    
    // Test for failed registration with mismatched passwords
    public function testRegistrationWithMismatchedPasswords(): void
    {
        $client = static::createClient();

        // Access the registration page
        $crawler = $client->request('GET', '/register');

        // Fill in the form with mismatched passwords
        $form = $crawler->selectButton('Créer un compte')->form([
            'registration_form[email]' => 'newuser2@example.com',
            'registration_form[name]' => 'newuser',
            'registration_form[firstname]' => 'newuserfirstname',
            'registration_form[plainPassword][first]' => 'newpassword1234',
            'registration_form[plainPassword][second]' => 'wrongpassword',
        ]);

        // Submit the form
        $client->submit($form);

        // Check that an error message is displayed
        $this->assertSelectorTextContains('main li', 'Les mots de passe doivent correspondre');
    }

    // Test for failed registration with an already used email
    public function testRegistrationWithUsedEmail(): void
    {
        // Create a client to simulate a web browser request
        $client = static::createClient();

        // Request the registration page
        $crawler = $client->request('GET', '/register');

        // Fill in the form with a used email
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
        $this->assertSelectorTextContains('main li', 'Il existe déjà un compte avec cet e-mail');
    }
}
