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

#[Route('/order')]
class OrderController extends AbstractController
{
     private OrderService $orderService;
     private EntityManagerInterface $entityManager;

     public function __construct(OrderService $orderService, EntityManagerInterface $entityManager)
     {
        $this->orderService = $orderService;
        $this->entityManager = $entityManager;
     }

    #[Route('/create', name: 'order_create', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function create(Request $request)
    {
        $user = $this->getUser();
        $data = $request->request->all();        
        $courseId = isset($data['courseId']) ? (int) $data['courseId'] : null;
        $lessonId = isset($data['lessonId']) ? (int) $data['lessonId'] : null;


        if (!$courseId && !$lessonId) {
            return $this->json(['error' => "Données invalides"], 400);
        }

        $items = [];

        if ($courseId) {
            $course = $this->entityManager->getRepository(Course::class)->find($courseId);
            if ($course) {
                $items[] = $course;
            }
        } elseif ($lessonId) {
            $lesson = $this->entityManager->getRepository(Lesson::class)->find($lessonId);
            if ($lesson) {
                $items[] = $lesson;
            }
        }

        if (empty($items)) {
            return $this->json(['error' => 'Aucun élément valide trouvé'], 400);
        }

        $order = $this->orderService->createOrder($user, $items);

        return $this->redirectToRoute('order_show', ['id' => $order->getId()]);
    }


    #[Route('/{id}', name: 'order_show',requirements: ['id' => '\d+'], methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(int $id)
    {
        $order = $this->entityManager->getRepository(Order::class)->find($id);

        // Vérification que la commande appartient bien à l'utilisateur connecté
        if (!$order || $order->getUser() !== $this->getUser()) {
            throw $this->createNotFoundException('Commande introuvable ou accès refusé.');
        }
    
        // Préparation des détails de la commande
        $orderDetails = [];
        foreach ($order->getOrderDetails() as $detail) {
            $orderDetails[] = [
                'course' => $detail->getCourse(),
                'lesson' => $detail->getLesson(),
                'unitPrice' => $detail->getUnitPrice(),
            ];
        }
    
        $billing = $this->entityManager->getRepository(Billing::class)->findOneBy(['order' => $order]);

        // Rendu du template avec les données de la commande
        return $this->render('order/OrderSummary.html.twig', [
            'order' => $order,
            'orderDetails' => $orderDetails,
            'billing' => $billing,
        ]);
    }

    #[Route('/success', name:'order_success')]
    public function success(): Response
    {
        return $this->render('order/OrderSuccess.html.twig');
    }
}    