<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class UserManagementController extends AbstractController
{

    private UserRepository $userRepository;
    private PaginatorInterface $paginator;

    // Injection des dépendances via le constructeur
    public function __construct(UserRepository $userRepository, PaginatorInterface $paginator)
    {
        $this->userRepository = $userRepository;
        $this->paginator = $paginator;
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/users', name: 'admin_users_list')]
    
    public function listUsers(Request $request): Response
    {
        // Récupérer le terme de recherche
        $searchTerm = $request->query->get('search', '');
        
        // Nombre d'éléments par page
        $page = $request->query->getInt('page', 1);
        $limit = 10;

        // Construire la requête pour récupérer les utilisateurs filtrés par nom
        $query = $this->userRepository->createQueryBuilder('u')
            ->where('u.name LIKE :search')
            ->setParameter('search', '%' . $searchTerm . '%')
            ->orderBy('u.name', 'ASC')
            ->getQuery();

        // Paginer les résultats
        $pagination = $this->paginator->paginate(
            $query,  // La requête
            $page,   // La page actuelle
            $limit   // Limite par page
        );

        return $this->render('admin/user/user_list.html.twig', [
            'pagination' => $pagination,
            'search' => $searchTerm
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/users/{id}', name: 'admin_user_show')]
    public function showUser(User $user): Response
    {
        return $this->render('admin/user/user_show.html.twig', [
            'user' => $user,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/users/{id}/delete', name: 'admin_user_delete', methods: ['POST'])]
    public function deleteUser(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur supprimé avec succès.');

            // Déterminer la redirection en fonction de la page d'origine
            $referer = $request->headers->get('referer');
            if (str_contains($referer, '/admin/users/' . $user->getId() . '/show')) {
                return $this->redirectToRoute('admin_users_list');
            }
        }

        return $this->redirectToRoute('admin_users_list');
    }


    #[IsGranted('ROLE_USER')]
    #[Route('/user/profile', name: 'user_profile')]
    public function profile(): Response
    {
        $user = $this->getUser(); // L'utilisateur connecté

        return $this->render('user/user_profile.html.twig', [
            'user' => $user,
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/user/profile/edit', name: 'user_profile_edit')]
    public function editProfile(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $this->getUser(); // Récupère l'utilisateur connecté
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Si un nouveau mot de passe a été saisi, on le hache et on met à jour l'utilisateur
            if ($form->get('plainPassword')->getData()) {
                $newPassword = $form->get('plainPassword')->getData();
                $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
                $user->setPassword($hashedPassword);
            }

            // Sauvegarder l'utilisateur avec le mot de passe mis à jour
            $entityManager->flush();

            // Ajouter un message flash de succès
            $this->addFlash('success', 'Votre profil a été mis à jour.');

            return $this->redirectToRoute('user_profile'); // Rediriger vers le profil
        }

        return $this->render('/user/user_edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/user/profile/delete', name: 'user_profile_delete', methods: ['POST'])]
    public function deleteProfile(Request $request, EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage, SessionInterface $session): Response
    {
        $user = $this->getUser();
    
        // Vérification du CSRF Token
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            // Suppression de l'utilisateur
            $entityManager->remove($user);
            $entityManager->flush();
    
            // Déconnexion de l'utilisateur
            $tokenStorage->setToken(null);  // Retirer le token de sécurité
            $session->invalidate();          // Invalider la session
    
            $this->addFlash('success', 'Votre compte a été supprimé.');
    
            // Redirection vers la page d'accueil
            return $this->redirectToRoute('app_home');
        }
    
        // En cas d'échec de la suppression, redirige vers la page de profil
        return $this->redirectToRoute('user_profile');
    }
    




}
