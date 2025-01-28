<?php

namespace App\Tests\Controller\User;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginTest extends WebTestCase
{
    // Test to ensure the login page displays correctly
    public function testLoginPageDisplay(): void
    {
        // Create a client to simulate a web browser request
        $client = static::createClient();

        // Request the login page
        $crawler = $client->request('GET', '/login');

        // Check that the response is successful (HTTP 200)
        $this->assertResponseIsSuccessful();

        // Verify that the page contains the text "Se connecter" in an <h2> tag (this is the title of the login page)
        $this->assertSelectorTextContains('h2', 'Se connecter');

        // Ensure that the login form contains input fields for "email" and "password"
        $this->assertCount(1, $crawler->filter('input[name="email"]'));
        $this->assertCount(1, $crawler->filter('input[name="password"]'));

        // Ensure that the page contains a submit button for the form
        $this->assertCount(1, $crawler->filter('button[type="submit"]'));
    }

    // Test for successful login with valid credentials
    public function testLoginWithValidCredentials(): void
    {
        // Create a client to simulate a web browser request
        $client = static::createClient();

        // Request the login page
        $crawler = $client->request('GET', '/login');

        // Fill in the form with valid credentials (use valid data from your fixtures)
        $form = $crawler->selectButton('Se Connecter')->form([
            'email' => 'user1@example.fr', // Valid email of a user from your fixtures
            'password' => 'pass_1234',     // Valid password corresponding to the user
        ]);
        // Submit the form
        $client->submit($form);

        // Check that the user is redirected to the homepage after successful login
        $this->assertResponseRedirects('/');

        // Follow the redirect and check that the authenticated page contains the text "Page authentifiée"
        $client->followRedirect();
        $this->assertSelectorTextContains('h2', 'Page authentifiée');
    }

    // Test for failed login with invalid credentials
    public function testLoginWithInvalidCredentials(): void
    {
        // Create a client to simulate a web browser request
        $client = static::createClient();

        // Request the login page
        $crawler = $client->request('GET', '/login');

        // Fill in the form with invalid credentials (wrong email and password)
        $form = $crawler->selectButton('Se Connecter')->form([
            'email' => 'invalid',         // Invalid email
            'password' => 'wrongpassword', // Invalid password
        ]);
        // Submit the form
        $client->submit($form);

        // Follow the redirection after submitting the form
        $client->followRedirect();

        // Assert that the response is successful (HTTP 200)
        $this->assertResponseIsSuccessful();

        // Assert that an error message is displayed indicating invalid credentials
        $this->assertSelectorTextContains('.alert-danger', 'Identifiants invalides.');
    }
}
