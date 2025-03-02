<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class AppCustomAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login'; // The route name for login page

    // Dependency injection of UrlGeneratorInterface to generate URLs
    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
    }

    /**
     * The authenticate method is called when the user submits the login form.
     * It retrieves the login form data and creates a Passport object for the authentication process.
     */
    public function authenticate(Request $request): Passport
    {
        // Get the email and password from the request
        $username = $request->request->get('email');
        $password = $request->request->get('password');
        $csrfToken = $request->request->get('_csrf_token');

        // Store the username in the session for the "last username" feature
        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $username);

        // Create and return the Passport with the credentials and CSRF token
        return new Passport(
            new UserBadge($username),  // User identifier (email in this case)
            new PasswordCredentials($password),  // User's password
            [
                new CsrfTokenBadge('authenticate', $csrfToken),  // CSRF token check
            ]
        );
    }

    /**
     * This method is called when authentication is successful.
     * Based on the user's role, it redirects them to either the admin dashboard or home page.
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // Check if the authenticated user has the 'ROLE_ADMIN' role
        if ($token->getUser()->hasRole('ROLE_ADMIN')) {
            // Redirect to the admin dashboard if the user is an admin
            return new RedirectResponse($this->urlGenerator->generate('admin_dashboard'));
        }

        // Otherwise, redirect to the home page for regular users
        return new RedirectResponse($this->urlGenerator->generate('app_home'));
    }

    /**
     * Returns the login URL.
     * This method is called when the user is redirected to the login page.
     */
    protected function getLoginUrl(Request $request): string
    {
        // Generate and return the login page URL
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
