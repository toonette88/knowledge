<?php

namespace App\Controller;

use App\Entity\Course;
use App\Form\CourseType;
use App\Repository\CourseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[IsGranted('ROLE_ADMIN')] // Ensures only users with the ROLE_ADMIN can access these routes
#[Route('admin/course')] // All routes in this controller will be prefixed with '/admin/course'
final class CourseController extends AbstractController
{
    // Displays a list of all courses (accessible via GET)
    #[Route(name: 'app_course_index', methods: ['GET'])]
    public function index(CourseRepository $courseRepository): Response
    {
        // Fetches all courses from the repository and passes them to the Twig template
        return $this->render('course/index.html.twig', [
            'courses' => $courseRepository->findAll(),
        ]);
    }

    // Displays the form to create a new course (accessible via GET and POST)
    #[Route('/new', name: 'app_course_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Instantiates a new course object
        $course = new Course();
        // Creates the form based on the CourseType form class
        $form = $this->createForm(CourseType::class, $course);
        // Handles the form submission (POPULATE the $course object with data from the request)
        $form->handleRequest($request);

        // Checks if the form was submitted and is valid
        if ($form->isSubmitted() && $form->isValid()) {
            // Persists the new course into the database
            $entityManager->persist($course);
            $entityManager->flush(); // Saves the course to the database

            // Redirects to the course index page after successful creation
            return $this->redirectToRoute('app_course_index', [], Response::HTTP_SEE_OTHER);
        }

        // If form not submitted or invalid, renders the form again
        return $this->render('course/new.html.twig', [
            'course' => $course,
            'form' => $form,
        ]);
    }

    // Displays a single course's details (accessible via GET)
    #[Route('/{id}', name: 'app_course_show', methods: ['GET'])]
    public function show(Course $course): Response
    {
        // Renders the 'show' template for the selected course
        return $this->render('course/show.html.twig', [
            'course' => $course,
        ]);
    }

    // Displays the form to edit an existing course (accessible via GET and POST)
    #[Route('/{id}/edit', name: 'app_course_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Course $course, EntityManagerInterface $entityManager): Response
    {
        // Creates the form for editing the course
        $form = $this->createForm(CourseType::class, $course);
        // Handles the form submission
        $form->handleRequest($request);

        // Checks if the form was submitted and is valid
        if ($form->isSubmitted() && $form->isValid()) {
            // Flushes changes to the database
            $entityManager->flush();

            // Redirects to the course index page after successful update
            return $this->redirectToRoute('app_course_index', [], Response::HTTP_SEE_OTHER);
        }

        // If form not submitted or invalid, renders the form again
        return $this->render('course/edit.html.twig', [
            'course' => $course,
            'form' => $form,
        ]);
    }

    // Deletes a course (accessible via POST)
    #[Route('/{id}', name: 'app_course_delete', methods: ['POST'])]
    public function delete(Request $request, Course $course, EntityManagerInterface $entityManager): Response
    {
        // Checks if the CSRF token is valid to prevent CSRF attacks
        if ($this->isCsrfTokenValid('delete'.$course->getId(), $request->request->get('_token'))) {
            // Removes the course from the database
            $entityManager->remove($course);
            $entityManager->flush();
        }

        // Redirects to the course index page after successful deletion
        return $this->redirectToRoute('app_course_index', [], Response::HTTP_SEE_OTHER);
    }
}
