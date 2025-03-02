<?php

// 1️⃣ Définir le dossier contenant la documentation générée par PHPDoc
$docPath = __DIR__ . '/docs/api/'; // Le dossier principal de la documentation
echo $docPath;
// Utilisation de RecursiveDirectoryIterator pour parcourir tous les sous-dossiers
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($docPath));
$htmlFiles = [];

// 2️⃣ Récupérer tous les fichiers HTML générés dans le dossier et sous-dossiers
foreach ($iterator as $file) {
    // Vérifier que le fichier est un fichier HTML
    if ($file->getExtension() == 'html') {
        $htmlFiles[] = $file->getRealPath();  // Correcte utilisation de getRealPath()
    }
}

// 3️⃣ Fusionner tous les fichiers HTML dans une seule variable
$fullHtml = '<html><head><meta charset="utf-8"><style>';
$fullHtml .= file_get_contents($docPath . 'css/template.css');
$fullHtml .= file_get_contents($docPath . 'css/base.css');
$fullHtml .= file_get_contents($docPath . 'css/normalize.css');
$fullHtml .= '</style></head><body>';

foreach ($htmlFiles as $file) {
    $content = file_get_contents($file);
    // Supprimer les balises <head> et <body> pour éviter les doublons
    $content = preg_replace('/<head.*?<\/head>/is', '', $content);
    $content = preg_replace('/<body.*?>|<\/body>/is', '', $content);
    $fullHtml .= $content;
}

$fullHtml .= '</body></html>';

// Sauvegarder ce HTML fusionné dans un fichier temporaire
file_put_contents('merged_documentation.html', $fullHtml);

// 4️⃣ Convertir le fichier HTML fusionné en PDF avec wkhtmltopdf
// Assurez-vous que wkhtmltopdf est installé et disponible dans le PATH
exec('wkhtmltopdf merged_documentation.html documentation_complète.pdf 2>&1', $output, $return_var);
if ($return_var !== 0) {
    echo "Erreur lors de l'exécution de wkhtmltopdf :\n";
    echo implode("\n", $output);
} else {
    echo "Le PDF a été généré avec succès.\n";
}

// 5️⃣ Optionnel : Supprimer le fichier temporaire HTML
unlink('merged_documentation.html');

// Afficher un message de succès
echo "Le PDF a été généré avec succès : documentation_complète.pdf\n";

?>
