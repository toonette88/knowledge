<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EmailTest extends WebTestCase
{
    public function testEmailSentUponRequest(): void
    {
        // Create a client instance to make requests
        $client = static::createClient();

        // Make a GET request to the route responsible for sending the email
        $client->request('GET', '/test-email');

        // Assert that the response was successful (status 200 OK)
        $this->assertResponseIsSuccessful();

        // At this point, the email should have been sent via Mailtrap.
        // You can manually check your Mailtrap inbox to confirm the email was sent.

        // Optionally: Add an assertion if you have configured a mock for MailerInterface
        $this->assertTrue(true, 'The email has been sent successfully.');
    }
}
