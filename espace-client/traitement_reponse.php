<?php
/*
 * espace-client/traitement_reponse.php — Le client accepte ou refuse son devis
 *
 * Sécurité :
 *   - client connecté obligatoire, et il ne répond que pour SA demande
 *     (l'id vient de la session, jamais du formulaire) ;
 *   - jeton CSRF : un autre site ne peut pas faire accepter un devis à sa place ;
 *   - pour accepter, la case « j'ai lu et j'accepte » doit être cochée ;
 *   - la requête SQL ne modifie la demande QUE si son statut est encore
 *     « devis_envoye » : impossible de répondre deux fois ou de revenir en arrière,
 *     même en renvoyant le formulaire ou en double-cliquant ;
 *   - on garde la trace : date, heure et adresse IP de la réponse.
 */
require_once __DIR__ . '/../includes/securite.php';
require_once __DIR__ . '/../config.php';

const INACTIVITE_MAX = 30 * 60;

function retour_espace(string $type, string $message): never
{
    $_SESSION['ec_flash'] = ['type' => $type, 'message' => $message];
    header('Location: espace.php');
    exit;
}

// --- Client connecté et actif ? --------------------------------------------
if (empty($_SESSION['espace_client_devis_id'])
    || time() - ($_SESSION['espace_client_derniere_action'] ?? 0) > INACTIVITE_MAX) {
    header('Location: index.php');
    exit;
}
$_SESSION['espace_client_derniere_action'] = time();
$id = (int) $_SESSION['espace_client_devis_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: espace.php');
    exit;
}
if (!csrf_verifier()) {
    retour_espace('erreur', 'La page a expiré. Rechargez-la puis recommencez.');
}

// --- Lecture et vérification de la réponse --------------------------------
$decision = $_POST['decision'] ?? '';
if (!in_array($decision, ['accepte', 'refuse'], true)) {
    retour_espace('erreur', 'Réponse inconnue.');
}

if ($decision === 'accepte' && ($_POST['confirmation'] ?? '') !== '1') {
    retour_espace('erreur', 'Pour accepter, cochez la case confirmant que vous avez lu le devis.');
}

$motif = null;
if ($decision === 'refuse') {
    $motif = trim(lire_texte('motif'));
    $motif = $motif === '' ? null : mb_substr($motif, 0, 500);
}

// --- Enregistrement : seulement si le devis attend encore une réponse -----
$stmt = $pdo->prepare("UPDATE devis
                       SET statut = :decision, date_reponse = NOW(), ip_reponse = :ip, motif_refus = :motif
                       WHERE id = :id AND statut = 'devis_envoye' AND fichier_devis IS NOT NULL");
$stmt->execute([
    ':decision' => $decision,
    ':ip'       => mb_substr($_SERVER['REMOTE_ADDR'] ?? '', 0, 45),
    ':motif'    => $motif,
    ':id'       => $id,
]);

// rowCount() = nombre de lignes modifiées. 0 = déjà répondu (ou pas de devis à valider).
if ($stmt->rowCount() !== 1) {
    retour_espace('erreur', 'Ce devis a déjà reçu une réponse. Pour toute modification, appelez-nous au 07 49 18 76 47.');
}

// --- Prévenir le commercial -------------------------------------------------
$stmt = $pdo->prepare('SELECT numero_suivi, nom, prenom, email, telephone, montant_devis FROM devis WHERE id = :id');
$stmt->execute([':id' => $id]);
$demande = $stmt->fetch(PDO::FETCH_ASSOC);

require_once __DIR__ . '/../mailer.php';
envoyerNotificationReponse($demande, $decision, $motif);   // un échec d'envoi n'annule pas la réponse (elle est en base)

retour_espace('succes', $decision === 'accepte'
    ? 'Merci ! Votre accord est bien enregistré. Nous revenons vers vous très vite pour fixer la date de l\'intervention.'
    : 'Votre réponse est enregistrée. Merci d\'avoir pris le temps de nous répondre.');