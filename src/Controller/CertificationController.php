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

#[Route('/certification/{id}', name: 'certification', requirements: ['id' => '\d+'])]
#[IsGranted('ROLE_USER')]
class CertificationController extends AbstractController
{
    #[Route('/view', name: '_view')]
    public function viewCertification(Course $course, CertificationRepository $certificationRepository): Response
    {
        $user = $this->getUser();

        // Vérifier si l'utilisateur a déjà une certification pour ce cursus
        $certification = $certificationRepository->findOneBy(['user' => $user, 'course' => $course]);

        if (!$certification) {
            throw $this->createNotFoundException('Certification non trouvée.');
        }

        // Générer le PDF avec DomPDF
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($options);

        // Rendu du template Twig pour le certificat
        $html = $this->renderView('certification/certificate.html.twig', [
            'certification' => $certification,
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        // Afficher le PDF directement dans le navigateur
        return new Response(
            $dompdf->output(),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="certification_'.$course->getId().'.pdf"',
            ]
        );
    }

    #[Route('/download', name: '_download')]
    public function downloadCertification(Course $course, CertificationRepository $certificationRepository): Response
    {
        $user = $this->getUser();

        // Vérifier si l'utilisateur a déjà une certification pour ce cursus
        $certification = $certificationRepository->findOneBy(['user' => $user, 'course' => $course]);

        if (!$certification) {
            throw $this->createNotFoundException('Certification non trouvée.');
        }

        // Générer le PDF avec DomPDF
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($options);

        // Rendu du template Twig pour le certificat
        $html = $this->renderView('certification/certificate.html.twig', [
            'certification' => $certification,
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        // Télécharger le PDF en pièce jointe
        return new Response(
            $dompdf->stream("certification_".$course->getId().".pdf", ["Attachment" => true]),
            200,
            ['Content-Type' => 'application/pdf']
        );
    }
}

