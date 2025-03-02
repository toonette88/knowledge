<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[IsGranted('ROLE_ADMIN')] // Ensures that only users with ROLE_ADMIN can access this controller
#[Route('admin/category')] // All routes within this controller will start with 'admin/category'
final class CategoryController extends AbstractController
{
    // Route to display a list of all categories (accessible via GET request)
    #[Route(name: 'app_category_index', methods: ['GET'])]
    public function index(CategoryRepository $categoryRepository): Response
    {
        // Render the 'index.html.twig' template, passing all categories retrieved from the repository
        return $this->render('category/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    // Route to create a new category (accessible via GET and POST requests)
    #[Route('/new', name: 'app_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Create a new Category object to bind with the form
        $category = new Category();
        // Create the form based on the CategoryType form class
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request); // Handle form submission

        // If the form is submitted and valid, persist the new category and redirect to the index page
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($category); // Save the category entity to the database
            $entityManager->flush(); // Commit the changes

            // Redirect to the category list page after successful creation
            return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
        }

        // Render the 'new.html.twig' template with the category object and the form
        return $this->render('category/new.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    // Route to display details of a specific category (accessible via GET request)
    #[Route('/{id}', name: 'app_category_show', methods: ['GET'])]
    public function show(Category $category): Response
    {
        // Render the 'show.html.twig' template, passing the selected category
        return $this->render('category/show.html.twig', [
            'category' => $category,
        ]);
    }

    // Route to edit an existing category (accessible via GET and POST requests)
    #[Route('/{id}/edit', name: 'app_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Category $category, EntityManagerInterface $entityManager): Response
    {
        // Create the form with the existing category data
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request); // Handle form submission

        // If the form is submitted and valid, save the updated category and redirect to the index page
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush(); // Commit the changes to the database

            // Redirect to the category list page after successful edit
            return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
        }

        // Render the 'edit.html.twig' template with the category and the form
        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    // Route to delete an existing category (accessible via POST request)
    #[Route('/{id}', name: 'app_category_delete', methods: ['POST'])]
    public function delete(Request $request, Category $category, EntityManagerInterface $entityManager): Response
    {
        // Check if the CSRF token is valid for the current delete operation
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->getPayload()->getString('_token'))) {
            // Remove the category from the database
            $entityManager->remove($category);
            $entityManager->flush(); // Commit the changes
        }

        // Redirect to the category list page after successful deletion
        return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
    }
}
