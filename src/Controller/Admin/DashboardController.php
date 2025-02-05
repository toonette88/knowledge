<?php

namespace App\Controller\Admin;

use App\Repository\CategoryRepository;
use App\Repository\CourseRepository;
use App\Repository\LessonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin', name: 'admin_')]
class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'dashboard')]
    public function index(CategoryRepository $categoryRepo, CourseRepository $courseRepo, LessonRepository $lessonRepo)
    {
        return $this->render('admin/dashboard.html.twig', [
            'categoryCount' => $categoryRepo->count([]),
            'courseCount' => $courseRepo->count([]),
            'lessonCount' => $lessonRepo->count([]),
        ]);
    }
}
