<?php
/*
 * admin/fichier.php — Ouvre le PDF (devis ou facture) d'une demande pour l'admin
 *
 * Le dossier uploads/ est maintenant fermé au public (uploads/.htaccess).
 * Les PDF passent donc par ce script, qui vérifie d'abord que l'admin est connecté
 * (auth.php), puis lit le chemin EN BASE (jamais dans l'URL) et contrôle qu'il
 * reste bien dans uploads/. Même principe que espace-client/telecharger.php.
 */
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../config.php';

$colonnes = ['devis' => 'fichier_devis', 'facture' => 'fichier_facture'];
$doc = $_GET['doc'] ?? '';
$id  = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

if (!is_string($doc) || !isset($colonnes[$doc]) || !$id) {
    http_response_code(404);
    exit('Document introuvable.');
}
$colonne = $colonnes[$doc]; // vient de notre liste : sûr dans la requête

$stmt = $pdo->prepare("SELECT numero_suivi, $colonne AS fichier FROM devis WHERE id = :id");
$stmt->execute([':id' => $id]);
$ligne = $stmt->fetch(PDO::FETCH_ASSOC);

$dossierUploads = realpath(__DIR__ . '/../uploads');
$chemin = ($ligne && $ligne['fichier']) ? realpath(__DIR__ . '/../' . $ligne['fichier']) : false;

if (!$dossierUploads || !$chemin || !is_file($chemin)
    || !str_starts_with($chemin, $dossierUploads . DIRECTORY_SEPARATOR)) {
    http_response_code(404);
    exit('Document introuvable.');
}

// « inline » : le PDF s'ouvre dans le navigateur (l'admin veut le relire, pas forcément le télécharger)
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . $doc . '-' . preg_replace('/[^A-Za-z0-9-]/', '', $ligne['numero_suivi']) . '.pdf"');
header('Content-Length: ' . filesize($chemin));
readfile($chemin);
exit;