<?php

namespace App\Controller\Payment;

use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Billing; // Make sure to import the Billing entity
use Doctrine\ORM\EntityManagerInterface;

class InvoiceController extends AbstractController
{
    private $entityManager;

    // Constructor: inject the EntityManager to interact with the database
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    // Route to download the invoice as a PDF
    #[Route('/invoice/download/{billingId}', name: 'invoice_download', requirements: ['billingId' => '\d+'])]
    public function downloadInvoice(int $billingId): Response
    {
        // Retrieve the billing information from the database using the provided billing ID
        $billing = $this->entityManager->getRepository(Billing::class)->find($billingId);

        // If the billing information is not found, throw a 404 error
        if (!$billing) {
            throw $this->createNotFoundException('Invoice not found.');
        }

        // Configure Dompdf options (e.g., set default font)
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($options);

        // Generate the HTML content for the invoice
        $html = $this->renderView('invoice/invoice.html.twig', [
            'billing' => $billing, // Pass the Billing object to the template
            'isDownload' => true, // Flag to indicate that it's for download
        ]);

        // Load the HTML content into Dompdf
        $dompdf->loadHtml($html);

        // Set the paper size and orientation for the PDF
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Return the PDF as a downloadable response
        return new Response($dompdf->stream('invoice-' . $billing->getId() . '.pdf', [
            "Attachment" => true, // "true" forces the download
        ]));        
    }

    // Route to display the invoice in the browser (streaming)
    #[Route('/invoice/stream/{billingId}', name: 'invoice_stream', requirements: ['billingId' => '\d+'])]
    public function streamInvoice(int $billingId): Response
    {
        // Retrieve the billing information from the database using the provided billing ID
        $billing = $this->entityManager->getRepository(Billing::class)->find($billingId);

        // If the billing information is not found, throw a 404 error
        if (!$billing) {
            throw $this->createNotFoundException('Invoice not found.');
        }

        // Render the invoice template for display in the browser (without download)
        return $this->render('invoice/invoice.html.twig', [
            'billing' => $billing,
            'isDownload' => false, // Flag to indicate that it's for streaming
        ]);
    }
}
