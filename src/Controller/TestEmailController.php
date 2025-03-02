<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class TestEmailController extends AbstractController
{
    // Sends a test email using Mailtrap and Symfony Mailer
    public function sendTestEmail(MailerInterface $mailer): Response
    {
        // Create a new email instance
        $email = (new Email())
            ->from('test@example.com') // Sender email address
            ->to('recipient@example.com') // Recipient email address
            ->subject('Test Email via Mailtrap') // Subject of the email
            ->text('Ceci est un test d\'envoi d\'email avec Mailtrap et Symfony Mailer.') // Plain text version of the email
            ->html('<p>Ceci est un test d\'envoi d\'email avec <strong>Mailtrap</strong> et Symfony Mailer.</p>'); // HTML version of the email

        // Send the email using Symfony Mailer
        $mailer->send($email);

        // Return a response indicating that the email was sent successfully
        return new Response('Email sent successfully! Check your Mailtrap inbox.');
    }
}
