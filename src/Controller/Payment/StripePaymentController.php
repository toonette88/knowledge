<?php

namespace App\Controller\Payment;

use App\Entity\Order;
use App\Entity\Billing;
use App\Enum\OrderStatus;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


class StripePaymentController extends AbstractController{

    #[Route('/payment/create-session/{orderId}', name:'payment_create_session', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function createSession(int $orderId, EntityManagerInterface $entityManager)
    {
        $order = $entityManager->getRepository(Order::class)->find($orderId);

        if (!$order || $order->getUser() !== $this->getUser()) {
            return $this->json(['error' => 'Commande non trouvée ou accès non autorisé.', 404]);
        }

        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        $checkoutSession = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => 'Achat de formations',
                    ],
                    'unit_amount' => (int) ($order->getTotal() * 100),
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $this->generateUrl(
                'payment_success', 
                ['orderId' => $order->getId()], 
                UrlGeneratorInterface::ABSOLUTE_URL
            ),
            'cancel_url' => $this->generateUrl(
                'payment_cancel', 
                ['orderId' => $order->getId()], 
                UrlGeneratorInterface::ABSOLUTE_URL
            ),
            'metadata' => [
                'order_id' => $order->getId(),
            ],
        ]);

        /*dump($this->generateUrl(
            'payment_success', 
            ['orderId' => $order->getId()], 
            UrlGeneratorInterface::ABSOLUTE_URL
        ));
        dump($this->generateUrl(
            'payment_cancel', 
            ['orderId' => $order->getId()], 
            UrlGeneratorInterface::ABSOLUTE_URL
        ));
        die();*/
        
        return $this->redirect($checkoutSession->url);
    }

    #[Route('/payment/success/{orderId}', name:'payment_success')]
    #[IsGranted('ROLE_USER')]
    public function success(int $orderId, EntityManagerInterface $em)
    {
        //Récupérer la commande avec l'ID
        $order = $em->getRepository(Order::class)->find($orderId);

        //Vérifier si la commande existe et si l'utilisateur est bien celui associé à la commande
        if (!$order || $order->getUser() !== $this->getUser()){
            return $this->json(['error' => 'Commande non trouvée ou accès non autorisé.'], 404);
        }

    
        return $this->render('order/OrderSuccess.html.twig', ['order' => $order]);    }

    #[Route('/payment/cancel/{orderId}', name:'payment_cancel')]
    #[IsGranted('ROLE_USER')]
    public function cancel(int $orderId, EntityManagerInterface $em)
    {
        $order = $em->getRepository(Order::class)->find($orderId);

        if (!$order || $order->getUser() !== $this->getUser()){
            return $this->json(['error' => 'Commande non trouvée ou accès non autorisé.', 404]);
        }

        $order->setStatus(OrderStatus::CANCELED);
        $em->persist($order);
        $em->flush();

        $this->addFlash('error', 'Le paiement a été annulé. Votre commande a été annulée.');


        return $this->redirectToRoute('catalog');
    }


}