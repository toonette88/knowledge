<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    // Route for the homepage ('/' is the root of the site)
    #[Route('/', name: 'app_home')]
    public function home(): Response
    {
        // Renders the 'home/index.html.twig' template for the homepage
        return $this->render('home/index.html.twig');
    }
}
