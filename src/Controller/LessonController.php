<?php

namespace App\Controller;

use App\Entity\Lesson;
use App\Entity\Course;
use App\Entity\LessonContent;
use App\Form\LessonType;
use App\Form\LessonContentType;
use App\Repository\LessonRepository;
use App\Repository\CourseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


#[IsGranted('ROLE_ADMIN')]
#[Route('admin/lesson')]
final class LessonController extends AbstractController
{
    // Afficher la liste des leçons
    #[Route('/', name: 'app_lesson_index', methods: ['GET'])]
    public function index(LessonRepository $lessonRepository): Response
    {
        return $this->render('lesson/index.html.twig', [
            'lessons' => $lessonRepository->findAll(),
        ]);
    }

    // Créer une nouvelle leçon
    #[Route('/new', name: 'app_lesson_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CourseRepository $courseRepository, EntityManagerInterface $entityManager): Response
    {
        $lesson = new Lesson();
        $courses = $courseRepository->findAll();
    
        $form = $this->createForm(LessonType::class, $lesson, [
            'courses' => $courses,
        ]);
    
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($lesson);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_lesson_index');
        }
    
        return $this->render('lesson/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    


    #[Route('/{id}/edit', name: 'app_lesson_edit', methods: ['GET', 'POST'])]
    public function edit(Lesson $lesson, Request $request, EntityManagerInterface $entityManager): Response
    {
        $courses = $entityManager->getRepository(Course::class)->findAll();
        // Formulaire principal de la leçon
        $form = $this->createForm(LessonType::class, $lesson, [
            'courses' => $courses,
        ]);
        $form->handleRequest($request);

        // Formulaires pour chaque contenu
        $contentForms = [];
        foreach ($lesson->getContents() as $content) {
            $contentForms[] = $this->createForm(LessonContentType::class, $content)->createView();  // Correction ici
        }

        // Traitement des formulaires
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'La leçon a été modifiée avec succès.');
            return $this->redirectToRoute('app_lesson_index');
        }

        // Retourner les formulaires
        return $this->render('lesson/edit.html.twig', [
            'lesson' => $lesson,
            'contents' => $lesson->getContents()->toArray(),
            'form' => $form->createView(),
            'contentForms' => $contentForms,  // Assurez-vous que les formulaires de contenu sont envoyés sous forme de vues
        ]);
    }


    #[Route('/{id}/add-content', name: 'app_lesson_add_content', methods: ['GET', 'POST'])]
    public function addContent(Lesson $lesson, Request $request, EntityManagerInterface $entityManager): Response
    {
        $content = new LessonContent();
        $content->setLesson($lesson);

        // Calculer le numéro de la prochaine partie
        $nextPart = count($lesson->getContents()) + 1;
        $content->setPart($nextPart);

        // Créer le formulaire pour le contenu
        $form = $this->createForm(LessonContentType::class, $content);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Sauvegarder le contenu
            $entityManager->persist($content);
            $entityManager->flush();

            // Rediriger vers la page de la leçon avec la nouvelle partie
            return $this->redirectToRoute('app_lesson_show', [
                'id' => $lesson->getId(),
                'part' => $nextPart, // Afficher la nouvelle partie
            ]);
        }

        return $this->render('lesson/add_content.html.twig', [
            'form' => $form->createView(),
            'lesson' => $lesson,
        ]);
    }


    #[Route('/{id}', name: 'app_lesson_show', requirements: ['id' => '\d+'])]
    public function show(LessonRepository $lessonRepository, int $id): Response
    {
        $lesson = $lessonRepository->find($id);
    
        if (!$lesson) {
            throw $this->createNotFoundException("La leçon demandée n'existe pas.");
        }
    
        return $this->render('lesson/show.html.twig', [
            'lesson' => $lesson,
            'contents' => $lesson->getContents()->toArray(),
        ]);
    }
    

    // Supprimer le contenu d'une partie spécifique
    #[Route('/{id}/delete/content/{contentId}', name: 'app_content_delete', methods: ['POST'])]
    public function deleteContent(Request $request, Lesson $lesson, EntityManagerInterface $entityManager, int $contentId): Response
    {
        $content = $entityManager->getRepository(LessonContent::class)->find($contentId);
        
        if (!$content) {
            throw new NotFoundHttpException('Le contenu demandé n\'existe pas.');
        }
    
        // Vérification CSRF
        if ($this->isCsrfTokenValid('delete' . $content->getId(), $request->request->get('_token'))) {
            $lesson->removeContent($content);
            $entityManager->remove($content);
            $entityManager->flush();
        }
    
        return $this->redirectToRoute('app_lesson_show', ['id' => $lesson->getId(), 'part' => 1]);
    }

    // Supprimer une leçon
    #[Route('/{id}', name: 'app_lesson_delete', methods: ['POST'])]
    public function delete(Request $request, Lesson $lesson, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $lesson->getId(), $request->request->get('_token'))) {
            $entityManager->remove($lesson);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_lesson_index');
    }

}
