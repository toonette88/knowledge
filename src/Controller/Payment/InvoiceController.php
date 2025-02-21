<?php

namespace App\Payment\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;

class InvoiceController extends AbstractController
{
    private $entityManager;

    // Injecter EntityManagerInterface dans le constructeur
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/invoice/download/{orderId}', name: 'invoice_download')]
    public function downloadInvoice(int $orderId): Response
    {
        // Récupérer la commande en base de données
        $order = $this->entityManager->getRepository(Order::class)->find($orderId);

        if (!$order) {
            throw $this->createNotFoundException('Commande introuvable.');
        }

        // Configuration de Dompdf
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($options);

        // Génération du HTML de la facture
        $html = $this->renderView('invoice/invoice.html.twig', [
            'order' => $order,
        ]);

        // Charger le HTML dans Dompdf
        $dompdf->loadHtml($html);

        // Rendre le PDF
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Retourner la réponse avec le PDF en téléchargement
        return new Response($dompdf->stream('facture-commande-' . $order->getId() . '.pdf', [
            "Attachment" => true, // "true" permet de forcer le téléchargement
        ]));
    }
}
