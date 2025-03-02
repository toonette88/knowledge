<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Order;
use App\Entity\Lesson;
use App\Entity\Progression;
use App\Repository\OrderRepository;
use App\Repository\ProgressionRepository;
use App\Repository\BillingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')] // Only allow access to users with 'ROLE_USER'
#[Route('/user')] // All routes in this controller will start with '/user'
class ProfileController extends AbstractController
{
    // Displays the user's profile dashboard
    #[Route('/', name: 'user_index')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig');
    }

    // Displays the user's profile information
    #[Route('/informations', name: 'user_informations')]
    public function informations(): Response
    {
        return $this->render('user/user_profile.html.twig');
    }

    // Displays the user's lessons based on their orders
    #[Route('/lessons', name: 'user_lessons')]
    public function userLessons(OrderRepository $orderRepository, ProgressionRepository $progressionRepository): Response
    {
        $user = $this->getUser();
    
        if (!$user) {
            throw $this->createAccessDeniedException('You must be logged in.');
        }
    
        // Get all paid orders for the user
        $orders = $orderRepository->findBy(['user' => $user, 'status' => 'Paid']);
    
        $lessons = [];
        $lessonProgressions = [];
    
        // Retrieve the lessons associated with the orders
        foreach ($orders as $order) {
            foreach ($order->getOrderDetails() as $orderDetail) {
                $lesson = $orderDetail->getLesson();
                $course = $orderDetail->getCourse();
    
                // Add lessons from complete courses
                if ($course) {
                    foreach ($course->getLessons() as $courseLesson) {
                        $lessons[$courseLesson->getId()] = $courseLesson; // Use associative array to avoid duplicates
                    }
                }
    
                // Add individual lessons
                if ($lesson) {
                    $lessons[$lesson->getId()] = $lesson; // Use associative array to avoid duplicates
                }
            }
        }
    
        // Retrieve the progress for each lesson
        $lessonProgressions = $progressionRepository->findBy([
            'user' => $user,
            'lesson' => array_values($lessons), // Get progress for all lessons in one query
        ]);
    
        // Organize the progress by lesson ID
        $progressions = [];
        foreach ($lessonProgressions as $progress) {
            $progressions[$progress->getLesson()->getId()] = $progress->getPercentage();
        }
    
        return $this->render('user/lessons.html.twig', [
            'lessons' => $lessons,
            'lessonProgressions' => $progressions, // Use an associative array for progress
        ]);
    }

    // Route to mark progress on a lesson
    #[Route('/lesson/{id}/next', name: 'user_lesson_next')]
    public function nextPart(Lesson $lesson, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $progression = $em->getRepository(Progression::class)->findOneBy([
            'user' => $user,
            'lesson' => $lesson,
        ]);

        // If the progression doesn't exist, create it
        if (!$progression) {
            $progression = new Progression();
            $progression->setUser($user)
                ->setLesson($lesson)
                ->setChapter(1)  // Start with the first part
                ->setPercentage(0);
            $em->persist($progression);
        }

        // Get the total number of parts
        $totalParts = count($lesson->getContents());

        // If it's the last part, finish directly
        if ($progression->getChapter() == $totalParts) {
            $progression->setPercentage(100);
            $em->flush();
            return $this->redirectToRoute('user_lesson_show', [
                'id' => $lesson->getId(),
                'partId' => $progression->getChapter(),
            ]);
        }

        // Check if we are on a new part and not a previously completed one
        if ($progression->getChapter() < $totalParts) {
            // Move to the next part
            $nextChapter = $progression->getChapter() + 1;

            // Calculate the progress increment: 1 / total number of parts
            $increment = 100 / $totalParts;

            // Increment the progress only if the next part has not already been validated
            if ($nextChapter > $progression->getChapter()) {
                $newPercentage = $progression->getPercentage() + $increment;
                $progression->setChapter($nextChapter);
                $progression->setPercentage(min($newPercentage, 100)); // Ensure the progress doesn't exceed 100%
            }
        }

        $em->flush();

        // Redirect to the next part or finish the lesson
        return $this->redirectToRoute('user_lesson_show', [
            'id' => $lesson->getId(),
            'partId' => $progression->getChapter(),
        ]);
    }

    // Displays a specific part of a lesson
    #[Route('/lesson/{id}/{partId<\d+>}', name: 'user_lesson_show', defaults: ['partId' => 1])]
    public function showLesson(Lesson $lesson, EntityManagerInterface $em, int $partId = 1): Response
    {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('You must be logged in.');
        }

        $this->denyAccessUnlessGranted('view_lesson', $lesson);

        // Get the user's progression
        $progression = $em->getRepository(Progression::class)->findOneBy([
            'user' => $user,
            'lesson' => $lesson,
        ]);

        if (!$progression) {
            // If no progression exists, create a new one
            $progression = new Progression();
            $progression->setUser($user)
                ->setLesson($lesson)
                ->setChapter(1)
                ->setPercentage(0);
            $em->persist($progression);
            $em->flush();
        }

        $contents = $lesson->getContents()->toArray();

        $totalParts = count($contents);
        if ($partId < 1) {
            $partId = 1;
        } elseif ($partId > $totalParts) {
            $partId = $totalParts;
        }

        return $this->render('user/lesson_show.html.twig', [
            'lesson' => $lesson,
            'contents' => $contents,
            'currentPart' => $partId,
            'totalParts' => $totalParts,
            'progression' => $progression,
        ]);
    }

    // Displays the user's invoices
    #[Route('/invoices', name: 'user_invoices')]
    public function invoices(BillingRepository $billingRepository): Response
    {   
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('You must be logged in.');
        }
        return $this->render('user/invoices.html.twig', [
            'billings' => $billingRepository->findAll(),
        ]);
    }

    // Displays the user's certificates
    #[Route('/certificates', name: 'user_certificates')]
    public function certificates(): Response
    {
        // Display the user's certificates
        return $this->render('user/certificates.html.twig');
    }
}
