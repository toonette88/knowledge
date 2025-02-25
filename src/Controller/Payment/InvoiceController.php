<?php

namespace App\Controller\Payment;

use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Billing; // Assurez-vous d'importer l'entité Billing
use Doctrine\ORM\EntityManagerInterface;

class InvoiceController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/invoice/download/{billingId}', name: 'invoice_download', requirements: ['billingId' => '\d+'])]
    public function downloadInvoice(int $billingId): Response
    {
        // Récupérer l'information de facturation en base de données
        $billing = $this->entityManager->getRepository(Billing::class)->find($billingId);

        if (!$billing) {
            throw $this->createNotFoundException('Facture introuvable.');
        }

        // Configuration de Dompdf
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($options);

        // Génération du HTML de la facture
        $html = $this->renderView('invoice/invoice.html.twig', [
            'billing' => $billing, // Vous passez l'objet Billing ici
            'isDownload' => true,
        ]);

        // Charger le HTML dans Dompdf
        $dompdf->loadHtml($html);

        // Rendre le PDF
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Retourner la réponse avec le PDF en téléchargement
        return new Response($dompdf->stream('facture-' . $billing->getId() . '.pdf', [
            "Attachment" => true, // "true" permet de forcer le téléchargement
        ]));        
    }

    #[Route('/invoice/stream/{billingId}', name: 'invoice_stream', requirements: ['billingId' => '\d+'])]
    public function streamInvoice(int $billingId): Response
    {
        $billing = $this->entityManager->getRepository(Billing::class)->find($billingId);

        if (!$billing){
            throw $this->createNotFoundExeption('Facture introuvable');
        }

        return $this->render('invoice/invoice.html.twig', [
            'billing' =>$billing,
            'isDownload' => false,
        ]);
    }
}