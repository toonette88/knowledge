<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class TestEmailController extends AbstractController
{
    public function sendTestEmail(MailerInterface $mailer): Response
    {
        $email = (new Email())
            ->from('test@example.com')
            ->to('recipient@example.com')
            ->subject('Test Email via Mailtrap')
            ->text('Ceci est un test d\'envoi d\'email avec Mailtrap et Symfony Mailer.')
            ->html('<p>Ceci est un test d\'envoi d\'email avec <strong>Mailtrap</strong> et Symfony Mailer.</p>');

        $mailer->send($email);

        return new Response('Email envoyé avec succès ! Vérifiez votre boîte Mailtrap.');
    }
}
