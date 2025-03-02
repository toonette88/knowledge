<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class EmailVerifier
{
    private VerifyEmailHelperInterface $verifyEmailHelper;
    private MailerInterface $mailer;
    private EntityManagerInterface $entityManager;

    // Constructor to inject dependencies
    public function __construct(
        VerifyEmailHelperInterface $verifyEmailHelper,
        MailerInterface $mailer,
        EntityManagerInterface $entityManager
    ) {
        $this->verifyEmailHelper = $verifyEmailHelper;
        $this->mailer = $mailer;
        $this->entityManager = $entityManager;
    }

    /**
     * Send the email confirmation with a signed URL for the user to verify their email address.
     *
     * @param string $verifyEmailRouteName The name of the route that handles the verification link.
     * @param User $user The user who is requesting email verification.
     * @param TemplatedEmail $email The email to be sent, which includes the confirmation link.
     */
    public function sendEmailConfirmation(string $verifyEmailRouteName, User $user, TemplatedEmail $email): void
    {
        // Generate the signed URL for email confirmation
        $signatureComponents = $this->verifyEmailHelper->generateSignature(
            $verifyEmailRouteName,
            (string) $user->getId(),
            (string) $user->getEmail(),
            ['id' => $user->getId()]
        );

        // Add the signed URL and expiration message data to the email context
        $context = $email->getContext();
        $context['signedUrl'] = $signatureComponents->getSignedUrl();
        $context['expiresAtMessageKey'] = $signatureComponents->getExpirationMessageKey();
        $context['expiresAtMessageData'] = $signatureComponents->getExpirationMessageData();

        // Update the email context with new data
        $email->context($context);

        // Send the email using the MailerInterface
        $this->mailer->send($email);
    }

    /**
     * Handle the email confirmation process once the user clicks the verification link.
     *
     * @throws VerifyEmailExceptionInterface If the verification token is invalid or expired.
     */
    public function handleEmailConfirmation(Request $request, User $user): void
    {
        // Validate the email confirmation from the request using the helper
        $this->verifyEmailHelper->validateEmailConfirmationFromRequest(
            $request, 
            (string) $user->getId(), 
            (string) $user->getEmail()
        );

        // Set the user's account as verified
        $user->setIsVerified(true);

        // Persist the updated user entity
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
