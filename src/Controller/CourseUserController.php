<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\CourseRepository;
use App\Repository\OrderRepository;
use App\Repository\OrderDetailRepository;
use App\Repository\ProgressionRepository;
use App\Repository\CertificationRepository;
use App\Entity\Certification;
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

    // Constructor injection to handle dependencies for the repositories
    public function __construct(OrderRepository $orderRepository, OrderDetailRepository $orderDetailRepository, ProgressionRepository $progressionRepository)
    {
        $this->orderRepository = $orderRepository;
        $this->orderDetailRepository = $orderDetailRepository;
        $this->progressionRepository = $progressionRepository;
    }

    // Route to display the catalog of courses, grouped by categories
    #[Route('/catalog', name: 'catalog')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        // Fetch all categories with their associated courses
        $categories = $categoryRepository->findAllWithCourses();

        // Render the catalog view with the categories and their courses
        return $this->render('catalog/catalog.html.twig', [
            'categories' => $categories,
        ]);
    }

    // Route to display a specific course and whether the user has purchased it or any of its lessons
    #[Route('/course/{id}', name:'course_show')]
    public function show(Course $course): Response
    {
        $user = $this->getUser(); // Get the currently logged-in user

        // Initialize flags to check if the course or any of its lessons are purchased
        $hasPurchasedCourse = false;
        $hasPurchasedLesson = false;

        if ($user) {
            // Fetch orders that have been paid for the current user
            $orders = $this->orderRepository->findBy(['user' => $user, 'status' => 'Payée']);
            
            // Loop through the orders and check if the course or any lesson within it is purchased
            foreach ($orders as $order) {
                foreach ($order->getOrderDetails() as $orderDetail) {
                    // Check if the entire course is purchased
                    if ($orderDetail->getCourse() === $course) {
                        $hasPurchasedCourse = true;
                    }

                    // Check if a specific lesson is purchased
                    if ($orderDetail->getLesson() && $orderDetail->getLesson()->getCourse() === $course) {
                        $hasPurchasedLesson = true;
                    }
                }
            }
        }

        // Render the course view with flags to show purchase status
        return $this->render('catalog/course_show.html.twig', [
            'course' => $course,
            'hasPurchasedCourse' => $hasPurchasedCourse,
            'hasPurchasedLesson' => $hasPurchasedLesson,
        ]);
    }

    // Route to display the user's purchased courses and their progression
    #[Route('/profile/courses', name: 'profile_course')]
    public function showCourses(
        OrderRepository $orderRepository,
        ProgressionRepository $progressionRepository,
        CertificationRepository $certificationRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $user = $this->getUser(); // Get the logged-in user

        if (!$user) {
            // If the user is not logged in, throw an access denied exception
            throw $this->createAccessDeniedException('Vous devez être connecté pour voir vos cursus.');
        }

        // Fetch all paid orders for the user
        $orders = $orderRepository->findBy(['user' => $user, 'status' => 'Payée']);

        // Initialize arrays to store courses and their progression
        $courses = [];
        $coursesWithProgression = [];

        // Loop through orders and add courses to the courses array
        foreach ($orders as $order) {
            foreach ($order->getOrderDetails() as $orderDetail) {
                // Determine if the course or a lesson is related to the order
                $course = $orderDetail->getCourse() ?? $orderDetail->getLesson()->getCourse();

                // Avoid duplicate courses
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
                // Get the progression for each lesson for the current user
                $progress = $progressionRepository->findOneBy([
                    'user' => $user,
                    'lesson' => $lesson,
                ]);

                // Add the progression of the lesson to the total progress
                $totalProgress += $progress ? $progress->getPercentage() : 0;
            }

            // Calculate the average progression for the course
            $progression = ($totalLessons > 0) ? ($totalProgress / $totalLessons) : 0;

            // If the progression is 100%, check if the user has a certification
            if ($progression > 99) {
                $certification = $certificationRepository->findOneBy(['user' => $user, 'course' => $course]);

                if (!$certification) {
                    // If no certification exists, create one for the user
                    $certification = new Certification();
                    $certification->setUser($user);
                    $certification->setCourse($course);
                    $certification->setDateObtained(new \DateTime());

                    // Persist the certification to the database
                    $entityManager->persist($certification);
                    $entityManager->flush();
                }
            }

            // Store the course with its progression percentage
            $coursesWithProgression[] = [
                'course' => $course,
                'progression' => $progression,
            ];
        }

        // Render the user's courses overview with progression data
        return $this->render('user/courses_overview.html.twig', [
            'coursesWithProgression' => $coursesWithProgression,
        ]);
    }

    // Route to display the details of a specific course and the progression of each lesson
    #[Route('/course/{id}/details', name: 'course_detail')]
    public function courseDetail(
        Course $course, 
        ProgressionRepository $progressionRepository,
        CertificationRepository $certificationRepository
    ): Response {
        $user = $this->getUser(); // Get the logged-in user

        if (!$user) {
            // If the user is not logged in, throw an access denied exception
            throw $this->createAccessDeniedException('Vous devez être connecté pour voir les détails du cursus.');
        }

        // Fetch all lessons for the given course
        $lessons = $course->getLessons();
        $lessonProgressions = [];

        // Calculate the progression for each lesson
        foreach ($lessons as $lesson) {
            $progression = $progressionRepository->findOneBy([
                'user' => $user,
                'lesson' => $lesson,
            ]);

            // Store the progression for each lesson
            $lessonProgressions[$lesson->getId()] = $progression ? $progression->getPercentage() : 0;
        }

        // Check if the user has obtained a certification for the course
        $certification = $certificationRepository->findOneBy([
            'user' => $user,
            'course' => $course
        ]);

        // Render the course details view with lesson progressions and certification status
        return $this->render('user/course_detail.html.twig', [
            'course' => $course,
            'lessonProgressions' => $lessonProgressions,
            'certification' => $certification,
        ]);
    }
}
