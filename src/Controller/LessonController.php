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


#[IsGranted('ROLE_ADMIN')]  // Restrict access to admin users only
#[Route('admin/lesson')]  // All routes in this controller are prefixed with 'admin/lesson'
final class LessonController extends AbstractController
{
    // Show the list of lessons
    #[Route('/', name: 'app_lesson_index', methods: ['GET'])]
    public function index(LessonRepository $lessonRepository): Response
    {
        // Renders the 'lesson/index.html.twig' template with a list of all lessons
        return $this->render('lesson/index.html.twig', [
            'lessons' => $lessonRepository->findAll(),
        ]);
    }

    // Create a new lesson
    #[Route('/new', name: 'app_lesson_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CourseRepository $courseRepository, EntityManagerInterface $entityManager): Response
    {
        // Create a new Lesson entity and fetch all courses for the dropdown
        $lesson = new Lesson();
        $courses = $courseRepository->findAll();
    
        // Create and handle the form for creating a new lesson
        $form = $this->createForm(LessonType::class, $lesson, [
            'courses' => $courses,
        ]);
    
        $form->handleRequest($request);
    
        // If form is submitted and valid, persist the lesson entity
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($lesson);
            $entityManager->flush();
    
            // Redirect to the lesson index page after saving
            return $this->redirectToRoute('app_lesson_index');
        }
    
        // Return the 'new' template with the form to create a lesson
        return $this->render('lesson/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Edit an existing lesson
    #[Route('/{id}/edit', name: 'app_lesson_edit', methods: ['GET', 'POST'])]
    public function edit(Lesson $lesson, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Fetch all courses to populate the dropdown in the form
        $courses = $entityManager->getRepository(Course::class)->findAll();
        
        // Create the form for the lesson and handle the request
        $form = $this->createForm(LessonType::class, $lesson, [
            'courses' => $courses,
        ]);
        $form->handleRequest($request);

        // Create forms for the content associated with the lesson
        $contentForms = [];
        foreach ($lesson->getContents() as $content) {
            // Create a form for each content part of the lesson
            $contentForms[] = $this->createForm(LessonContentType::class, $content)->createView();  // Correction here
        }

        // If form is submitted and valid, update the lesson and persist changes
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'The lesson has been successfully updated.');
            return $this->redirectToRoute('app_lesson_index');
        }

        // Return the 'edit' template with the lesson, content forms, and the main form
        return $this->render('lesson/edit.html.twig', [
            'lesson' => $lesson,
            'contents' => $lesson->getContents()->toArray(),
            'form' => $form->createView(),
            'contentForms' => $contentForms,  // Pass the content forms to the template
        ]);
    }

    // Add content to the lesson
    #[Route('/{id}/add-content', name: 'app_lesson_add_content', methods: ['GET', 'POST'])]
    public function addContent(Lesson $lesson, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Create a new LessonContent instance and associate it with the lesson
        $content = new LessonContent();
        $content->setLesson($lesson);

        // Calculate the next part number based on existing content
        $nextPart = count($lesson->getContents()) + 1;
        $content->setPart($nextPart);

        // Create the form for the new content part
        $form = $this->createForm(LessonContentType::class, $content);
        $form->handleRequest($request);

        // If form is submitted and valid, persist the content and redirect to the lesson page
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($content);
            $entityManager->flush();

            return $this->redirectToRoute('app_lesson_show', [
                'id' => $lesson->getId(),
                'part' => $nextPart, // Redirect to the newly added part
            ]);
        }

        // Return the 'add_content' template with the form and lesson
        return $this->render('lesson/add_content.html.twig', [
            'form' => $form->createView(),
            'lesson' => $lesson,
        ]);
    }

    // Show lesson details
    #[Route('/{id}', name: 'app_lesson_show', requirements: ['id' => '\d+'])]
    public function show(LessonRepository $lessonRepository, int $id): Response
    {
        // Fetch the lesson by ID, throw an exception if not found
        $lesson = $lessonRepository->find($id);
    
        if (!$lesson) {
            throw $this->createNotFoundException("The requested lesson does not exist.");
        }
    
        // Return the 'show' template with the lesson and its contents
        return $this->render('lesson/show.html.twig', [
            'lesson' => $lesson,
            'contents' => $lesson->getContents()->toArray(),
        ]);
    }

    // Delete content from the lesson
    #[Route('/{id}/delete/content/{contentId}', name: 'app_content_delete', methods: ['POST'])]
    public function deleteContent(Request $request, Lesson $lesson, EntityManagerInterface $entityManager, int $contentId): Response
    {
        // Find the content by ID, throw an exception if not found
        $content = $entityManager->getRepository(LessonContent::class)->find($contentId);
        
        if (!$content) {
            throw new NotFoundHttpException('The requested content does not exist.');
        }
    
        // Check CSRF token for safety
        if ($this->isCsrfTokenValid('delete' . $content->getId(), $request->request->get('_token'))) {
            // Remove the content from the lesson and persist changes
            $lesson->removeContent($content);
            $entityManager->remove($content);
            $entityManager->flush();
        }
    
        // Redirect to the lesson page after deleting the content
        return $this->redirectToRoute('app_lesson_show', ['id' => $lesson->getId(), 'part' => 1]);
    }

    // Delete a lesson
    #[Route('/delete/{id}', name: 'app_lesson_delete', methods: ['POST'])]
    public function delete(Request $request, Lesson $lesson, EntityManagerInterface $entityManager): Response
    {
        // Check CSRF token and delete the lesson if valid
        if ($this->isCsrfTokenValid('delete' . $lesson->getId(), $request->request->get('_token'))) {
            $entityManager->remove($lesson);
            $entityManager->flush();
        }
    
        // Redirect to the lesson index page after deletion
        return $this->redirectToRoute('app_lesson_index', [], Response::HTTP_SEE_OTHER);
    }
}
