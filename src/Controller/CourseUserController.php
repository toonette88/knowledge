<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\CourseRepository;
use App\Repository\OrderRepository;
use App\Repository\OrderDetailRepository;
use App\Repository\ProgressionRepository;
use App\Repository\CertificationRepository;
use App\ENtity\Certification;
use App\Entity\Course;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CourseUserController extends AbstractController
{
    private OrderRepository $orderRepository;
    private OrderDetailRepository $orderDetailRepository;
    private ProgressionRepository $progressionRepository;

    public function __construct(OrderRepository $orderRepository, OrderDetailRepository $orderDetailRepository, ProgressionRepository $progressionRepository)
    {
        $this->orderRepository = $orderRepository;
        $this->orderDetailRepository = $orderDetailRepository;
        $this->progressionRepository = $progressionRepository;
    }

    #[Route('/catalog', name: 'catalog')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAllWithCourses();

        return $this->render('catalog/catalog.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/course/{id}', name:'course_show')]
    public function show(Course $course): Response
    {
        $user = $this->getUser();

        $hasPurchasedCourse = false;
        $hasPurchasedLesson = false;

        if ($user) {
            $orders = $this->orderRepository->findBy(['user' => $user, 'status' => 'Payée']);
            foreach ($orders as $order) {
                foreach ($order->getOrderDetails() as $orderDetail) {
                    // Vérifier si le cursus complet a été acheté
                    if ($orderDetail->getCourse() === $course) {
                        $hasPurchasedCourse = true;
                    }

                    // Vérifier si une leçon spécifique a été achetée
                    if ($orderDetail->getLesson() && $orderDetail->getLesson()->getCourse() === $course) {
                        $hasPurchasedLesson = true;
                    }
                }
            }
        }

        return $this->render('catalog/course_show.html.twig', [
            'course' => $course,
            'hasPurchasedCourse' => $hasPurchasedCourse,
            'hasPurchasedLesson' => $hasPurchasedLesson,
        ]);
    }

    #[Route('/profile/courses', name: 'profile_course')]
    public function showCourses(
        OrderRepository $orderRepository,
        ProgressionRepository $progressionRepository,
        CertificationRepository $certificationRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $user = $this->getUser();
    
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour voir vos cursus.');
        }
    
        // Récupérer toutes les commandes payées de l'utilisateur
        $orders = $orderRepository->findBy(['user' => $user, 'status' => 'Payée']);
    
        $courses = [];
        $coursesWithProgression = [];
    
        foreach ($orders as $order) {
            foreach ($order->getOrderDetails() as $orderDetail) {
                $course = $orderDetail->getCourse() ?? $orderDetail->getLesson()->getCourse();
    
                // Vérifier si le cursus est déjà ajouté (éviter les doublons)
                if ($course && !array_key_exists($course->getId(), $courses)) {
                    $courses[$course->getId()] = $course;
                }
            }
        }
    
        // Calculer la progression pour chaque cursus
        foreach ($courses as $course) {
            $totalLessons = count($course->getLessons());
            $totalProgress = 0;
    
            foreach ($course->getLessons() as $lesson) {
                $progress = $progressionRepository->findOneBy([
                    'user' => $user,
                    'lesson' => $lesson,
                ]);
    
                // Ajouter la progression réelle de la leçon (0% si aucune progression)
                $totalProgress += $progress ? $progress->getPercentage() : 0;
            }
    
            // Calcul de la moyenne de progression
            $progression = ($totalLessons > 0) ? ($totalProgress / $totalLessons) : 0;
    
            // Vérifier si la progression atteint 100% et créer la certification si nécessaire
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
            ];
        }
    
        return $this->render('user/courses_overview.html.twig', [
            'coursesWithProgression' => $coursesWithProgression,
        ]);
    }
    
    
    
    #[Route('/course/{id}/details', name: 'course_detail')]
    public function courseDetail(
        Course $course, 
        ProgressionRepository $progressionRepository,
        CertificationRepository $certificationRepository
    ): Response {
        $user = $this->getUser();
    
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour voir les détails du cursus.');
        }
    
        // Récupérer toutes les leçons du cursus
        $lessons = $course->getLessons();
        $lessonProgressions = [];
    
        // Calculer la progression de chaque leçon pour cet utilisateur
        foreach ($lessons as $lesson) {
            $progression = $progressionRepository->findOneBy([
                'user' => $user,
                'lesson' => $lesson,
            ]);
    
            // Stocker la progression de chaque leçon
            $lessonProgressions[$lesson->getId()] = $progression ? $progression->getPercentage() : 0;
        }
    
        // Vérifier si l'utilisateur a obtenu une certification pour ce cursus
        $certification = $certificationRepository->findOneBy([
            'user' => $user,
            'course' => $course
        ]);
    
        // Passer les données au template
        return $this->render('user/course_detail.html.twig', [
            'course' => $course,
            'lessonProgressions' => $lessonProgressions,
            'certification' => $certification,
        ]);
    }
    
    

}
