<?php

namespace App\Controller;

use App\Entity\Certification;
use App\Entity\Course;
use App\Repository\CertificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Dompdf\Dompdf;
use Dompdf\Options;

#[Route('/certification/{id}', name: 'certification', requirements: ['id' => '\d+'])] // Defines the base route for the certification page
#[IsGranted('ROLE_USER')] // Ensures that only users with ROLE_USER can access the routes of this controller
class CertificationController extends AbstractController
{
    // Route to view the certification PDF (accessible via GET request)
    #[Route('/view', name: '_view')]
    public function viewCertification(Course $course, CertificationRepository $certificationRepository): Response
    {
        $user = $this->getUser(); // Get the current logged-in user

        // Check if the user has a certification for the specific course
        $certification = $certificationRepository->findOneBy(['user' => $user, 'course' => $course]);

        // If no certification is found, throw a not found exception
        if (!$certification) {
            throw $this->createNotFoundException('Certification non trouvée.');
        }

        // Initialize DomPDF options and set the default font
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($options);

        // Render the 'certificate.html.twig' template with the certification data
        $html = $this->renderView('certification/certificate.html.twig', [
            'certification' => $certification,
        ]);

        // Load the HTML content and set paper size to A4, landscape orientation
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        // Return the generated PDF as a response to be displayed in the browser
        return new Response(
            $dompdf->output(), // Output the generated PDF
            200, // HTTP status code 200 (OK)
            [
                'Content-Type' => 'application/pdf', // Set content type to PDF
                'Content-Disposition' => 'inline; filename="certification_'.$course->getId().'.pdf"', // Inline display of the PDF with a filename
            ]
        );
    }

    // Route to download the certification PDF (accessible via GET request)
    #[Route('/download', name: '_download')]
    public function downloadCertification(Course $course, CertificationRepository $certificationRepository): Response
    {
        $user = $this->getUser(); // Get the current logged-in user

        // Check if the user has a certification for the specific course
        $certification = $certificationRepository->findOneBy(['user' => $user, 'course' => $course]);

        // If no certification is found, throw a not found exception
        if (!$certification) {
            throw $this->createNotFoundException('Certification non trouvée.');
        }

        // Initialize DomPDF options and set the default font
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($options);

        // Render the 'certificate.html.twig' template with the certification data
        $html = $this->renderView('certification/certificate.html.twig', [
            'certification' => $certification,
        ]);

        // Load the HTML content and set paper size to A4, landscape orientation
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        // Return the PDF as an attachment to be downloaded
        return new Response(
            $dompdf->stream("certification_".$course->getId().".pdf", ["Attachment" => true]), // Stream the PDF with the 'Attachment' option
            200, // HTTP status code 200 (OK)
            ['Content-Type' => 'application/pdf'] // Set content type to PDF
        );
    }
}
