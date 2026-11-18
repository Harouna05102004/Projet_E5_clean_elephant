<?php
/*
 * espace-client/telecharger.php — Envoie le PDF du devis ou de la facture
 *
 * Avant, le PDF était un simple lien (../uploads/devis/xxx.pdf) : n'importe
 * qui connaissant l'adresse pouvait le télécharger, connecté ou non.
 * Ici, on vérifie avant d'envoyer le fichier :
 *   1. que le client est connecté ;
 *   2. que le fichier est bien celui de SA demande (lu en base, jamais dans l'URL) ;
 *   3. que le chemin reste dans le dossier uploads/ (contre « ../../ » pour sortir du dossier).
 */
require_once __DIR__ . '/../includes/securite.php';
require_once __DIR__ . '/../config.php';

if (empty($_SESSION['espace_client_devis_id'])) {
    header('Location: index.php');
    exit;
}

// L'URL ne contient qu'un mot-clé (devis / facture), jamais un nom de fichier
$colonnes = ['devis' => 'fichier_devis', 'facture' => 'fichier_facture'];
$doc = $_GET['doc'] ?? '';
if (!is_string($doc) || !isset($colonnes[$doc])) {
    http_response_code(404);
    exit('Document introuvable.');
}
$colonne = $colonnes[$doc];   // valeur tirée de notre liste : sûre à mettre dans la requête

$stmt = $pdo->prepare("SELECT numero_suivi, $colonne AS fichier FROM devis WHERE id = :id");
$stmt->execute([':id' => $_SESSION['espace_client_devis_id']]);
$ligne = $stmt->fetch(PDO::FETCH_ASSOC);

// realpath() donne le vrai chemin complet ; on vérifie qu'il est DANS le dossier uploads
$dossierUploads = realpath(__DIR__ . '/../uploads');
$chemin = ($ligne && $ligne['fichier']) ? realpath(__DIR__ . '/../' . $ligne['fichier']) : false;

if (!$dossierUploads || !$chemin || !is_file($chemin)
    || !str_starts_with($chemin, $dossierUploads . DIRECTORY_SEPARATOR)) {
    http_response_code(404);
    exit('Document introuvable.');
}

// Envoi du fichier avec un nom propre (ex. devis-CE-1A2B3C4D.pdf)
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $doc . '-' . preg_replace('/[^A-Z0-9-]/', '', $ligne['numero_suivi']) . '.pdf"');
header('Content-Length: ' . filesize($chemin));
header('X-Content-Type-Options: nosniff');   // le navigateur ne doit pas « deviner » un autre type de fichier
header('Cache-Control: private, no-store');  // document personnel : pas de copie en cache
readfile($chemin);
exit;