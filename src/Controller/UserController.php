<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\AppCustomAuthenticator;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends AbstractController
{
    // Constructor to inject the EmailVerifier service
    public function __construct(private EmailVerifier $emailVerifier)
    {
    }

    // Route for registering a new user
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        Security $security,
        EntityManagerInterface $entityManager
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Hash the user's password before saving
            $plainPassword = $form->get('plainPassword')->getData();
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
    
            $entityManager->persist($user); // Save the user to the database
            $entityManager->flush();
    
            // Send an email confirmation for account verification
            $this->emailVerifier->sendEmailConfirmation(
                'app_verify_email',
                $user,
                (new TemplatedEmail())
                    ->from(new Address('no-reply@stubborn.com', 'Mail Bot')) // Sender's email
                    ->to($user->getEmail()) // Recipient's email
                    ->subject('Veuillez comfirmer votre Email') // Email subject
                    ->htmlTemplate('registration/confirmation_email.html.twig') // Template for the email
            );
    
            // Log the user in after registration and redirect to homepage
            $security->login($user, AppCustomAuthenticator::class, 'main');
            return $this->redirectToRoute('app_home'); // Redirect after login
        }
    
        // Render the registration form
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    // Route to handle the email verification link click
    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        // Ensure the user is authenticated before accessing the email verification
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // Try to validate the email confirmation link and update the user's status
        try {
            /** @var User $user */
            $user = $this->getUser();
            $this->emailVerifier->handleEmailConfirmation($request, $user); // Confirm email and set user as verified
        } catch (VerifyEmailExceptionInterface $exception) {
            // If there's an error with the verification, show an error message
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register'); // Redirect to registration if verification fails
        }

        // Add a success message and redirect after email verification
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_register'); // Redirect to registration after successful verification
    }

    // Route for logging in users
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // If the user is already logged in, redirect to the homepage
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        // Get the login error (if any) and the last entered username
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        // Render the login form with error and last username
        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    // Route for logging out users (this method is handled automatically by Symfony's firewall)
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        // This method is intentionally left blank, as logout is handled by Symfony
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
