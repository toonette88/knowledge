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

#[IsGranted('ROLE_USER')]
#[Route('/user')]
class ProfileController extends AbstractController
{
    #[Route('/', name: 'user_index')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig');
    }

    #[Route('/informations', name: 'user_informations')]
    public function informations(): Response
    {
        return $this->render('user/user_profile.html.twig');
    }
    #[Route('/lessons', name: 'user_lessons')]
    public function userLessons(OrderRepository $orderRepository, ProgressionRepository $progressionRepository): Response
    {
        $user = $this->getUser();
    
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté.');
        }
    
        // On récupère toutes les commandes payées de l'utilisateur
        $orders = $orderRepository->findBy(['user' => $user, 'status' => 'Payée']);
    
        $lessons = [];
        $lessonProgressions = [];
    
        // Récupérer les leçons associées aux commandes
        foreach ($orders as $order) {
            foreach ($order->getOrderDetails() as $orderDetail) {
                $lesson = $orderDetail->getLesson();
                $course = $orderDetail->getCourse();
    
                // Ajouter les leçons de cursus complets
                if ($course) {
                    foreach ($course->getLessons() as $courseLesson) {
                        $lessons[$courseLesson->getId()] = $courseLesson; // Utilisation d'un tableau associatif pour éviter les doublons
                    }
                }
    
                // Ajouter la leçon individuelle
                if ($lesson) {
                    $lessons[$lesson->getId()] = $lesson; // Utilisation d'un tableau associatif pour éviter les doublons
                }
            }
        }
    
        // Récupérer la progression pour chaque leçon
        $lessonProgressions = $progressionRepository->findBy([
            'user' => $user,
            'lesson' => array_values($lessons), // Récupérer les progressions de toutes les leçons en une seule requête
        ]);
    
        // Organiser les progressions par ID de leçon
        $progressions = [];
        foreach ($lessonProgressions as $progress) {
            $progressions[$progress->getLesson()->getId()] = $progress->getPercentage();
        }
    
        return $this->render('user/lessons.html.twig', [
            'lessons' => $lessons,
            'lessonProgressions' => $progressions, // Utilisation d'un tableau associatif pour les progressions
        ]);
    }
    
    
    // Route pour marquer la progression
    #[Route('/lesson/{id}/next', name: 'user_lesson_next')]
    public function nextPart(Lesson $lesson, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $progression = $em->getRepository(Progression::class)->findOneBy([
            'user' => $user,
            'lesson' => $lesson,
        ]);

        // Si la progression n'existe pas encore, on la crée
        if (!$progression) {
            $progression = new Progression();
            $progression->setUser($user)
                ->setLesson($lesson)
                ->setChapter(1)  // Commencer avec la première partie
                ->setPercentage(0);
            $em->persist($progression);
        }

        // Récupérer le total des parties
        $totalParts = count($lesson->getContents());

        // Si on est sur la dernière partie, terminer directement
        if ($progression->getChapter() == $totalParts) {
            $progression->setPercentage(100);
            $em->flush();
            return $this->redirectToRoute('user_lesson_show', [
                'id' => $lesson->getId(),
                'partId' => $progression->getChapter(),
            ]);
        }

        // Vérifier si on est sur une nouvelle partie et pas sur une partie déjà validée
        if ($progression->getChapter() < $totalParts) {
            // Passer à la partie suivante
            $nextChapter = $progression->getChapter() + 1;

            // Calculer l'incrément de progression : 1 / nombre total de parties
            $increment = 100 / $totalParts;

            // Incrémenter la progression uniquement si la partie suivante n'est pas déjà validée
            if ($nextChapter > $progression->getChapter()) {
                $newPercentage = $progression->getPercentage() + $increment;
                $progression->setChapter($nextChapter);
                $progression->setPercentage(min($newPercentage, 100)); // S'assurer que la progression ne dépasse pas 100%
            }
        }

        $em->flush();

        // Rediriger vers la page de la partie suivante ou terminer la leçon
        return $this->redirectToRoute('user_lesson_show', [
            'id' => $lesson->getId(),
            'partId' => $progression->getChapter(),
        ]);
    }




    #[Route('/lesson/{id}/{partId<\d+>}', name: 'user_lesson_show', defaults: ['partId' => 1])]
    public function showLesson(Lesson $lesson, EntityManagerInterface $em, int $partId = 1): Response
    {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté.');
        }

        $this->denyAccessUnlessGranted('view_lesson', $lesson);

        // Récupérer la progression de l'utilisateur
        $progression = $em->getRepository(Progression::class)->findOneBy([
            'user' => $user,
            'lesson' => $lesson,
        ]);

        if (!$progression) {
            // Si aucune progression n'existe, on en crée une nouvelle
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

       


    #[Route('/invoices', name: 'user_invoices')]
    public function invoices(BillingRepository $billingRepository): Response
    {   
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedExeption('Vous devez être connecté.');
        }
        return $this->render('user/invoices.html.twig', [
            'billings' => $billingRepository->findAll(),
        ]);
    }

    #[Route('/certificates', name: 'user_certificates')]
    public function certificates(): Response
    {
        // Afficher les certifications de l'utilisateur
        return $this->render('user/certificates.html.twig');
    }
}
