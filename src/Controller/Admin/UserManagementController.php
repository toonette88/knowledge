<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Repository\CourseRepository;
use App\Repository\OrderRepository;
use App\Repository\OrderDetailRepository;
use App\Repository\ProgressionRepository;
use App\Repository\CertificationRepository;
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

    // Constructor dependency injection for user repository and paginator
    public function __construct(UserRepository $userRepository, PaginatorInterface $paginator)
    {
        $this->userRepository = $userRepository;
        $this->paginator = $paginator;
    }

    // List all users with pagination and search functionality
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/users', name: 'admin_users_list')]
    public function listUsers(Request $request): Response
    {
        $searchTerm = $request->query->get('search', ''); // Get the search term from the query string
        $page = $request->query->getInt('page', 1); // Current page number
        $limit = 10; // Number of items per page

        // Create query to retrieve users filtered by search term
        $query = $this->userRepository->createQueryBuilder('u')
            ->where('u.name LIKE :search')
            ->setParameter('search', '%' . $searchTerm . '%')
            ->orderBy('u.name', 'ASC')
            ->getQuery();

        // Paginate the result
        $pagination = $this->paginator->paginate($query, $page, $limit);

        // Render the user list view
        return $this->render('admin/user/user_list.html.twig', [
            'pagination' => $pagination,
            'search' => $searchTerm
        ]);
    }

    // Show user details, including orders, progressions, and certifications
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/users/{id}', name: 'admin_user_show')]
    public function showUser(
        User $user,
        OrderRepository $orderRepository,
        ProgressionRepository $progressionRepository,
        CertificationRepository $certificationRepository,
        EntityManagerInterface $entityManager,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $query = $orderRepository->findByUser($user); // Get user orders
        $orders = $paginator->paginate($query, $request->query->getInt('page', 1), 10); // Paginate orders

        // Handle user courses and progression
        $courses = [];
        $coursesWithProgression = [];
        $lessonProgressions = [];

        foreach ($orders as $order) {
            foreach ($order->getOrderDetails() as $orderDetail) {
                $course = $orderDetail->getCourse() ?? $orderDetail->getLesson()->getCourse();
                if ($course && !array_key_exists($course->getId(), $courses)) {
                    $courses[$course->getId()] = $course;
                }
            }
        }

        // Calculate the progression for each course
        foreach ($courses as $course) {
            $totalLessons = count($course->getLessons());
            $totalProgress = 0;

            foreach ($course->getLessons() as $lesson) {
                $progress = $progressionRepository->findOneBy([
                    'user' => $user,
                    'lesson' => $lesson,
                ]);
                $lessonProgressions[$lesson->getId()] = $progress ? $progress->getPercentage() : 0;
                $totalProgress += $progress ? $progress->getPercentage() : 0;
            }

            $progression = ($totalLessons > 0) ? ($totalProgress / $totalLessons) : 0;

            // If the progression reaches 100%, create a certification
            if ($progression > 99) {
                $certification = $certificationRepository->findOneBy(['user' => $user, 'course' => $course]);
                if (!$certification) {
                    $certification = new Certification();
                    $certification->setUser($user);
                    $certification->setCourse($course);
                    $certification->setDateObtained(new \DateTime());

                    $entityManager->persist($certification);
                    $entityManager->flush();
                }
            }

            $coursesWithProgression[] = [
                'course' => $course,
                'progression' => $progression,
                'lessonProgressions' => $lessonProgressions,
            ];
        }

        // Paginate the courses with progression
        $coursesPagination = $paginator->paginate($coursesWithProgression, $request->query->getInt('page', 1), 5);

        return $this->render('admin/user/user_show.html.twig', [
            'user' => $user,
            'orders' => $orders,
            'lessonProgressions' => $lessonProgressions,
            'coursesWithProgression' => $coursesPagination,
        ]);
    }

    // Delete a user
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/users/{id}/delete', name: 'admin_user_delete', methods: ['POST'])]
    public function deleteUser(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur supprimé avec succès.');

            // Redirect to user list if the user was on the show page
            $referer = $request->headers->get('referer');
            if (str_contains($referer, '/admin/users/' . $user->getId() . '/show')) {
                return $this->redirectToRoute('admin_users_list');
            }
        }

        return $this->redirectToRoute('admin_users_list');
    }

    // Display the logged-in user's profile
    #[IsGranted('ROLE_USER')]
    #[Route('/user/profile', name: 'user_profile')]
    public function profile(): Response
    {
        $user = $this->getUser(); // Get the logged-in user

        return $this->render('user/user_profile.html.twig', [
            'user' => $user,
        ]);
    }

    // Edit the logged-in user's profile
    #[IsGranted('ROLE_USER')]
    #[Route('/user/profile/edit', name: 'user_profile_edit')]
    public function editProfile(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $this->getUser(); // Get the logged-in user
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // If a new password is provided, hash it and update the user
            if ($form->get('plainPassword')->getData()) {
                $newPassword = $form->get('plainPassword')->getData();
                $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
                $user->setPassword($hashedPassword);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Your profile has been updated.');

            return $this->redirectToRoute('user_profile');
        }

        return $this->render('/user/user_edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Delete the logged-in user's profile
    #[IsGranted('ROLE_USER')]
    #[Route('/user/profile/delete', name: 'user_profile_delete', methods: ['POST'])]
    public function deleteProfile(Request $request, EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage, SessionInterface $session): Response
    {
        $user = $this->getUser(); // Get the logged-in user
    
        // Verify CSRF token
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            // Delete the user
            $entityManager->remove($user);
            $entityManager->flush();
    
            // Log the user out
            $tokenStorage->setToken(null);
            $session->invalidate();
    
            $this->addFlash('success', 'Your account has been deleted.');
    
            // Redirect to the home page
            return $this->redirectToRoute('app_home');
        }
    
        // In case of failure, redirect to the profile page
        return $this->redirectToRoute('user_profile');
    }
}
