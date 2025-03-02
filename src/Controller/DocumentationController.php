<?php

namespace App\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DocumentationController extends AbstractController
{
    #[Route('/documentation/generate-pdf', name: 'generate_pdf')]
    public function generatePdf(): Response
    {
        // 1️⃣ Récupérer le chemin des fichiers générés par PHPDoc
        $docPath = __DIR__ . '/../../docs/api/';  // Modifie ce chemin selon ton projet

        // 2️⃣ Récupérer tous les fichiers HTML générés par PHPDoc
        $htmlFiles = glob($docPath . '*.html');

        if (empty($htmlFiles)) {
            throw $this->createNotFoundException('Documentation not found.');
        }

        // 3️⃣ Fusionner le contenu des fichiers HTML
        $fullHtml = '<html><head><meta charset="utf-8"><style>';
        // Ajouter le style CSS si présent
        //$fullHtml .= file_get_contents($docPath . 'css/template.css');
        $fullHtml .= '</style></head><body>';

        // Fusionner le contenu de tous les fichiers HTML
        foreach ($htmlFiles as $file) {
            $content = file_get_contents($file);
            // Supprimer les balises <head> et <body> pour éviter les conflits
            $content = preg_replace('/<head>.*?<\/head>/s', '', $content);
            $content = preg_replace('/<body>|<\/body>/', '', $content);
            $fullHtml .= $content;
        }

        $fullHtml .= '</body></html>';

        // 4️⃣ Initialiser Dompdf avec les options nécessaires
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true); // Pour charger les images et CSS externes

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($fullHtml);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // 5️⃣ Générer le PDF et le retourner pour le téléchargement
        return new Response(
            $dompdf->stream("Documentation_Complète.pdf", ["Attachment" => true]),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="Documentation_Complète.pdf"',
            ]
        );
    }
}
