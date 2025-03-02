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

final class CheckController extends AbstractController
{
    // Define the route to check for courses with missing categories (accessible via GET request)
    #[Route('/admin/courses/check-missing-category', name: 'app_courses_check_missing_category')]
    public function checkMissingCategory(CourseRepository $courseRepository): Response
    {
        // Fetch all courses where the category is null (missing category)
        $coursesWithoutCategory = $courseRepository->findBy(['category' => null]);

        // If there are any courses without a category, dump the result and stop execution
        if (!empty($coursesWithoutCategory)) {
            dump($coursesWithoutCategory); // Dump the list of courses without categories
            die; // Stop execution for debugging purposes (will be removed in production)
        }

        // If no courses are missing a category, return a success message
        return new Response('Tous les cours ont une catégorie.');
    }
}
