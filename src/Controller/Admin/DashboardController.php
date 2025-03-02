<?php

namespace App\Controller\Admin;

use App\Repository\CategoryRepository;
use App\Repository\CourseRepository;
use App\Repository\LessonRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_ADMIN')] // This ensures that only users with the 'ROLE_ADMIN' role can access this controller's routes.
#[Route('/admin', name: 'admin_')] // Prefixes all routes in this controller with '/admin'.
class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'dashboard')] // This route handles the '/admin/dashboard' URL.
    public function index(CategoryRepository $categoryRepo, CourseRepository $courseRepo, LessonRepository $lessonRepo, UserRepository $userRepo)
    {
        // The 'index' method renders the admin dashboard, passing the counts of categories, courses, lessons, and users to the template.
        return $this->render('admin/dashboard.html.twig', [
            'categoryCount' => $categoryRepo->count([]), // Gets the count of all categories from the database.
            'courseCount' => $courseRepo->count([]), // Gets the count of all courses from the database.
            'lessonCount' => $lessonRepo->count([]), // Gets the count of all lessons from the database.
            'userCount' => $userRepo->count([]), // Gets the count of all users from the database.
        ]);
    }
}
