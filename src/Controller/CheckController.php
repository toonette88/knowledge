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
#[Route('/admin/courses/check-missing-category', name: 'app_courses_check_missing_category')]
public function checkMissingCategory(CourseRepository $courseRepository): Response
{
$coursesWithoutCategory = $courseRepository->findBy(['category' => null]);

if (!empty($coursesWithoutCategory)) {
    dump($coursesWithoutCategory);
    die;
}

return new Response('Tous les cours ont une catégorie.');
}
}