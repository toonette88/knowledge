<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\CourseRepository;
use App\Entity\Course;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Stripe\Checkout\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


class CourseUserController extends AbstractController
{
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
        return $this->render('catalog/course_show.html.twig', [
            'course' => $course,
        ]);
    }
}
