<?php

namespace App\Controller\Payment;

use App\Entity\Course;
use App\Entity\Lesson;
use App\Entity\Order;
use App\Service\OrderService;
use App\Entity\Billing;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/order')] // Define the base URL for order-related routes
class OrderController extends AbstractController
{
    private OrderService $orderService;
    private EntityManagerInterface $entityManager;

    // Constructor: inject services for order handling and entity management
    public function __construct(OrderService $orderService, EntityManagerInterface $entityManager)
    {
        $this->orderService = $orderService;
        $this->entityManager = $entityManager;
    }

    // Route to create an order
    #[Route('/create', name: 'order_create', methods: ['POST'])]
    #[IsGranted('ROLE_USER')] // Only accessible by authenticated users
    public function create(Request $request)
    {
        $user = $this->getUser(); // Get the current user
        $data = $request->request->all(); // Get the request data
        $courseId = isset($data['courseId']) ? (int) $data['courseId'] : null;
        $lessonId = isset($data['lessonId']) ? (int) $data['lessonId'] : null;

        // Validate that either a course or lesson ID is provided
        if (!$courseId && !$lessonId) {
            return $this->json(['error' => 'Either courseId or lessonId must be provided.'], 400);
        }

        $items = [];

        // Fetch the course or lesson based on the provided ID
        if ($courseId) {
            $course = $this->entityManager->getRepository(Course::class)->find($courseId);
            if ($course) {
                $items[] = $course;
            } else {
                return $this->json(['error' => 'Course not found.'], 400);
            }
        } elseif ($lessonId) {
            $lesson = $this->entityManager->getRepository(Lesson::class)->find($lessonId);
            if ($lesson) {
                $items[] = $lesson;
            } else {
                return $this->json(['error' => 'Lesson not found.'], 400);
            }
        }

        // If no valid items were found, return an error
        if (empty($items)) {
            return $this->json(['error' => 'Invalid course or lesson data.'], 400);
        }

        // Create the order using the OrderService
        $order = $this->orderService->createOrder($user, $items);

        // Redirect to the order show page with the new order ID
        return $this->redirectToRoute('order_show', ['id' => $order->getId()]);
    }

    // Route to display a single order's details
    #[Route('/{id}', name: 'order_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[IsGranted('ROLE_USER')] // Only accessible by authenticated users
    public function show(int $id)
    {
        // Retrieve the order using the provided order ID
        $order = $this->entityManager->getRepository(Order::class)->find($id);

        // Check that the order exists and belongs to the current user
        if (!$order || $order->getUser() !== $this->getUser()) {
            throw $this->createNotFoundException('Order not found or access denied.');
        }

        // Prepare the order details for rendering
        $orderDetails = [];
        foreach ($order->getOrderDetails() as $detail) {
            $orderDetails[] = [
                'course' => $detail->getCourse(),
                'lesson' => $detail->getLesson(),
                'unitPrice' => $detail->getUnitPrice(),
            ];
        }

        // Fetch the billing related to the order
        $billing = $this->entityManager->getRepository(Billing::class)->findOneBy(['order' => $order]);

        // Render the order summary template with the order data
        return $this->render('order/OrderSummary.html.twig', [
            'order' => $order,
            'orderDetails' => $orderDetails,
            'billing' => $billing,
        ]);
    }

    // Route to show a success page after an order is completed
    #[Route('/success', name: 'order_success')]
    public function success(): Response
    {
        // Render the success page after order completion
        return $this->render('order/OrderSuccess.html.twig');
    }
}
